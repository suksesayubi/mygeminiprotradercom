<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubscriptionPlan;
use App\Models\Subscription;
use App\Models\Payment;
use App\Services\NowPaymentsService;
use App\Services\DuitkuService;
use App\Models\UserNotification;

class BillingController extends Controller
{
    private NowPaymentsService $nowPayments;
    private DuitkuService $duitku;

    public function __construct(NowPaymentsService $nowPayments, DuitkuService $duitku)
    {
        $this->nowPayments = $nowPayments;
        $this->duitku = $duitku;
    }

    public function index()
    {
        $user = auth()->user();
        $activeSubscription = $user->activeSubscription;
        $subscriptionPlans = SubscriptionPlan::where('is_active', true)->get();
        $recentPayments = $user->payments()->latest()->limit(10)->get();
        $duitkuPaymentMethods = $this->duitku->getPopularPaymentMethods();

        return view('billing.index', compact(
            'activeSubscription',
            'subscriptionPlans',
            'recentPayments',
            'duitkuPaymentMethods'
        ));
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'payment_gateway' => 'required|string|in:crypto,rupiah',
            'pay_currency' => 'required_if:payment_gateway,crypto|string|in:btc,eth,ltc,usdt',
            'payment_method' => 'required_if:payment_gateway,rupiah|string',
        ]);

        $user = auth()->user();
        $plan = SubscriptionPlan::findOrFail($request->plan_id);

        // Check if user already has an active subscription
        if ($user->hasActiveSubscription()) {
            return redirect()
                ->route('billing.index')
                ->with('error', 'You already have an active subscription.');
        }

        try {
            if ($request->payment_gateway === 'crypto') {
                return $this->processCryptoPayment($request, $user, $plan);
            } else {
                return $this->processRupiahPayment($request, $user, $plan);
            }

        } catch (\Exception $e) {
            return redirect()
                ->route('billing.index')
                ->with('error', 'Failed to create payment: ' . $e->getMessage());
        }
    }

    private function processCryptoPayment(Request $request, $user, $plan)
    {
        // Create payment with NowPayments
        $paymentResponse = $this->nowPayments->createSubscriptionPayment(
            $user->id,
            $plan->id,
            $plan->price,
            $plan->currency,
            $request->pay_currency
        );

        // Create subscription record
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'status' => 'pending',
            'starts_at' => now(),
            'ends_at' => now()->addMonth($plan->billing_period === 'yearly' ? 12 : 1),
            'amount_paid' => $plan->price,
            'currency' => $plan->currency,
            'payment_method' => 'crypto',
        ]);

        // Create payment record
        Payment::create([
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'payment_gateway' => 'nowpayments',
            'nowpayments_payment_id' => $paymentResponse['payment_id'],
            'nowpayments_order_id' => $paymentResponse['order_id'] ?? null,
            'payment_status' => $paymentResponse['payment_status'],
            'pay_amount' => $paymentResponse['pay_amount'],
            'pay_currency' => $paymentResponse['pay_currency'],
            'price_amount' => $paymentResponse['price_amount'],
            'price_currency' => $paymentResponse['price_currency'],
            'pay_address' => $paymentResponse['pay_address'] ?? null,
            'description' => "Subscription to {$plan->name}",
            'payment_created_at' => now(),
        ]);

        return redirect($paymentResponse['invoice_url'] ?? route('billing.payment', $paymentResponse['payment_id']));
    }

    private function processRupiahPayment(Request $request, $user, $plan)
    {
        // Convert USD to IDR (approximate rate: 1 USD = 15,000 IDR)
        $amountIDR = $plan->price * 15000;

        // Create payment with Duitku
        $paymentResponse = $this->duitku->createSubscriptionPayment(
            $user->id,
            $plan->id,
            $amountIDR,
            'IDR',
            $request->payment_method
        );

        // Create subscription record
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'status' => 'pending',
            'starts_at' => now(),
            'ends_at' => now()->addMonth($plan->billing_period === 'yearly' ? 12 : 1),
            'amount_paid' => $plan->price,
            'currency' => $plan->currency,
            'payment_method' => 'rupiah',
        ]);

        // Create payment record
        Payment::create([
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'payment_gateway' => 'duitku',
            'payment_method' => $request->payment_method,
            'duitku_payment_id' => $paymentResponse['payment_id'],
            'duitku_order_id' => $paymentResponse['order_id'],
            'payment_status' => 'pending',
            'pay_amount' => $amountIDR,
            'pay_currency' => 'IDR',
            'price_amount' => $plan->price,
            'price_currency' => $plan->currency,
            'va_number' => $paymentResponse['va_number'] ?? null,
            'qr_string' => $paymentResponse['qr_string'] ?? null,
            'payment_url' => $paymentResponse['payment_url'],
            'expires_at' => $paymentResponse['expires_at'],
            'description' => "Subscription to {$plan->name}",
            'payment_created_at' => now(),
        ]);

        return redirect()->route('billing.payment-duitku', $paymentResponse['payment_id']);
    }

    public function payment(string $paymentId)
    {
        $payment = Payment::where('nowpayments_payment_id', $paymentId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        try {
            // Get updated payment status from NowPayments
            $paymentStatus = $this->nowPayments->getPaymentStatus($paymentId);
            
            // Update local payment record
            $payment->update([
                'payment_status' => $paymentStatus['payment_status'],
                'actually_paid' => $paymentStatus['actually_paid'] ?? null,
                'outcome_amount' => $paymentStatus['outcome_amount'] ?? null,
                'outcome_currency' => $paymentStatus['outcome_currency'] ?? null,
                'payment_updated_at' => now(),
            ]);

        } catch (\Exception $e) {
            // Log error but continue to show payment page
            \Log::error('Failed to update payment status: ' . $e->getMessage());
        }

        return view('billing.payment', compact('payment'));
    }

    public function paymentDuitku(string $paymentId)
    {
        $payment = Payment::where('duitku_payment_id', $paymentId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        try {
            // Get updated payment status from Duitku
            $paymentStatus = $this->duitku->getPaymentStatus($paymentId);
            
            // Update local payment record
            $payment->update([
                'payment_status' => $paymentStatus['payment_status'],
                'actually_paid' => $paymentStatus['amount_paid'] ?? null,
                'payment_updated_at' => now(),
            ]);

        } catch (\Exception $e) {
            // Log error but continue to show payment page
            \Log::error('Failed to update Duitku payment status: ' . $e->getMessage());
        }

        return view('billing.payment-duitku', compact('payment'));
    }

    public function duitkuCallback(Request $request)
    {
        try {
            $callbackData = $this->duitku->handleCallback($request->all());
            
            $payment = Payment::where('duitku_order_id', $callbackData['order_id'])->first();
            
            if (!$payment) {
                \Log::error('Duitku callback: Payment not found', ['order_id' => $callbackData['order_id']]);
                return response('Payment not found', 404);
            }

            // Update payment status
            $payment->update([
                'payment_status' => $callbackData['payment_status'],
                'actually_paid' => $callbackData['amount_paid'],
                'payment_updated_at' => now(),
            ]);

            // If payment is successful, activate subscription
            if ($callbackData['payment_status'] === 'completed') {
                $subscription = $payment->subscription;
                if ($subscription) {
                    $subscription->update([
                        'status' => 'active',
                        'activated_at' => now(),
                    ]);

                    // Create notification
                    UserNotification::createForUser(
                        $payment->user,
                        'subscription',
                        'Subscription Activated',
                        'Your subscription has been activated successfully!',
                        ['subscription_id' => $subscription->id],
                        'high'
                    );
                }
            }

            return response('OK', 200);

        } catch (\Exception $e) {
            \Log::error('Duitku callback error: ' . $e->getMessage(), $request->all());
            return response('Error', 500);
        }
    }

    public function paymentHistory()
    {
        $payments = auth()->user()->payments()
            ->with('subscription.subscriptionPlan')
            ->latest()
            ->paginate(15);

        return view('billing.history', compact('payments'));
    }

    public function cancelSubscription()
    {
        $user = auth()->user();
        $subscription = $user->activeSubscription;

        if (!$subscription) {
            return redirect()
                ->route('billing.index')
                ->with('error', 'No active subscription found.');
        }

        $subscription->cancel();

        // Create notification
        UserNotification::createForUser(
            $user,
            'subscription',
            'Subscription Cancelled',
            'Your subscription has been cancelled. You will continue to have access until the end of your billing period.',
            ['subscription_id' => $subscription->id],
            'medium'
        );

        return redirect()
            ->route('billing.index')
            ->with('success', 'Subscription cancelled successfully.');
    }

    public function success()
    {
        return view('billing.success');
    }

    public function cancel()
    {
        return view('billing.cancel');
    }

    public function downloadInvoice(Payment $payment)
    {
        if ($payment->user_id !== auth()->id()) {
            abort(403);
        }

        // Generate and return PDF invoice
        // This would typically use a PDF library like DomPDF or similar
        return response()->json(['message' => 'Invoice download feature coming soon']);
    }
}
