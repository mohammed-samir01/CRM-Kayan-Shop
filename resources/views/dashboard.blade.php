<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            لوحة التحكم
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Date Filter -->
            <form method="GET" action="{{ route('dashboard') }}" class="bg-white p-4 rounded-lg shadow flex flex-wrap gap-4 items-end justify-between" id="filterForm">
                <div class="flex flex-wrap gap-4 items-end">
                    <div>
                        <label for="date_range" class="block text-sm font-medium text-gray-700">الفترة الزمنية</label>
                        <select name="date_range" id="date_range" onchange="toggleCustomDates()" class="mt-1 block w-48 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="today" {{ $dateRange == 'today' ? 'selected' : '' }}>اليوم</option>
                            <option value="yesterday" {{ $dateRange == 'yesterday' ? 'selected' : '' }}>أمس</option>
                            <option value="last_7_days" {{ $dateRange == 'last_7_days' ? 'selected' : '' }}>آخر 7 أيام</option>
                            <option value="last_30_days" {{ $dateRange == 'last_30_days' ? 'selected' : '' }}>آخر 30 يوم</option>
                            <option value="last_60_days" {{ $dateRange == 'last_60_days' ? 'selected' : '' }}>آخر 60 يوم</option>
                            <option value="last_90_days" {{ $dateRange == 'last_90_days' ? 'selected' : '' }}>آخر 90 يوم</option>
                            <option value="this_month" {{ $dateRange == 'this_month' ? 'selected' : '' }}>هذا الشهر</option>
                            <option value="last_month" {{ $dateRange == 'last_month' ? 'selected' : '' }}>الشهر الماضي</option>
                            <option value="this_year" {{ $dateRange == 'this_year' ? 'selected' : '' }}>هذا العام</option>
                            <option value="custom" {{ $dateRange == 'custom' ? 'selected' : '' }}>تاريخ مخصص</option>
                        </select>
                    </div>
                    
                    <div id="custom_dates" class="{{ $dateRange == 'custom' ? '' : 'hidden' }} flex gap-4">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">من</label>
                            <input type="date" name="start_date" id="start_date" value="{{ $startDate->format('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700">إلى</label>
                            <input type="date" name="end_date" id="end_date" value="{{ $endDate->format('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                    </div>
                </div>

                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded shadow">تطبيق الفلتر</button>
            </form>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Leads Count -->
                <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div class="mr-4">
                                <p class="text-sm font-medium text-gray-500">العملاء</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $leadsCount }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Conversion Rate -->
                <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                            </div>
                            <div class="mr-4">
                                <p class="text-sm font-medium text-gray-500">معدل التحويل</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ number_format($conversionRate, 1) }}%</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Revenue -->
                <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="mr-4">
                                <p class="text-sm font-medium text-gray-500">الإيرادات</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ number_format($revenue, 2) }} ج.م</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Orders Count -->
                <div class="bg-white overflow-hidden shadow-lg rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <div class="mr-4">
                                <p class="text-sm font-medium text-gray-500">الطلبات</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $ordersCount }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Sales Chart -->
                <div class="bg-white shadow-lg rounded-lg col-span-1 lg:col-span-2">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">المبيعات</h3>
                        <div class="h-72">
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Lead Status Chart -->
                <div class="bg-white shadow-lg rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">توزيع حالات العملاء</h3>
                        <div class="h-64 flex justify-center">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Platforms Chart -->
                <div class="bg-white shadow-lg rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">أهم المنصات</h3>
                        <div class="h-64 flex justify-center">
                            <canvas id="platformChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tables Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white shadow-lg rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">العملاء المتوقعين حسب الحالة</h3>
                        <div class="space-y-3">
                            @foreach($leadsByStatus as $status => $count)
                                <div class="flex items-center justify-between p-2 hover:bg-gray-50 rounded-md transition-colors">
                                    <a href="{{ route('leads.index', ['status' => $status]) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 hover:underline">
                                        {{ __('leads.status.' . $status) }}
                                    </a>
                                    <span class="text-sm font-bold text-gray-900">{{ $count }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow-lg rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">أهم المنصات</h3>
                        <div class="space-y-3">
                            @foreach($topPlatforms as $platform)
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-700">
                                        @if(is_array($platform->platform))
                                            @foreach($platform->platform as $p)
                                                {{ __('campaigns.platform.' . $p) }}@if(!$loop->last), @endif
                                            @endforeach
                                        @else
                                            {{ __('campaigns.platform.' . $platform->platform) }}
                                        @endif
                                    </span>
                                    <span class="text-sm font-bold text-gray-900">{{ $platform->count }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Analytics Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Top Selling Products -->
                <div class="bg-white shadow-lg rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">المنتجات الأكثر مبيعاً</h3>
                        <div class="space-y-3">
                            @foreach($topProducts as $item)
                                <div class="flex items-center justify-between p-2 border-b border-gray-100 last:border-0">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                        </div>
                                        <div class="mr-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $item->product?->name ?? 'منتج غير موجود' }}</div>
                                            <div class="text-xs text-gray-500">{{ $item->product?->sku ?? '#' . $item->product_id }}</div>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ $item->total_sold }} مباع
                                    </span>
                                </div>
                            @endforeach
                            @if($topProducts->isEmpty())
                                <p class="text-sm text-gray-500 text-center py-4">لا توجد مبيعات حتى الآن</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Low Stock Alerts -->
                <div class="bg-white shadow-lg rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900">تنبيهات نقص المخزون</h3>
                            <span class="px-2 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded-full">أقل من 10</span>
                        </div>
                        <div class="space-y-3">
                            @foreach($lowStockProducts as $product)
                                <div class="flex items-center justify-between p-2 border-b border-gray-100 last:border-0 bg-red-50 rounded-md">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 bg-red-100 rounded-full flex items-center justify-center text-red-600">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                        </div>
                                        <div class="mr-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $product->sku }}</div>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        {{ $product->stock }} متبقي
                                    </span>
                                </div>
                            @endforeach
                            @if($lowStockProducts->isEmpty())
                                <p class="text-sm text-green-600 text-center py-4 flex items-center justify-center">
                                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    جميع المنتجات متوفرة بكميات جيدة
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-lg rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">آخر العملاء المتوقعين</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">كود العميل</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">اسم العميل</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">رقم الهاتف</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحملة</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($recentLeads as $lead)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600">
                                            <a href="{{ route('leads.show', $lead) }}">{{ $lead->lead_code }}</a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $lead->customer_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $lead->phone }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ match($lead->status) {
                                                    'New' => 'bg-blue-100 text-blue-800',
                                                    'Contacted' => 'bg-yellow-100 text-yellow-800',
                                                    'Interested' => 'bg-green-100 text-green-800',
                                                    'Confirmed' => 'bg-indigo-100 text-indigo-800',
                                                    'Shipped' => 'bg-purple-100 text-purple-800',
                                                    'Cancelled' => 'bg-red-100 text-red-800',
                                                    default => 'bg-gray-100 text-gray-800',
                                                } }}">
                                                {{ __('leads.status.' . $lead->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $lead->campaign?->name ?? '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Sales Chart
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_keys($chartData)) !!},
                datasets: [{
                    label: 'المبيعات اليومية',
                    data: {!! json_encode(array_values($chartData)) !!},
                    borderColor: 'rgb(79, 70, 229)',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    tension: 0.1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        rtl: true
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Status Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        const statusData = {!! json_encode($leadsByStatus) !!};
        const statusTranslations = {!! json_encode($statusTranslations) !!};
        
        // Define colors for statuses
        const statusColors = {
            'New': '#3B82F6', // Blue
            'Contacted': '#F59E0B', // Yellow
            'Interested': '#10B981', // Green
            'Confirmed': '#6366F1', // Indigo
            'Shipped': '#8B5CF6', // Purple
            'Cancelled': '#EF4444', // Red
        };

        const statusLabels = Object.keys(statusData).map(status => {
            return statusTranslations[status] || status;
        });

        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: Object.values(statusData),
                    backgroundColor: Object.keys(statusData).map(status => statusColors[status] || '#9CA3AF')
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        rtl: true
                    }
                }
            }
        });

        // Platform Chart
        const platformCtx = document.getElementById('platformChart').getContext('2d');
        const platforms = {!! json_encode($topPlatforms) !!};
        const platformTranslations = {!! json_encode($platformTranslations) !!};
        
        const platformColors = [
            '#1877F2', // Facebook Blue
            '#E1306C', // Instagram Pink
            '#1DA1F2', // Twitter Blue
            '#0A66C2', // LinkedIn Blue
            '#FF0000', // YouTube Red
            '#000000', // TikTok Black
        ];

        new Chart(platformCtx, {
            type: 'pie',
            data: {
                labels: platforms.map(p => {
                    return platformTranslations[p.platform] || p.platform;
                }),
                datasets: [{
                    data: platforms.map(p => p.count),
                    backgroundColor: platformColors.slice(0, platforms.length)
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        rtl: true
                    }
                }
            }
        });

        function toggleCustomDates() {
            const range = document.getElementById('date_range').value;
            const customDates = document.getElementById('custom_dates');
            if (range === 'custom') {
                customDates.classList.remove('hidden');
            } else {
                customDates.classList.add('hidden');
            }
        }
        
        // Initialize on load
        document.addEventListener('DOMContentLoaded', function() {
            toggleCustomDates();
        });
    </script>
    @endpush
</x-app-layout>
