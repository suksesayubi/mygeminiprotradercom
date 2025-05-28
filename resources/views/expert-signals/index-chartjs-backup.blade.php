<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Expert Signals Dashboard') }}
            </h2>
            <div class="flex space-x-2">
                <button class="px-3 py-1 text-xs bg-gray-200 text-gray-700 rounded">1M</button>
                <button class="px-3 py-1 text-xs bg-gray-200 text-gray-700 rounded">5M</button>
                <button class="px-3 py-1 text-xs bg-gray-200 text-gray-700 rounded">15M</button>
                <button class="px-3 py-1 text-xs bg-blue-500 text-white rounded">1H</button>
                <button class="px-3 py-1 text-xs bg-gray-200 text-gray-700 rounded">4H</button>
                <button class="px-3 py-1 text-xs bg-gray-200 text-gray-700 rounded">1D</button>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Main Performance Chart -->
            <div class="bg-gray-900 rounded-lg p-6 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-white text-lg font-semibold">ETH/USD Performance Chart</h3>
                    <div class="flex space-x-2 text-xs">
                        <span class="text-gray-400">1M</span>
                        <span class="text-gray-400">5M</span>
                        <span class="text-gray-400">15M</span>
                        <span class="text-blue-400 font-semibold">1H</span>
                        <span class="text-gray-400">4H</span>
                        <span class="text-gray-400">1D</span>
                    </div>
                </div>
                <div class="h-64 bg-gray-800 rounded-lg flex items-center justify-center">
                    <canvas id="performanceChart" class="w-full h-full"></canvas>
                </div>
            </div>

            <!-- Secondary Charts Row -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- RSI Chart -->
                <div class="bg-gray-900 rounded-lg p-4">
                    <h4 class="text-white text-sm font-semibold mb-3">RSI - RELATIVE STRENGTH INDEX</h4>
                    <div class="h-32 bg-gray-800 rounded flex items-center justify-center">
                        <canvas id="rsiChart" class="w-full h-full"></canvas>
                    </div>
                </div>

                <!-- MACD Chart -->
                <div class="bg-gray-900 rounded-lg p-4">
                    <h4 class="text-white text-sm font-semibold mb-3">MACD - MOVING AVERAGE CONVERGENCE DIVERGENCE</h4>
                    <div class="h-32 bg-gray-800 rounded flex items-center justify-center">
                        <canvas id="macdChart" class="w-full h-full"></canvas>
                    </div>
                </div>

                <!-- Volume Chart -->
                <div class="bg-gray-900 rounded-lg p-4">
                    <h4 class="text-white text-sm font-semibold mb-3">VOLUME ANALYSIS</h4>
                    <div class="h-32 bg-gray-800 rounded flex items-center justify-center">
                        <canvas id="volumeChart" class="w-full h-full"></canvas>
                    </div>
                </div>
            </div>

            <!-- Signal Details & Analysis Row -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Signal Details & Analysis -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                        <h3 class="text-lg font-semibold mb-4 flex items-center">
                            <span class="w-3 h-3 bg-red-500 rounded-full mr-2"></span>
                            Signal Details & Analysis
                        </h3>
                        
                        <!-- Signal Status -->
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                            <div class="text-center">
                                <div class="text-red-600 font-bold text-2xl">SELL</div>
                                <div class="text-sm text-gray-600">ETH/USD</div>
                                <div class="text-xs text-gray-500 mt-1">ACTIVATED</div>
                                <button class="mt-2 bg-blue-500 text-white px-4 py-1 rounded text-sm">Copy Signal</button>
                            </div>
                        </div>

                        <!-- Performance Stats -->
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Overall Signal Strength</span>
                                <span class="font-semibold">91%</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Latest Trend</span>
                                <span class="text-red-600 font-semibold">BEARISH</span>
                            </div>
                        </div>
                    </div>

                    <!-- Technical Analysis -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h4 class="font-semibold mb-4 flex items-center">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                            Technical Analysis
                        </h4>
                        
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span>Bollinger Bands</span>
                                <span class="font-semibold">0.04</span>
                            </div>
                            <div class="flex justify-between">
                                <span>MACD</span>
                                <span class="font-semibold">0.00</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Moving Average</span>
                                <span class="font-semibold">1.01</span>
                            </div>
                            <div class="flex justify-between">
                                <span>RSI</span>
                                <span class="font-semibold">32.00</span>
                            </div>
                            
                            <div class="mt-4">
                                <div class="flex justify-between mb-1">
                                    <span>Strength</span>
                                    <span class="text-red-600 font-semibold">48.00</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-red-500 h-2 rounded-full" style="width: 48%"></div>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">Bearish</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Trading Details -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h4 class="font-semibold mb-4 flex items-center">
                            <span class="w-2 h-2 bg-orange-500 rounded-full mr-2"></span>
                            Trading Details
                        </h4>
                        
                        <div class="space-y-4">
                            <div>
                                <div class="text-sm text-gray-600">Entry Price:</div>
                                <div class="text-lg font-bold">$3,100.00000</div>
                            </div>
                            
                            <div>
                                <div class="text-sm text-gray-600">Take Profit:</div>
                                <div class="text-lg font-bold text-green-600">$2,900.00000</div>
                            </div>
                            
                            <div>
                                <div class="text-sm text-gray-600">Stop Loss:</div>
                                <div class="text-lg font-bold text-red-600">$3,200.00000</div>
                            </div>
                            
                            <div>
                                <div class="text-sm text-gray-600">Risk Level:</div>
                                <div class="text-lg font-bold">Low</div>
                            </div>
                            
                            <div>
                                <div class="text-sm text-gray-600">Timeframe:</div>
                                <div class="text-lg font-bold">M30</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Market Status & Analysis Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                        <h4 class="font-semibold mb-4 flex items-center">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                            Market Status
                        </h4>
                        
                        <div class="text-center">
                            <div class="text-green-600 font-bold text-lg">OPEN UTC</div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h4 class="font-semibold mb-4 flex items-center">
                            <span class="w-2 h-2 bg-purple-500 rounded-full mr-2"></span>
                            Analysis Summary
                        </h4>
                        
                        <div class="text-sm text-gray-700">
                            <p>Analisis menunjukkan tren bearish yang kuat dengan momentum yang terus menurun. Target profit yang telah ditetapkan memiliki probabilitas tinggi untuk tercapai.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Signals -->
            @if(isset($signals) && $signals->count() > 0)
            <div class="mt-8">
                <h3 class="text-lg font-semibold mb-4">Recent Expert Signals</h3>
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pair</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Signal</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Entry</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">TP</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SL</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($signals->take(5) as $signal)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $signal->pair }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($signal->signal_type == 'BUY') bg-green-100 text-green-800
                                            @elseif($signal->signal_type == 'SELL') bg-red-100 text-red-800
                                            @else bg-yellow-100 text-yellow-800 @endif">
                                            {{ $signal->signal_type }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($signal->entry_price, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">${{ number_format($signal->take_profit ?? 0, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">${{ number_format($signal->stop_loss ?? 0, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Active</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $signal->created_at->format('H:i') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Performance Chart
        const performanceCtx = document.getElementById('performanceChart').getContext('2d');
        new Chart(performanceCtx, {
            type: 'line',
            data: {
                labels: Array.from({length: 50}, (_, i) => i),
                datasets: [{
                    data: [3200, 3180, 3150, 3170, 3140, 3120, 3100, 3080, 3060, 3040, 3020, 3000, 2980, 2960, 2940, 2920, 2900, 2880, 2860, 2840, 2820, 2800, 2780, 2760, 2740, 2720, 2700, 2680, 2660, 2640, 2620, 2600, 2580, 2560, 2540, 2520, 2500, 2480, 2460, 2440, 2420, 2400, 2380, 2360, 2340, 2320, 2300, 2280, 2260, 2240],
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: { display: false },
                    y: { 
                        display: true,
                        grid: { color: '#374151' },
                        ticks: { color: '#9CA3AF' }
                    }
                }
            }
        });

        // RSI Chart
        const rsiCtx = document.getElementById('rsiChart').getContext('2d');
        new Chart(rsiCtx, {
            type: 'line',
            data: {
                labels: Array.from({length: 20}, (_, i) => i),
                datasets: [{
                    data: [65, 62, 58, 55, 52, 48, 45, 42, 38, 35, 32, 30, 28, 26, 24, 22, 20, 18, 16, 14],
                    borderColor: '#F59E0B',
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { display: false },
                    y: { 
                        display: false,
                        min: 0,
                        max: 100
                    }
                }
            }
        });

        // MACD Chart
        const macdCtx = document.getElementById('macdChart').getContext('2d');
        new Chart(macdCtx, {
            type: 'line',
            data: {
                labels: Array.from({length: 20}, (_, i) => i),
                datasets: [
                    {
                        data: [0.5, 0.3, 0.1, -0.1, -0.3, -0.5, -0.7, -0.9, -1.1, -1.3, -1.5, -1.7, -1.9, -2.1, -2.3, -2.5, -2.7, -2.9, -3.1, -3.3],
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        fill: false,
                        tension: 0.4,
                        pointRadius: 0
                    },
                    {
                        data: [0.3, 0.1, -0.1, -0.3, -0.5, -0.7, -0.9, -1.1, -1.3, -1.5, -1.7, -1.9, -2.1, -2.3, -2.5, -2.7, -2.9, -3.1, -3.3, -3.5],
                        borderColor: '#EF4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        fill: false,
                        tension: 0.4,
                        pointRadius: 0
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { display: false },
                    y: { display: false }
                }
            }
        });

        // Volume Chart
        const volumeCtx = document.getElementById('volumeChart').getContext('2d');
        new Chart(volumeCtx, {
            type: 'bar',
            data: {
                labels: Array.from({length: 20}, (_, i) => i),
                datasets: [{
                    data: [100, 120, 80, 150, 90, 110, 130, 95, 140, 85, 125, 105, 160, 75, 135, 115, 145, 70, 155, 100],
                    backgroundColor: '#3B82F6',
                    borderRadius: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { display: false },
                    y: { display: false }
                }
            }
        });
    </script>
    @endpush
</x-app-layout>