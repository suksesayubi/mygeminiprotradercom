<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Expert Signals') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Filter Section -->
                    <div class="mb-6">
                        <form method="GET" class="flex flex-wrap gap-4">
                            <div>
                                <label for="pair" class="block text-sm font-medium text-gray-700">Pair</label>
                                <select name="pair" id="pair" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">All Pairs</option>
                                    @foreach($pairs as $pair)
                                        <option value="{{ $pair }}" {{ request('pair') == $pair ? 'selected' : '' }}>
                                            {{ $pair }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="signal_type" class="block text-sm font-medium text-gray-700">Signal Type</label>
                                <select name="signal_type" id="signal_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">All Types</option>
                                    <option value="BUY" {{ request('signal_type') == 'BUY' ? 'selected' : '' }}>BUY</option>
                                    <option value="SELL" {{ request('signal_type') == 'SELL' ? 'selected' : '' }}>SELL</option>
                                    <option value="HODL" {{ request('signal_type') == 'HODL' ? 'selected' : '' }}>HODL</option>
                                </select>
                            </div>
                            <div class="flex items-end">
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Filter
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Signals List -->
                    @if($signals->count() > 0)
                        <div class="grid gap-6">
                            @foreach($signals as $signal)
                                <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                                    <div class="flex justify-between items-start mb-4">
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900">{{ $signal->pair }}</h3>
                                            <p class="text-sm text-gray-600">{{ $signal->published_at->format('M d, Y H:i') }}</p>
                                        </div>
                                        <span class="px-3 py-1 rounded-full text-sm font-medium
                                            @if($signal->signal_type == 'BUY') bg-green-100 text-green-800
                                            @elseif($signal->signal_type == 'SELL') bg-red-100 text-red-800
                                            @else bg-yellow-100 text-yellow-800 @endif">
                                            {{ $signal->signal_type }}
                                        </span>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                        <div>
                                            <span class="text-sm font-medium text-gray-500">Entry Price</span>
                                            <p class="text-lg font-semibold">${{ number_format($signal->entry_price, 8) }}</p>
                                        </div>
                                        @if($signal->take_profit)
                                        <div>
                                            <span class="text-sm font-medium text-gray-500">Take Profit</span>
                                            <p class="text-lg font-semibold text-green-600">${{ number_format($signal->take_profit, 8) }}</p>
                                        </div>
                                        @endif
                                        @if($signal->stop_loss)
                                        <div>
                                            <span class="text-sm font-medium text-gray-500">Stop Loss</span>
                                            <p class="text-lg font-semibold text-red-600">${{ number_format($signal->stop_loss, 8) }}</p>
                                        </div>
                                        @endif
                                    </div>
                                    
                                    @if($signal->analysis)
                                    <div class="mb-4">
                                        <span class="text-sm font-medium text-gray-500">Analysis</span>
                                        <p class="text-gray-700 mt-1">{{ $signal->analysis }}</p>
                                    </div>
                                    @endif
                                    
                                    <div class="flex justify-between items-center text-sm text-gray-500">
                                        <span>By: {{ $signal->creator->name ?? 'Expert' }}</span>
                                        @if($signal->expires_at)
                                            <span>Expires: {{ $signal->expires_at->format('M d, Y H:i') }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $signals->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="text-gray-500 text-lg mb-4">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No Expert Signals Available</h3>
                            <p class="text-gray-500">Expert signals will appear here when they are published by our trading experts.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>