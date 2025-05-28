<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DuitkuService
{
    private string $merchantCode;
    private string $apiKey;
    private string $baseUrl;
    private bool $isSandbox;

    public function __construct()
    {
        $this->merchantCode = config('services.duitku.merchant_code');
        $this->apiKey = config('services.duitku.api_key');
        $this->isSandbox = config('services.duitku.sandbox', true);
        $this->baseUrl = $this->isSandbox 
            ? 'https://sandbox.duitku.com/webapi/api'
            : 'https://passport.duitku.com/webapi/api';
    }

    /**
     * Create payment for subscription
     */
    public function createSubscriptionPayment(
        int $userId,
        int $planId,
        float $amount,
        string $currency = 'IDR',
        string $paymentMethod = 'SP'
    ): array {
        $orderId = 'SUB-' . $userId . '-' . $planId . '-' . time();
        
        $params = [
            'merchantCode' => $this->merchantCode,
            'paymentAmount' => (int)($amount * 100), // Convert to cents
            'paymentMethod' => $paymentMethod, // SP = Shopee Pay, OV = OVO, DA = DANA, etc.
            'merchantOrderId' => $orderId,
            'productDetails' => 'Gemini Pro Trader Subscription',
            'customerVaName' => auth()->user()->name ?? 'Customer',
            'email' => auth()->user()->email ?? 'customer@example.com',
            'phoneNumber' => auth()->user()->phone ?? '08123456789',
            'itemDetails' => [
                [
                    'name' => 'Subscription Plan',
                    'price' => (int)($amount * 100),
                    'quantity' => 1
                ]
            ],
            'customerDetail' => [
                'firstName' => auth()->user()->name ?? 'Customer',
                'lastName' => '',
                'email' => auth()->user()->email ?? 'customer@example.com',
                'phoneNumber' => auth()->user()->phone ?? '08123456789'
            ],
            'callbackUrl' => route('duitku.callback'),
            'returnUrl' => route('billing.success'),
            'expiryPeriod' => 60 // 60 minutes
        ];

        // Generate signature
        $signature = $this->generateSignature($params);
        $params['signature'] = $signature;

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post($this->baseUrl . '/merchant/createinvoice', $params);

            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['statusCode'] === '00') {
                    return [
                        'success' => true,
                        'payment_id' => $orderId,
                        'order_id' => $orderId,
                        'payment_url' => $data['paymentUrl'],
                        'va_number' => $data['vaNumber'] ?? null,
                        'qr_string' => $data['qrString'] ?? null,
                        'amount' => $amount,
                        'currency' => $currency,
                        'payment_method' => $paymentMethod,
                        'expires_at' => now()->addMinutes(60),
                        'raw_response' => $data
                    ];
                } else {
                    throw new \Exception('Duitku API Error: ' . ($data['statusMessage'] ?? 'Unknown error'));
                }
            } else {
                throw new \Exception('HTTP Error: ' . $response->status() . ' - ' . $response->body());
            }

        } catch (\Exception $e) {
            Log::error('Duitku payment creation failed', [
                'error' => $e->getMessage(),
                'params' => $params
            ]);
            
            throw new \Exception('Failed to create Duitku payment: ' . $e->getMessage());
        }
    }

    /**
     * Get payment status
     */
    public function getPaymentStatus(string $orderId): array
    {
        $params = [
            'merchantCode' => $this->merchantCode,
            'merchantOrderId' => $orderId
        ];

        $signature = $this->generateStatusSignature($params);
        $params['signature'] = $signature;

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post($this->baseUrl . '/merchant/transactionStatus', $params);

            if ($response->successful()) {
                $data = $response->json();
                
                return [
                    'success' => true,
                    'status' => $this->mapDuitkuStatus($data['statusCode'] ?? ''),
                    'payment_status' => $this->mapDuitkuStatus($data['statusCode'] ?? ''),
                    'amount_paid' => isset($data['amount']) ? $data['amount'] / 100 : 0,
                    'currency' => 'IDR',
                    'paid_at' => $data['settlementDate'] ?? null,
                    'raw_response' => $data
                ];
            } else {
                throw new \Exception('HTTP Error: ' . $response->status());
            }

        } catch (\Exception $e) {
            Log::error('Duitku status check failed', [
                'error' => $e->getMessage(),
                'order_id' => $orderId
            ]);
            
            return [
                'success' => false,
                'status' => 'unknown',
                'payment_status' => 'unknown',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get available payment methods
     */
    public function getPaymentMethods(): array
    {
        $params = [
            'merchantcode' => $this->merchantCode,
            'amount' => 10000, // Minimum amount for checking methods
            'datetime' => date('Y-m-d H:i:s')
        ];

        $signature = $this->generateMethodSignature($params);
        $params['signature'] = $signature;

        try {
            $response = Http::timeout(30)
                ->get($this->baseUrl . '/merchant/paymentmethod/getpaymentmethod', $params);

            if ($response->successful()) {
                $data = $response->json();
                return $data['paymentFee'] ?? [];
            }

            return [];

        } catch (\Exception $e) {
            Log::error('Duitku payment methods fetch failed', [
                'error' => $e->getMessage()
            ]);
            
            return [];
        }
    }

    /**
     * Handle callback from Duitku
     */
    public function handleCallback(array $callbackData): array
    {
        // Verify signature
        if (!$this->verifyCallbackSignature($callbackData)) {
            throw new \Exception('Invalid callback signature');
        }

        return [
            'order_id' => $callbackData['merchantOrderId'],
            'payment_status' => $this->mapDuitkuStatus($callbackData['resultCode']),
            'amount_paid' => $callbackData['amount'] / 100,
            'currency' => 'IDR',
            'paid_at' => $callbackData['settlementDate'] ?? now(),
            'reference' => $callbackData['reference'] ?? null,
            'raw_data' => $callbackData
        ];
    }

    /**
     * Generate signature for payment creation
     */
    private function generateSignature(array $params): string
    {
        $signatureString = $this->merchantCode . 
                          $params['merchantOrderId'] . 
                          $params['paymentAmount'] . 
                          $this->apiKey;
        
        return md5($signatureString);
    }

    /**
     * Generate signature for status check
     */
    private function generateStatusSignature(array $params): string
    {
        $signatureString = $this->merchantCode . 
                          $params['merchantOrderId'] . 
                          $this->apiKey;
        
        return md5($signatureString);
    }

    /**
     * Generate signature for payment methods
     */
    private function generateMethodSignature(array $params): string
    {
        $signatureString = $this->merchantCode . 
                          $params['amount'] . 
                          $params['datetime'] . 
                          $this->apiKey;
        
        return md5($signatureString);
    }

    /**
     * Verify callback signature
     */
    private function verifyCallbackSignature(array $data): bool
    {
        $signatureString = $this->merchantCode . 
                          $data['amount'] . 
                          $data['merchantOrderId'] . 
                          $this->apiKey;
        
        $expectedSignature = md5($signatureString);
        
        return hash_equals($expectedSignature, $data['signature'] ?? '');
    }

    /**
     * Map Duitku status codes to our internal status
     */
    private function mapDuitkuStatus(string $statusCode): string
    {
        return match($statusCode) {
            '00' => 'completed',
            '01' => 'pending',
            '02' => 'failed',
            default => 'unknown'
        };
    }

    /**
     * Get popular payment methods for Indonesia
     */
    public function getPopularPaymentMethods(): array
    {
        return [
            [
                'code' => 'SP',
                'name' => 'ShopeePay',
                'type' => 'ewallet',
                'icon' => 'shopee-pay.png'
            ],
            [
                'code' => 'OV',
                'name' => 'OVO',
                'type' => 'ewallet',
                'icon' => 'ovo.png'
            ],
            [
                'code' => 'DA',
                'name' => 'DANA',
                'type' => 'ewallet',
                'icon' => 'dana.png'
            ],
            [
                'code' => 'LK',
                'name' => 'LinkAja',
                'type' => 'ewallet',
                'icon' => 'linkaja.png'
            ],
            [
                'code' => 'I1',
                'name' => 'BCA Virtual Account',
                'type' => 'bank_transfer',
                'icon' => 'bca.png'
            ],
            [
                'code' => 'M2',
                'name' => 'Mandiri Virtual Account',
                'type' => 'bank_transfer',
                'icon' => 'mandiri.png'
            ],
            [
                'code' => 'B1',
                'name' => 'CIMB Niaga Virtual Account',
                'type' => 'bank_transfer',
                'icon' => 'cimb.png'
            ],
            [
                'code' => 'AG',
                'name' => 'ATM Bersama',
                'type' => 'bank_transfer',
                'icon' => 'atm-bersama.png'
            ]
        ];
    }
}