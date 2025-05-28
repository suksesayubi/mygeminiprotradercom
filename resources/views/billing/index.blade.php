<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Billing & Subscriptions') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Current Subscription -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Current Subscription</h3>
                            
                            @if($activeSubscription)
                                <div class="border border-green-200 rounded-lg p-4 bg-green-50">
                                    <div class="flex items-center justify-between mb-4">
                                        <div>
                                            <h4 class="text-lg font-semibold text-green-800">{{ $activeSubscription->subscriptionPlan->name }}</h4>
                                            <p class="text-green-600">Active Subscription</p>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-2xl font-bold text-green-800">${{ number_format($activeSubscription->subscriptionPlan->price, 2) }}</div>
                                            <div class="text-sm text-green-600">per {{ $activeSubscription->subscriptionPlan->billing_cycle }}</div>
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <span class="text-gray-500">Started:</span>
                                            <span class="font-medium">{{ $activeSubscription->starts_at->format('M d, Y') }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500">Next Billing:</span>
                                            <span class="font-medium">{{ $activeSubscription->ends_at->format('M d, Y') }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500">Status:</span>
                                            <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                                {{ ucfirst($activeSubscription->status) }}
                                            </span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500">Auto-renew:</span>
                                            <span class="font-medium">{{ $activeSubscription->auto_renew ? 'Yes' : 'No' }}</span>
                                        </div>
                                    </div>

                                    <div class="mt-4 pt-4 border-t border-green-200">
                                        <h5 class="font-medium text-green-800 mb-2">Plan Features:</h5>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                            @foreach($activeSubscription->subscriptionPlan->features as $feature)
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    <span class="text-sm text-green-700">{{ $feature }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="mt-4 flex space-x-3">
                                        <form action="{{ route('billing.cancel-subscription') }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700"
                                                    onclick="return confirm('Are you sure you want to cancel your subscription?')">
                                                Cancel Subscription
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @else
                                <div class="border border-gray-200 rounded-lg p-6 text-center">
                                    <div class="text-gray-400 mb-4">
                                        <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                        </svg>
                                    </div>
                                    <h4 class="text-lg font-semibold text-gray-900 mb-2">No Active Subscription</h4>
                                    <p class="text-gray-600 mb-4">Choose a subscription plan to access premium features and trading bots.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Recent Payments -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Payments</h3>
                            
                            @if($recentPayments->count() > 0)
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gateway</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($recentPayments as $payment)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                        {{ $payment->created_at->format('M d, Y') }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                        @if($payment->pay_currency === 'IDR')
                                                            Rp {{ number_format($payment->pay_amount, 0, ',', '.') }}
                                                        @else
                                                            {{ number_format($payment->pay_amount, 8) }} {{ strtoupper($payment->pay_currency) }}
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                        @if($payment->payment_gateway === 'duitku')
                                                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                                                🇮🇩 Rupiah
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                                                💰 Crypto
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <span class="px-2 py-1 text-xs font-medium rounded-full
                                                            @if($payment->payment_status === 'completed') bg-green-100 text-green-800
                                                            @elseif($payment->payment_status === 'pending') bg-yellow-100 text-yellow-800
                                                            @elseif($payment->payment_status === 'failed') bg-red-100 text-red-800
                                                            @else bg-gray-100 text-gray-800
                                                            @endif">
                                                            {{ ucfirst($payment->payment_status) }}
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                        @if($payment->payment_status === 'completed')
                                                            <a href="{{ route('billing.download-invoice', $payment) }}" 
                                                               class="text-blue-600 hover:text-blue-900">Download Invoice</a>
                                                        @elseif($payment->payment_status === 'pending')
                                                            @if($payment->payment_gateway === 'duitku')
                                                                <a href="{{ route('billing.payment-duitku', $payment->duitku_payment_id) }}" 
                                                                   class="text-green-600 hover:text-green-900">Complete Payment</a>
                                                            @else
                                                                <a href="{{ route('billing.payment', $payment->nowpayments_payment_id) }}" 
                                                                   class="text-blue-600 hover:text-blue-900">Complete Payment</a>
                                                            @endif
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="mt-4">
                                    <a href="{{ route('billing.history') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        View All Payment History →
                                    </a>
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <div class="text-gray-400 mb-4">
                                        <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <p class="text-gray-600">No payment history found.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Subscription Plans -->
                <div class="lg:col-span-1">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Available Plans</h3>
                            
                            <div class="space-y-4">
                                @foreach($subscriptionPlans as $plan)
                                    <div class="border border-gray-200 rounded-lg p-4 
                                        @if($activeSubscription && $activeSubscription->subscription_plan_id === $plan->id) 
                                            border-green-500 bg-green-50 
                                        @endif">
                                        <div class="flex items-center justify-between mb-3">
                                            <h4 class="font-semibold text-gray-900">{{ $plan->name }}</h4>
                                            @if($activeSubscription && $activeSubscription->subscription_plan_id === $plan->id)
                                                <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                                    Current
                                                </span>
                                            @endif
                                        </div>
                                        
                                        <div class="mb-3">
                                            <div class="text-2xl font-bold text-gray-900">${{ number_format($plan->price, 2) }}</div>
                                            <div class="text-sm text-gray-500">per {{ $plan->billing_cycle }}</div>
                                        </div>
                                        
                                        <p class="text-sm text-gray-600 mb-4">{{ $plan->description }}</p>
                                        
                                        @if($plan->features)
                                            <div class="mb-4">
                                                <div class="space-y-1">
                                                    @foreach(array_slice($plan->features, 0, 3) as $feature)
                                                        <div class="flex items-center text-sm">
                                                            <svg class="w-3 h-3 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                            </svg>
                                                            <span class="text-gray-700">{{ $feature }}</span>
                                                        </div>
                                                    @endforeach
                                                    @if(count($plan->features) > 3)
                                                        <div class="text-xs text-gray-500">
                                                            +{{ count($plan->features) - 3 }} more features
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                        
                                        @if(!$activeSubscription || $activeSubscription->subscription_plan_id !== $plan->id)
                                            <form action="{{ route('billing.subscribe') }}" method="POST" id="subscribeForm{{ $plan->id }}">
                                                @csrf
                                                <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                                
                                                <!-- Payment Gateway Selection -->
                                                <div class="mb-3">
                                                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                                                    <div class="space-y-2">
                                                        <label class="flex items-center">
                                                            <input type="radio" name="payment_gateway" value="crypto" class="mr-2" onchange="togglePaymentOptions({{ $plan->id }}, 'crypto')" checked>
                                                            <span class="text-sm">💰 Cryptocurrency (BTC, ETH, USDT, LTC)</span>
                                                        </label>
                                                        <label class="flex items-center">
                                                            <input type="radio" name="payment_gateway" value="rupiah" class="mr-2" onchange="togglePaymentOptions({{ $plan->id }}, 'rupiah')">
                                                            <span class="text-sm">🇮🇩 Rupiah (QRIS, E-Wallet, Bank Transfer)</span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <!-- Crypto Payment Options -->
                                                <div id="cryptoOptions{{ $plan->id }}" class="mb-3">
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Cryptocurrency</label>
                                                    <select name="pay_currency" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                                                        <option value="btc">Bitcoin (BTC)</option>
                                                        <option value="eth">Ethereum (ETH)</option>
                                                        <option value="usdt">Tether (USDT)</option>
                                                        <option value="ltc">Litecoin (LTC)</option>
                                                    </select>
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        Price: ${{ number_format($plan->price, 2) }} USD
                                                    </div>
                                                </div>

                                                <!-- Rupiah Payment Options -->
                                                <div id="rupiahOptions{{ $plan->id }}" class="mb-3 hidden">
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                                                    <select name="payment_method" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
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
                                                            <option value="AG">ATM Bersama</option>
                                                        </optgroup>
                                                    </select>
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        Price: Rp {{ number_format($plan->price * 15000, 0, ',', '.') }} IDR
                                                        <span class="text-gray-400">(~${{ number_format($plan->price, 2) }} USD)</span>
                                                    </div>
                                                </div>

                                                <button type="submit" 
                                                        class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg font-medium hover:bg-blue-700">
                                                    Subscribe Now
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function togglePaymentOptions(planId, gateway) {
            const cryptoOptions = document.getElementById('cryptoOptions' + planId);
            const rupiahOptions = document.getElementById('rupiahOptions' + planId);
            
            if (gateway === 'crypto') {
                cryptoOptions.classList.remove('hidden');
                rupiahOptions.classList.add('hidden');
            } else {
                cryptoOptions.classList.add('hidden');
                rupiahOptions.classList.remove('hidden');
            }
        }
    </script>
    @endpush
</x-app-layout>