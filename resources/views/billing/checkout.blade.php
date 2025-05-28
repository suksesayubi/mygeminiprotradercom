@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-width-container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">
                    Subscribe Plan - {{ $plan->name }} (${{ number_format($plan->price, 2) }}/{{ $plan->billing_period === 'yearly' ? 'year' : 'month' }})
                </h2>

                <!-- Step 1: Select Payment Method -->
                <div class="step active" id="step-1">
                    <div class="space-y-4 mb-6">
                        <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="payment_method" value="crypto" class="mr-3" checked>
                            <span class="text-2xl mr-3">🪙</span>
                            <span class="font-medium">Cryptocurrency (NowPayments)</span>
                        </label>
                        <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="payment_method" value="card" class="mr-3">
                            <span class="text-2xl mr-3">💳</span>
                            <span class="font-medium">Credit/Debit Card (Duitku)</span>
                        </label>
                        <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="payment_method" value="qris" class="mr-3">
                            <span class="text-2xl mr-3">🔲</span>
                            <span class="font-medium">QRIS (Duitku)</span>
                        </label>
                    </div>
                    <button id="btn-proceed" class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-blue-700">
                        Proceed to Payment
                    </button>
                </div>

                <!-- Step 2: Payment Details -->
                <div class="step hidden" id="step-2">
                    <!-- Crypto Details -->
                    <div class="step-content hidden" id="detail-crypto">
                        <form id="crypto-form" class="space-y-4">
                            <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                            <input type="hidden" name="payment_method" value="crypto">
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Amount (IDR)</label>
                                <input type="text" value="Rp {{ number_format($amountIDR, 0, ',', '.') }}" readonly 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Select Cryptocurrency</label>
                                <select name="crypto_currency" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                    <option value="USDT">USDT</option>
                                    <option value="BTC">BTC</option>
                                    <option value="ETH">ETH</option>
                                </select>
                            </div>
                            
                            <div id="crypto-payment-info" class="hidden">
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center mb-4">
                                    <p class="font-medium mb-2">Wallet Address:</p>
                                    <p class="font-mono text-sm break-all" id="wallet-address"></p>
                                </div>
                                
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-center mb-4">
                                    <p class="font-medium mb-2">Scan QR Code to Pay</p>
                                    <div id="qr-code" class="flex justify-center">
                                        <div class="w-32 h-32 bg-black"></div>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Time Left</label>
                                    <input type="text" id="crypto-timer" value="15:00" readonly 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50">
                                </div>
                            </div>
                            
                            <button type="submit" id="btn-pay-crypto" class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-blue-700">
                                Pay Now
                            </button>
                        </form>
                    </div>

                    <!-- Card Details -->
                    <div class="step-content hidden" id="detail-card">
                        <form id="card-form" class="space-y-4">
                            <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                            <input type="hidden" name="payment_method" value="card">
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Cardholder Name</label>
                                <input type="text" name="cardholder_name" placeholder="Nama sesuai kartu" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Card Number</label>
                                <input type="text" name="card_number" placeholder="1234 5678 9012 3456" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Expiry Date</label>
                                    <input type="text" name="expiry_date" placeholder="MM/YY" required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">CVC</label>
                                    <input type="text" name="cvc" placeholder="123" required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                </div>
                            </div>
                            
                            <button type="submit" id="btn-pay-card" class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-blue-700">
                                Pay Now
                            </button>
                        </form>
                    </div>

                    <!-- QRIS Details -->
                    <div class="step-content hidden" id="detail-qris">
                        <form id="qris-form" class="space-y-4">
                            <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                            <input type="hidden" name="payment_method" value="qris">
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Amount (IDR)</label>
                                <input type="text" value="Rp {{ number_format($amountIDR, 0, ',', '.') }}" readonly 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50">
                            </div>
                            
                            <div id="qris-payment-info" class="hidden">
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-center mb-4">
                                    <p class="font-medium mb-2">Dynamic QRIS Code</p>
                                    <div id="qris-code" class="flex justify-center">
                                        <div class="w-32 h-32 bg-black"></div>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Time Left</label>
                                    <input type="text" id="qris-timer" value="10:00" readonly 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50">
                                </div>
                            </div>
                            
                            <button type="submit" id="btn-pay-qris" class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-blue-700">
                                Scan & Pay
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Step 3: Confirmation Status -->
                <div class="step hidden" id="step-3">
                    <div id="status-pending" class="bg-blue-50 border border-blue-200 rounded-lg p-6 text-center mb-4">
                        <p class="font-bold text-lg mb-2">Status: Waiting for Payment Confirmation</p>
                        <p class="text-gray-600">Please complete the payment. You will be redirected once confirmed.</p>
                        <div class="mt-4">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
                        </div>
                    </div>
                    
                    <div id="status-success" class="hidden bg-green-50 border border-green-200 rounded-lg p-6 text-center mb-4">
                        <p class="font-bold text-lg mb-2 text-green-800">Status: Payment Successful!</p>
                        <p class="text-green-600">Thank you. Your subscription is now active.</p>
                        <div class="mt-4">
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                Go to Dashboard
                            </a>
                        </div>
                    </div>
                    
                    <div id="status-failed" class="hidden bg-red-50 border border-red-200 rounded-lg p-6 text-center mb-4">
                        <p class="font-bold text-lg mb-2 text-red-800">Status: Payment Failed/Expired</p>
                        <p class="text-red-600">Please try again or choose another payment method.</p>
                    </div>
                    
                    <button id="btn-restart" class="w-full bg-gray-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-gray-700">
                        Back to Start
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const step1 = document.getElementById('step-1');
    const step2 = document.getElementById('step-2');
    const step3 = document.getElementById('step-3');
    const paymentMethodRadios = document.querySelectorAll('input[name="payment_method"]');
    const contents = {
        crypto: document.getElementById('detail-crypto'),
        card: document.getElementById('detail-card'),
        qris: document.getElementById('detail-qris')
    };
    const btnProceed = document.getElementById('btn-proceed');
    const statusPending = document.getElementById('status-pending');
    const statusSuccess = document.getElementById('status-success');
    const statusFailed = document.getElementById('status-failed');
    const btnRestart = document.getElementById('btn-restart');

    // Step 1 -> Step 2
    btnProceed.addEventListener('click', function() {
        const selectedMethod = document.querySelector('input[name="payment_method"]:checked').value;
        
        step1.classList.add('hidden');
        step1.classList.remove('active');
        step2.classList.remove('hidden');
        step2.classList.add('active');
        
        // Hide all content divs
        Object.values(contents).forEach(content => content.classList.add('hidden'));
        
        // Show selected content
        contents[selectedMethod].classList.remove('hidden');
    });

    // Handle form submissions
    document.getElementById('crypto-form').addEventListener('submit', function(e) {
        e.preventDefault();
        processPayment(this);
    });

    document.getElementById('card-form').addEventListener('submit', function(e) {
        e.preventDefault();
        processPayment(this);
    });

    document.getElementById('qris-form').addEventListener('submit', function(e) {
        e.preventDefault();
        processPayment(this);
    });

    function processPayment(form) {
        const formData = new FormData(form);
        const submitButton = form.querySelector('button[type="submit"]');
        
        // Disable submit button
        submitButton.disabled = true;
        submitButton.textContent = 'Processing...';

        fetch('{{ route("billing.process-checkout") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show payment details if needed
                if (data.payment_data) {
                    showPaymentDetails(formData.get('payment_method'), data.payment_data);
                }
                
                // Move to step 3
                step2.classList.add('hidden');
                step2.classList.remove('active');
                step3.classList.remove('hidden');
                step3.classList.add('active');
                
                // Start checking payment status
                checkPaymentStatus(data.payment_data.payment_id);
            } else {
                alert('Error: ' + data.message);
                submitButton.disabled = false;
                submitButton.textContent = form.querySelector('button[type="submit"]').getAttribute('data-original-text') || 'Pay Now';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
            submitButton.disabled = false;
            submitButton.textContent = form.querySelector('button[type="submit"]').getAttribute('data-original-text') || 'Pay Now';
        });
    }

    function showPaymentDetails(method, paymentData) {
        if (method === 'crypto') {
            document.getElementById('wallet-address').textContent = paymentData.wallet_address;
            document.getElementById('crypto-payment-info').classList.remove('hidden');
            startTimer('crypto-timer', paymentData.time_left);
        } else if (method === 'qris') {
            document.getElementById('qris-payment-info').classList.remove('hidden');
            startTimer('qris-timer', paymentData.time_left);
        } else if (method === 'card') {
            // For card payments, redirect to payment gateway
            if (paymentData.payment_url) {
                window.location.href = paymentData.payment_url;
                return;
            }
        }
    }

    function startTimer(elementId, seconds) {
        const timerElement = document.getElementById(elementId);
        let timeLeft = seconds;
        
        const timer = setInterval(function() {
            const minutes = Math.floor(timeLeft / 60);
            const secs = timeLeft % 60;
            timerElement.value = `${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
            
            if (timeLeft <= 0) {
                clearInterval(timer);
                timerElement.value = '00:00';
            }
            timeLeft--;
        }, 1000);
    }

    function checkPaymentStatus(paymentId) {
        // Simulate payment status checking
        setTimeout(() => {
            const success = Math.random() > 0.3; // 70% success rate for demo
            
            statusPending.classList.add('hidden');
            
            if (success) {
                statusSuccess.classList.remove('hidden');
            } else {
                statusFailed.classList.remove('hidden');
            }
        }, 3000);
    }

    // Restart button
    btnRestart.addEventListener('click', function() {
        window.location.reload();
    });

    // Store original button texts
    document.querySelectorAll('button[type="submit"]').forEach(button => {
        button.setAttribute('data-original-text', button.textContent);
    });
});
</script>
@endsection