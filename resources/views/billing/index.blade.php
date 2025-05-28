<!-- TEST CHANGE -->
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Billing & Subscriptions') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Current Subscription Status -->
            @if($activeSubscription)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $activeSubscription->subscriptionPlan->name }}</h3>
                            <p class="text-green-600">✅ Active Subscription</p>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-gray-900">${{ number_format($activeSubscription->subscriptionPlan->price, 2) }}</div>
                            <div class="text-sm text-gray-500">per {{ $activeSubscription->subscriptionPlan->billing_cycle }}</div>
                        </div>
                    </div>
                    <div class="mt-4 text-sm text-gray-600">
                        Next billing: {{ $activeSubscription->ends_at->format('M d, Y') }}
                    </div>
                </div>
            @endif

            <!-- Subscription Dashboard -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                @if(!$activeSubscription)
                    <!-- Step 1: Select Plan -->
                    <div class="step active" id="step-1">
                        <h2 class="text-xl font-semibold mb-6">Choose Your Subscription Plan</h2>
                        
                        <div class="space-y-4">
                            @foreach($subscriptionPlans as $plan)
                                <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 cursor-pointer plan-option" 
                                     data-plan-id="{{ $plan->id }}" 
                                     data-plan-name="{{ $plan->name }}" 
                                     data-plan-price="{{ $plan->price }}">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <input type="radio" name="selected_plan" value="{{ $plan->id }}" class="mr-3">
                                            <div>
                                                <h4 class="font-semibold text-gray-900">{{ $plan->name }}</h4>
                                                <p class="text-sm text-gray-600">{{ $plan->description }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-xl font-bold text-gray-900">${{ number_format($plan->price, 2) }}</div>
                                            <div class="text-sm text-gray-500">per {{ $plan->billing_cycle }}</div>
                                        </div>
                                    </div>
                                    
                                    @if($plan->features)
                                        <div class="mt-3 grid grid-cols-2 gap-2">
                                            @foreach(array_slice($plan->features, 0, 4) as $feature)
                                                <div class="flex items-center text-sm text-gray-600">
                                                    <svg class="w-3 h-3 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    {{ $feature }}
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-6">
                            <button id="btn-proceed" class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed" >
                                Proceed to Payment Method
                            </button>
                            <div id="debug-info" class="mt-2 text-sm text-gray-500"></div>
                        </div>
                    </div>

                    <!-- Step 2: Select Payment Method -->
                    <div class="step hidden" id="step-2">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-semibold">Select Payment Method</h2>
                            <button id="btn-back-1" class="text-blue-600 hover:text-blue-800 text-sm">← Back to Plans</button>
                        </div>
                        
                        <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                            <div class="flex justify-between items-center">
                                <span class="font-medium">Selected Plan:</span>
                                <div class="text-right">
                                    <div id="selected-plan-name" class="font-semibold"></div>
                                    <div id="selected-plan-price" class="text-sm text-gray-600"></div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:border-blue-300 payment-method-option">
                                <input type="radio" name="payment_method" value="crypto" class="mr-3">
                                <div class="flex items-center">
                                    <span class="text-2xl mr-3">🪙</span>
                                    <div>
                                        <div class="font-medium">Cryptocurrency (NowPayments)</div>
                                        <div class="text-sm text-gray-600">Bitcoin, Ethereum, USDT, Litecoin</div>
                                    </div>
                                </div>
                            </label>
                            
                            <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:border-blue-300 payment-method-option">
                                <input type="radio" name="payment_method" value="card" class="mr-3">
                                <div class="flex items-center">
                                    <span class="text-2xl mr-3">💳</span>
                                    <div>
                                        <div class="font-medium">Credit/Debit Card (Duitku)</div>
                                        <div class="text-sm text-gray-600">Visa, Mastercard, JCB</div>
                                    </div>
                                </div>
                            </label>
                            
                            <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:border-blue-300 payment-method-option">
                                <input type="radio" name="payment_method" value="qris" class="mr-3">
                                <div class="flex items-center">
                                    <span class="text-2xl mr-3">🔲</span>
                                    <div>
                                        <div class="font-medium">QRIS & E-Wallet (Duitku)</div>
                                        <div class="text-sm text-gray-600">QRIS, OVO, DANA, ShopeePay, GoPay</div>
                                    </div>
                                </div>
                            </label>
                        </div>
                        
                        <div class="mt-6">
                            <button id="btn-proceed-payment" class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed" >
                                Proceed to Payment Details
                            </button>
                        </div>
                    </div>

                    <!-- Step 3: Payment Details -->
                    <div class="step hidden" id="step-3">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-semibold">Payment Details</h2>
                            <button id="btn-back-2" class="text-blue-600 hover:text-blue-800 text-sm">← Back to Payment Method</button>
                        </div>

                        <!-- Crypto Payment Details -->
                        <div class="payment-details hidden" id="crypto-details">
                            <form id="crypto-form" action="{{ route('billing.subscribe') }}" method="POST">
                                @csrf
                                <input type="hidden" name="plan_id" id="crypto-plan-id">
                                <input type="hidden" name="payment_gateway" value="crypto">
                                
                                <div class="space-y-4">
                                    <div class="p-4 bg-gray-50 rounded-lg">
                                        <div class="flex justify-between">
                                            <span>Amount (USD):</span>
                                            <span id="crypto-amount" class="font-semibold"></span>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Cryptocurrency</label>
                                        <select name="pay_currency" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                            <option value="usdt">USDT (Tether)</option>
                                            <option value="btc">BTC (Bitcoin)</option>
                                            <option value="eth">ETH (Ethereum)</option>
                                            <option value="ltc">LTC (Litecoin)</option>
                                        </select>
                                    </div>
                                    
                                    <button type="submit" class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-blue-700">
                                        Create Crypto Payment
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Card Payment Details -->
                        <div class="payment-details hidden" id="card-details">
                            <form id="card-form" action="{{ route('billing.subscribe') }}" method="POST">
                                @csrf
                                <input type="hidden" name="plan_id" id="card-plan-id">
                                <input type="hidden" name="payment_gateway" value="rupiah">
                                <input type="hidden" name="payment_method" value="CC">
                                
                                <div class="space-y-4">
                                    <div class="p-4 bg-gray-50 rounded-lg">
                                        <div class="flex justify-between">
                                            <span>Amount (IDR):</span>
                                            <span id="card-amount" class="font-semibold"></span>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Cardholder Name</label>
                                        <input type="text" name="cardholder_name" placeholder="Nama sesuai kartu" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Card Number</label>
                                        <input type="text" name="card_number" placeholder="1234 5678 9012 3456" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
                                            <input type="text" name="expiry_date" placeholder="MM/YY" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">CVC</label>
                                            <input type="text" name="cvc" placeholder="123" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
                                        </div>
                                    </div>
                                    
                                    <button type="submit" class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-blue-700">
                                        Pay with Card
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- QRIS Payment Details -->
                        <div class="payment-details hidden" id="qris-details">
                            <form id="qris-form" action="{{ route('billing.subscribe') }}" method="POST">
                                @csrf
                                <input type="hidden" name="plan_id" id="qris-plan-id">
                                <input type="hidden" name="payment_gateway" value="rupiah">
                                
                                <div class="space-y-4">
                                    <div class="p-4 bg-gray-50 rounded-lg">
                                        <div class="flex justify-between">
                                            <span>Amount (IDR):</span>
                                            <span id="qris-amount" class="font-semibold"></span>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Payment Method</label>
                                        <select name="payment_method" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                            <optgroup label="QRIS">
                                                <option value="QR">QRIS (All E-Wallets)</option>
                                            </optgroup>
                                            <optgroup label="E-Wallet">
                                                <option value="SP">ShopeePay</option>
                                                <option value="OV">OVO</option>
                                                <option value="DA">DANA</option>
                                                <option value="LK">LinkAja</option>
                                            </optgroup>
                                            <optgroup label="Bank Transfer">
                                                <option value="I1">BCA Virtual Account</option>
                                                <option value="M2">Mandiri Virtual Account</option>
                                                <option value="B1">CIMB Niaga Virtual Account</option>
                                            </optgroup>
                                        </select>
                                    </div>
                                    
                                    <button type="submit" class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-blue-700">
                                        Create Payment
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @else
                    <!-- Already Subscribed -->
                    <div class="text-center py-8">
                        <div class="text-green-500 mb-4">
                            <svg class="w-16 h-16 mx-auto" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">You're All Set!</h3>
                        <p class="text-gray-600 mb-6">You have an active subscription. Enjoy all premium features!</p>
                        
                        <div class="space-y-3">
                            <a href="{{ route('expert-signals.index') }}" class="inline-block bg-blue-600 text-white py-2 px-6 rounded-lg font-medium hover:bg-blue-700">
                                Access Expert Signals
                            </a>
                            <div>
                                <form action="{{ route('billing.cancel-subscription') }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="text-red-600 hover:text-red-800 text-sm"
                                            onclick="return confirm('Are you sure you want to cancel your subscription?')">
                                        Cancel Subscription
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Recent Payments -->
            @if($recentPayments->count() > 0)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Payments</h3>
                    
                    <div class="space-y-3">
                        @foreach($recentPayments->take(3) as $payment)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <div class="font-medium">
                                        @if($payment->pay_currency === 'IDR')
                                            Rp {{ number_format($payment->pay_amount, 0, ',', '.') }}
                                        @else
                                            {{ number_format($payment->pay_amount, 8) }} {{ strtoupper($payment->pay_currency) }}
                                        @endif
                                    </div>
                                    <div class="text-sm text-gray-600">{{ $payment->created_at->format('M d, Y') }}</div>
                                </div>
                                <div class="text-right">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        @if($payment->payment_status === 'completed') bg-green-100 text-green-800
                                        @elseif($payment->payment_status === 'pending') bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($payment->payment_status) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    @if($recentPayments->count() > 3)
                        <div class="mt-4 text-center">
                            <a href="{{ route('billing.history') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                View All Payment History →
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        // Step navigation
        let currentStep = 1;
        let selectedPlan = null;
        let selectedPaymentMethod = null;

        // Plan selection function
        function selectPlan(planElement) {
            console.log('selectPlan called with:', planElement);
            
            // Clear previous selections
            document.querySelectorAll('.plan-option').forEach(opt => {
                opt.classList.remove('border-blue-500', 'bg-blue-50');
                opt.querySelector('input[type="radio"]').checked = false;
            });
            
            // Select current option
            planElement.classList.add('border-blue-500', 'bg-blue-50');
            planElement.querySelector('input[type="radio"]').checked = true;
            
            selectedPlan = {
                id: planElement.dataset.planId,
                name: planElement.dataset.planName,
                price: parseFloat(planElement.dataset.planPrice)
            };
            
            console.log('Selected plan:', selectedPlan);
            
            const proceedBtn = document.getElementById('btn-proceed');
            const debugInfo = document.getElementById('debug-info');
            
            if (proceedBtn) {
                proceedBtn.disabled = false;
                proceedBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                proceedBtn.classList.add('hover:bg-blue-700');
                console.log('Button enabled successfully');
                
                if (debugInfo) {
                    debugInfo.innerHTML = `Selected: ${selectedPlan.name} ($${selectedPlan.price}) - Button enabled!`;
                }
            } else {
                console.error('Proceed button not found!');
                if (debugInfo) {
                    debugInfo.innerHTML = 'Error: Proceed button not found!';
                }
            }
        }

        // Payment method selection function
        function selectPaymentMethod(methodElement) {
            // Clear previous selections
            document.querySelectorAll('.payment-method-option').forEach(opt => {
                opt.classList.remove('border-blue-500', 'bg-blue-50');
                opt.querySelector('input[type="radio"]').checked = false;
            });
            
            // Select current option
            methodElement.classList.add('border-blue-500', 'bg-blue-50');
            methodElement.querySelector('input[type="radio"]').checked = true;
            
            selectedPaymentMethod = methodElement.querySelector('input[type="radio"]').value;
            
            const proceedBtn = document.getElementById('btn-proceed-payment');
            if (proceedBtn) {
                proceedBtn.disabled = false;
                proceedBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                proceedBtn.classList.add('hover:bg-blue-700');
            }
        }

        // Wait for DOM to be fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Debug: Check if plans are available
            const planOptions = document.querySelectorAll('.plan-option');
            const debugInfo = document.getElementById('debug-info');
            const proceedBtn = document.getElementById('btn-proceed');
            
            if (debugInfo) {
                debugInfo.innerHTML = `Found ${planOptions.length} subscription plans. Button status: ${proceedBtn ? (proceedBtn.disabled ? 'disabled' : 'enabled') : 'not found'}`;
            }
            
            // If there are plans available but no plan is selected, enable the button after 1 second
            // This is a fallback in case the JavaScript selection isn't working
            if (planOptions.length > 0 && proceedBtn && proceedBtn.disabled) {
                setTimeout(function() {
                    if (proceedBtn.disabled && !selectedPlan) {
                        // Auto-select the first plan as fallback
                        const firstPlan = planOptions[0];
                        if (firstPlan) {
                            selectPlan(firstPlan);
                            if (debugInfo) {
                                debugInfo.innerHTML += '<br>Auto-selected first plan as fallback.';
                            }
                        }
                    }
                }, 1000);
            }
            
            // Plan selection
            document.querySelectorAll('.plan-option').forEach(option => {
                option.addEventListener('click', function(e) {
                    selectPlan(this);
                });
                
                // Also listen to radio button clicks
                const radio = option.querySelector('input[type="radio"]');
                if (radio) {
                    radio.addEventListener('click', function(e) {
                        e.stopPropagation();
                        selectPlan(option);
                    });
                }
            });

            // Payment method selection
            document.querySelectorAll('.payment-method-option').forEach(option => {
                option.addEventListener('click', function(e) {
                    selectPaymentMethod(this);
                });
                
                // Also listen to radio button clicks
                const radio = option.querySelector('input[type="radio"]');
                if (radio) {
                    radio.addEventListener('click', function(e) {
                        e.stopPropagation();
                        selectPaymentMethod(option);
                    });
                }
            });

            // Step 1 -> Checkout Page
            const btnProceed = document.getElementById('btn-proceed');
            if (btnProceed) {
                btnProceed.addEventListener('click', function() {
                    if (selectedPlan) {
                        // Redirect to checkout page with selected plan
                        window.location.href = `{{ route('billing.checkout') }}?plan_id=${selectedPlan.id}`;
                    } else {
                        alert('Please select a subscription plan first.');
                    }
                });
            }

            // Step 2 -> Step 3
            const btnProceedPayment = document.getElementById('btn-proceed-payment');
            if (btnProceedPayment) {
                btnProceedPayment.addEventListener('click', function() {
                    if (selectedPaymentMethod) {
                        document.getElementById('step-2').classList.add('hidden');
                        document.getElementById('step-3').classList.remove('hidden');
                        
                        // Hide all payment details
                        document.querySelectorAll('.payment-details').forEach(detail => {
                            detail.classList.add('hidden');
                        });
                        
                        // Show selected payment details
                        const detailsId = selectedPaymentMethod + '-details';
                        const detailsElement = document.getElementById(detailsId);
                        if (detailsElement) {
                            detailsElement.classList.remove('hidden');
                        }
                        
                        // Update form data
                        if (selectedPaymentMethod === 'crypto') {
                            const cryptoPlanId = document.getElementById('crypto-plan-id');
                            const cryptoAmount = document.getElementById('crypto-amount');
                            if (cryptoPlanId) cryptoPlanId.value = selectedPlan.id;
                            if (cryptoAmount) cryptoAmount.textContent = `$${selectedPlan.price.toFixed(2)} USD`;
                        } else if (selectedPaymentMethod === 'card') {
                            const cardPlanId = document.getElementById('card-plan-id');
                            const cardAmount = document.getElementById('card-amount');
                            if (cardPlanId) cardPlanId.value = selectedPlan.id;
                            if (cardAmount) cardAmount.textContent = `Rp ${(selectedPlan.price * 15000).toLocaleString('id-ID')} IDR`;
                        } else if (selectedPaymentMethod === 'qris') {
                            const qrisPlanId = document.getElementById('qris-plan-id');
                            const qrisAmount = document.getElementById('qris-amount');
                            if (qrisPlanId) qrisPlanId.value = selectedPlan.id;
                            if (qrisAmount) qrisAmount.textContent = `Rp ${(selectedPlan.price * 15000).toLocaleString('id-ID')} IDR`;
                        }
                        
                        currentStep = 3;
                    }
                });
            }

            // Back buttons
            const btnBack1 = document.getElementById('btn-back-1');
            if (btnBack1) {
                btnBack1.addEventListener('click', function() {
                    document.getElementById('step-2').classList.add('hidden');
                    document.getElementById('step-1').classList.remove('hidden');
                    currentStep = 1;
                });
            }

            const btnBack2 = document.getElementById('btn-back-2');
            if (btnBack2) {
                btnBack2.addEventListener('click', function() {
                    document.getElementById('step-3').classList.add('hidden');
                    document.getElementById('step-2').classList.remove('hidden');
                    currentStep = 2;
                });
            }

            // Format card number input
            document.addEventListener('input', function(e) {
                if (e.target.name === 'card_number') {
                    let value = e.target.value.replace(/\s/g, '').replace(/[^0-9]/gi, '');
                    let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
                    e.target.value = formattedValue;
                }
                
                if (e.target.name === 'expiry_date') {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length >= 2) {
                        value = value.substring(0, 2) + '/' + value.substring(2, 4);
                    }
                    e.target.value = value;
                }
                
                if (e.target.name === 'cvc') {
                    e.target.value = e.target.value.replace(/\D/g, '').substring(0, 4);
                }
            });
        });
    </script>
    @endpush
</x-app-layout>