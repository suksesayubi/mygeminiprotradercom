<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Expert Signals Dashboard') }}
            </h2>
            <div class="flex space-x-2">
                <button class="px-3 py-1 text-xs bg-gray-200 text-gray-700 rounded hover:bg-gray-300" onclick="changeTimeframe('1')">1M</button>
                <button class="px-3 py-1 text-xs bg-gray-200 text-gray-700 rounded hover:bg-gray-300" onclick="changeTimeframe('5')">5M</button>
                <button class="px-3 py-1 text-xs bg-gray-200 text-gray-700 rounded hover:bg-gray-300" onclick="changeTimeframe('15')">15M</button>
                <button class="px-3 py-1 text-xs bg-blue-500 text-white rounded hover:bg-blue-600" onclick="changeTimeframe('60')">1H</button>
                <button class="px-3 py-1 text-xs bg-gray-200 text-gray-700 rounded hover:bg-gray-300" onclick="changeTimeframe('240')">4H</button>
                <button class="px-3 py-1 text-xs bg-gray-200 text-gray-700 rounded hover:bg-gray-300" onclick="changeTimeframe('1D')">1D</button>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Main Layout: Large Chart Left + 3 Widgets Right -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <!-- Left Side: Main TradingView Chart (2/3 width) -->
                <div class="lg:col-span-2">
                    <div class="bg-gray-900 rounded-lg p-6 border border-gray-700 h-full">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-white text-lg font-semibold">BTC/USDT Live Chart</h3>
                            <div class="flex items-center space-x-3">
                                <span class="px-3 py-1 bg-green-600 text-white rounded text-xs font-semibold">LIVE</span>
                                <span class="text-xs text-gray-400">Powered by TradingView</span>
                            </div>
                        </div>
                        <div class="rounded-lg overflow-hidden bg-gray-800">
                            <!-- TradingView Advanced Chart Widget -->
                            <div class="tradingview-widget-container" style="height:600px;width:100%">
                                <div class="tradingview-widget-container__widget" style="height:calc(100% - 32px);width:100%"></div>
                                <div class="tradingview-widget-copyright" style="font-size: 13px; color: #848E9C; line-height: 14px; text-align: center; vertical-align: middle; font-family: -apple-system, BlinkMacSystemFont, 'Trebuchet MS', Roboto, Ubuntu, sans-serif;">
                                    <a href="https://www.tradingview.com/" rel="noopener nofollow" target="_blank">
                                        <span style="color: #848E9C;">Track all markets on TradingView</span>
                                    </a>
                                </div>
                                <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-advanced-chart.js" async>
                                {
                                  "autosize": true,
                                  "symbol": "BINANCE:BTCUSDT",
                                  "interval": "60",
                                  "timezone": "Etc/UTC",
                                  "theme": "dark",
                                  "style": "1",
                                  "locale": "en",
                                  "enable_publishing": false,
                                  "backgroundColor": "rgba(19, 23, 34, 1)",
                                  "gridColor": "rgba(42, 46, 57, 0.5)",
                                  "hide_top_toolbar": false,
                                  "hide_legend": false,
                                  "save_image": false,
                                  "container_id": "tradingview_chart"
                                }
                                </script>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Side: 3 Stacked Widgets (1/3 width) -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Technical Analysis Summary -->
                    <div class="bg-gray-900 rounded-lg p-4 border border-gray-700">
                        <h4 class="text-white text-sm font-semibold mb-3">TECHNICAL ANALYSIS</h4>
                        <div class="rounded-lg overflow-hidden">
                            <div class="tradingview-widget-container" style="height:180px;width:100%">
                                <div class="tradingview-widget-container__widget" style="height:calc(100% - 32px);width:100%"></div>
                                <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-technical-analysis.js" async>
                                {
                                  "interval": "1h",
                                  "width": "100%",
                                  "isTransparent": false,
                                  "height": "180",
                                  "symbol": "BINANCE:BTCUSDT",
                                  "showIntervalTabs": true,
                                  "displayMode": "single",
                                  "locale": "en",
                                  "colorTheme": "dark"
                                }
                                </script>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Symbol Overview -->
                    <div class="bg-gray-900 rounded-lg p-4 border border-gray-700">
                        <h4 class="text-white text-sm font-semibold mb-3">SYMBOL OVERVIEW</h4>
                        <div class="rounded-lg overflow-hidden">
                            <div class="tradingview-widget-container" style="height:180px;width:100%">
                                <div class="tradingview-widget-container__widget" style="height:calc(100% - 32px);width:100%"></div>
                                <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-symbol-overview.js" async>
                                {
                                  "symbols": [
                                    [
                                      "BINANCE:BTCUSDT|1h"
                                    ]
                                  ],
                                  "chartOnly": false,
                                  "width": "100%",
                                  "height": "180",
                                  "locale": "en",
                                  "colorTheme": "dark",
                                  "autosize": false,
                                  "showVolume": false,
                                  "showMA": false,
                                  "hideDateRanges": false,
                                  "hideMarketStatus": false,
                                  "hideSymbolLogo": false,
                                  "scalePosition": "right",
                                  "scaleMode": "Normal",
                                  "fontFamily": "-apple-system, BlinkMacSystemFont, Trebuchet MS, Roboto, Ubuntu, sans-serif",
                                  "fontSize": "10",
                                  "noTimeScale": false,
                                  "valuesTracking": "1",
                                  "changeMode": "price-and-percent",
                                  "chartType": "area",
                                  "maLineColor": "#2962FF",
                                  "maLineWidth": 1,
                                  "maLength": 9,
                                  "backgroundColor": "rgba(19, 23, 34, 1)",
                                  "lineWidth": 2,
                                  "lineType": 0,
                                  "dateRanges": [
                                    "1d|1",
                                    "1m|30",
                                    "3m|60",
                                    "12m|1D",
                                    "60m|1W",
                                    "all|1M"
                                  ]
                                }
                                </script>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Mini Chart -->
                    <div class="bg-gray-900 rounded-lg p-4 border border-gray-700">
                        <h4 class="text-white text-sm font-semibold mb-3">MINI CHART</h4>
                        <div class="rounded-lg overflow-hidden">
                            <div class="tradingview-widget-container" style="height:180px;width:100%">
                                <div class="tradingview-widget-container__widget" style="height:calc(100% - 32px);width:100%"></div>
                                <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-mini-symbol-overview.js" async>
                                {
                                  "symbol": "BINANCE:BTCUSDT",
                                  "width": "100%",
                                  "height": "180",
                                  "locale": "en",
                                  "dateRange": "12M",
                                  "colorTheme": "dark",
                                  "isTransparent": false,
                                  "autosize": false,
                                  "largeChartUrl": ""
                                }
                                </script>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Signal Details & Analysis Row -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Signal Details & Analysis -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                        <h3 class="text-lg font-semibold mb-4 flex items-center">
                            <span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                            Signal Details & Analysis
                        </h3>
                        
                        <!-- Signal Status -->
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                            <div class="text-center">
                                <div class="text-green-600 font-bold text-2xl">BUY</div>
                                <div class="text-sm text-gray-600">BTC/USDT</div>
                                <div class="text-xs text-gray-500 mt-1">ACTIVATED</div>
                                <button class="mt-2 bg-blue-500 text-white px-4 py-1 rounded text-sm hover:bg-blue-600">Copy Signal</button>
                            </div>
                        </div>

                        <!-- Performance Stats -->
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Overall Signal Strength</span>
                                <span class="font-semibold">87%</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Latest Trend</span>
                                <span class="text-green-600 font-semibold">BULLISH</span>
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
                                <span class="font-semibold">0.12</span>
                            </div>
                            <div class="flex justify-between">
                                <span>MACD</span>
                                <span class="font-semibold">0.08</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Moving Average</span>
                                <span class="font-semibold">1.15</span>
                            </div>
                            <div class="flex justify-between">
                                <span>RSI</span>
                                <span class="font-semibold">68.00</span>
                            </div>
                            
                            <div class="mt-4">
                                <div class="flex justify-between mb-1">
                                    <span>Strength</span>
                                    <span class="text-green-600 font-semibold">72.00</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: 72%"></div>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">Bullish</div>
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
                                <div class="text-lg font-bold">$67,500.00</div>
                            </div>
                            
                            <div>
                                <div class="text-sm text-gray-600">Take Profit:</div>
                                <div class="text-lg font-bold text-green-600">$72,000.00</div>
                            </div>
                            
                            <div>
                                <div class="text-sm text-gray-600">Stop Loss:</div>
                                <div class="text-lg font-bold text-red-600">$65,000.00</div>
                            </div>
                            
                            <div>
                                <div class="text-sm text-gray-600">Risk Level:</div>
                                <div class="text-lg font-bold">Medium</div>
                            </div>
                            
                            <div>
                                <div class="text-sm text-gray-600">Timeframe:</div>
                                <div class="text-lg font-bold">1H</div>
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
                            <p>Analisis menunjukkan tren bullish yang kuat dengan momentum yang terus meningkat. Target profit yang telah ditetapkan memiliki probabilitas tinggi untuk tercapai dalam timeframe 1H.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Signals Table -->
            <div class="mt-8">
                <h3 class="text-lg font-semibold mb-4">Recent Expert Signals</h3>
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PAIR</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SIGNAL</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ENTRY</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">TP</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SL</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">STATUS</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">TIME</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">BTC/USDT</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            BUY
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$67,500.00</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">$72,000.00</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">$65,000.00</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Active</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">10:31</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">ETH/USDT</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            SELL
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$3,850.00</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">$3,600.00</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">$4,000.00</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Active</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">10:31</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">ADA/USDT</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            HODL
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$0.49</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">$0.00</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">$0.42</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Active</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">10:31</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Function to change timeframe
        function changeTimeframe(interval) {
            // Update button states
            document.querySelectorAll('button[onclick^="changeTimeframe"]').forEach(btn => {
                btn.className = 'px-3 py-1 text-xs bg-gray-200 text-gray-700 rounded hover:bg-gray-300';
            });
            event.target.className = 'px-3 py-1 text-xs bg-blue-500 text-white rounded hover:bg-blue-600';
            
            // Here you would typically reload the TradingView widgets with new interval
            // For now, we'll just log the change
            console.log('Timeframe changed to:', interval);
            
            // In a real implementation, you would update the TradingView widget configuration
            // and reload the widgets with the new interval
        }
        
        // Initialize TradingView widgets after page load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('TradingView widgets initialized');
        });
    </script>
    @endpush
</x-app-layout>