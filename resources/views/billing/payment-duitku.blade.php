<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Payment - Duitku') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Payment Status Header -->
                    <div class="text-center mb-8">
                        @if($payment->payment_status === 'completed')
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-green-600 mb-2">Payment Successful!</h3>
                            <p class="text-gray-600">Your subscription has been activated.</p>
                        @elseif($payment->payment_status === 'pending')
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-yellow-100 rounded-full mb-4">
                                <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-yellow-600 mb-2">Waiting for Payment</h3>
                            <p class="text-gray-600">Please complete your payment using the details below.</p>
                        @else
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 rounded-full mb-4">
                                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-red-600 mb-2">Payment {{ ucfirst($payment->payment_status) }}</h3>
                            <p class="text-gray-600">There was an issue with your payment.</p>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Payment Details -->
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Payment Details</h4>
                            
                            <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Order ID:</span>
                                    <span class="font-medium">{{ $payment->duitku_order_id }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Amount:</span>
                                    <span class="font-medium">Rp {{ number_format($payment->pay_amount, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Payment Method:</span>
                                    <span class="font-medium">
                                        @switch($payment->payment_method)
                                            @case('SP') ShopeePay @break
                                            @case('OV') OVO @break
                                            @case('DA') DANA @break
                                            @case('LK') LinkAja @break
                                            @case('I1') BCA Virtual Account @break
                                            @case('M2') Mandiri Virtual Account @break
                                            @case('B1') CIMB Niaga Virtual Account @break
                                            @case('AG') ATM Bersama @break
                                            @default {{ $payment->payment_method }}
                                        @endswitch
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Status:</span>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        @if($payment->payment_status === 'completed') bg-green-100 text-green-800
                                        @elseif($payment->payment_status === 'pending') bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($payment->payment_status) }}
                                    </span>
                                </div>
                                @if($payment->expires_at)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Expires:</span>
                                        <span class="font-medium">{{ $payment->expires_at->format('M d, Y H:i') }}</span>
                                    </div>
                                @endif
                            </div>

                            @if($payment->payment_status === 'pending')
                                <div class="mt-4">
                                    <button onclick="checkPaymentStatus()" 
                                            class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg font-medium hover:bg-blue-700">
                                        Check Payment Status
                                    </button>
                                </div>
                            @endif
                        </div>

                        <!-- Payment Instructions -->
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Payment Instructions</h4>
                            
                            @if($payment->payment_status === 'pending')
                                @if($payment->va_number)
                                    <!-- Virtual Account -->
                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                        <h5 class="font-semibold text-blue-800 mb-2">Virtual Account Number</h5>
                                        <div class="bg-white border border-blue-300 rounded p-3 mb-3">
                                            <div class="text-center">
                                                <div class="text-2xl font-mono font-bold text-blue-800">{{ $payment->va_number }}</div>
                                                <button onclick="copyToClipboard('{{ $payment->va_number }}')" 
                                                        class="mt-2 text-sm text-blue-600 hover:text-blue-800">
                                                    📋 Copy Number
                                                </button>
                                            </div>
                                        </div>
                                        <div class="text-sm text-blue-700">
                                            <p class="mb-2"><strong>How to pay:</strong></p>
                                            <ol class="list-decimal list-inside space-y-1">
                                                <li>Open your mobile banking or ATM</li>
                                                <li>Select "Transfer" or "Virtual Account"</li>
                                                <li>Enter the virtual account number above</li>
                                                <li>Enter the exact amount: Rp {{ number_format($payment->pay_amount, 0, ',', '.') }}</li>
                                                <li>Confirm and complete the transaction</li>
                                            </ol>
                                        </div>
                                    </div>
                                @elseif($payment->qr_string)
                                    <!-- QR Code -->
                                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                        <h5 class="font-semibold text-green-800 mb-2">QR Code Payment</h5>
                                        <div class="text-center mb-3">
                                            <div id="qrcode" class="inline-block"></div>
                                        </div>
                                        <div class="text-sm text-green-700">
                                            <p class="mb-2"><strong>How to pay:</strong></p>
                                            <ol class="list-decimal list-inside space-y-1">
                                                <li>Open your e-wallet app (ShopeePay, OVO, DANA, etc.)</li>
                                                <li>Scan the QR code above</li>
                                                <li>Confirm the amount: Rp {{ number_format($payment->pay_amount, 0, ',', '.') }}</li>
                                                <li>Complete the payment</li>
                                            </ol>
                                        </div>
                                    </div>
                                @elseif($payment->payment_url)
                                    <!-- Direct Payment URL -->
                                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                                        <h5 class="font-semibold text-purple-800 mb-2">Complete Payment</h5>
                                        <p class="text-sm text-purple-700 mb-3">
                                            Click the button below to complete your payment through the secure payment gateway.
                                        </p>
                                        <a href="{{ $payment->payment_url }}" 
                                           target="_blank"
                                           class="inline-block w-full bg-purple-600 text-white py-2 px-4 rounded-lg font-medium hover:bg-purple-700 text-center">
                                            Complete Payment
                                        </a>
                                    </div>
                                @endif

                                <!-- Payment Timer -->
                                @if($payment->expires_at)
                                    <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                        <h5 class="font-semibold text-yellow-800 mb-2">⏰ Payment Timer</h5>
                                        <div class="text-center">
                                            <div id="countdown" class="text-2xl font-bold text-yellow-800"></div>
                                            <div class="text-sm text-yellow-600">Time remaining to complete payment</div>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-8 flex justify-center space-x-4">
                        <a href="{{ route('billing.index') }}" 
                           class="px-6 py-2 bg-gray-600 text-white rounded-lg font-medium hover:bg-gray-700">
                            Back to Billing
                        </a>
                        @if($payment->payment_status === 'completed')
                            <a href="{{ route('dashboard') }}" 
                               class="px-6 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700">
                                Go to Dashboard
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
    <script>
        // Generate QR Code if QR string exists
        @if($payment->qr_string)
            QRCode.toCanvas(document.getElementById('qrcode'), '{{ $payment->qr_string }}', {
                width: 200,
                height: 200,
                margin: 2
            });
        @endif

        // Countdown timer
        @if($payment->expires_at && $payment->payment_status === 'pending')
            const expiresAt = new Date('{{ $payment->expires_at->toISOString() }}');
            
            function updateCountdown() {
                const now = new Date();
                const timeLeft = expiresAt - now;
                
                if (timeLeft <= 0) {
                    document.getElementById('countdown').innerHTML = 'EXPIRED';
                    return;
                }
                
                const hours = Math.floor(timeLeft / (1000 * 60 * 60));
                const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);
                
                document.getElementById('countdown').innerHTML = 
                    `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            }
            
            updateCountdown();
            setInterval(updateCountdown, 1000);
        @endif

        // Copy to clipboard function
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                alert('Virtual Account number copied to clipboard!');
            });
        }

        // Check payment status
        function checkPaymentStatus() {
            window.location.reload();
        }

        // Auto refresh every 30 seconds for pending payments
        @if($payment->payment_status === 'pending')
            setInterval(function() {
                window.location.reload();
            }, 30000);
        @endif
    </script>
    @endpush
</x-app-layout>