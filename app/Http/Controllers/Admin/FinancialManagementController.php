<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinancialManagementController extends Controller
{
    public function index()
    {
        $stats = [
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
            'monthly_revenue' => Payment::where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->sum('amount'),
            'active_subscriptions' => Subscription::where('status', 'active')->count(),
            'total_users' => User::count(),
        ];

        $recentPayments = Payment::with('user')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.financial.index', compact('stats', 'recentPayments'));
    }

    public function plans()
    {
        $plans = Plan::orderBy('sort_order')->get();
        return view('admin.financial.plans', compact('plans'));
    }

    public function createPlan()
    {
        return view('admin.financial.create-plan');
    }

    public function storePlan(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'billing_period' => 'required|in:monthly,yearly,lifetime',
            'features' => 'nullable|array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        Plan::create($validated);

        return redirect()->route('admin.financial.plans')
            ->with('success', 'Plan created successfully.');
    }

    public function editPlan(Plan $plan)
    {
        return view('admin.financial.edit-plan', compact('plan'));
    }

    public function updatePlan(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'billing_period' => 'required|in:monthly,yearly,lifetime',
            'features' => 'nullable|array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $plan->update($validated);

        return redirect()->route('admin.financial.plans')
            ->with('success', 'Plan updated successfully.');
    }

    public function transactions()
    {
        $transactions = Payment::with('user')
            ->latest()
            ->paginate(20);

        return view('admin.financial.transactions', compact('transactions'));
    }

    public function subscriptions()
    {
        $subscriptions = Subscription::with(['user', 'plan'])
            ->latest()
            ->paginate(20);

        return view('admin.financial.subscriptions', compact('subscriptions'));
    }
}
