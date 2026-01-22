<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            تفاصيل العميل: {{ $lead->lead_code }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white shadow-lg rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">معلومات العميل</h3>
                        <div class="space-y-3">
                            <div class="flex">
                                <span class="text-gray-500 w-32">الكود:</span>
                                <span class="font-medium text-gray-900">{{ $lead->lead_code }}</span>
                            </div>
                            <div class="flex">
                                <span class="text-gray-500 w-32">اسم العميل:</span>
                                <span class="font-medium text-gray-900">{{ $lead->customer_name }}</span>
                            </div>
                            <div class="flex">
                                <span class="text-gray-500 w-32">رقم الهاتف:</span>
                                <span class="font-medium text-gray-900">{{ $lead->phone }}</span>
                            </div>
                            <div class="flex">
                                <span class="text-gray-500 w-32">البريد الإلكتروني:</span>
                                <span class="font-medium text-gray-900">{{ $lead->email ?? '-' }}</span>
                            </div>
                            <div class="flex">
                                <span class="text-gray-500 w-32">المدينة:</span>
                                <span class="font-medium text-gray-900">{{ $lead->city ?? '-' }}</span>
                            </div>
                            <div class="flex">
                                <span class="text-gray-500 w-32">العنوان:</span>
                                <span class="font-medium text-gray-900">{{ $lead->address ?? '-' }}</span>
                            </div>
                            <div class="flex">
                                <span class="text-gray-500 w-32">الحالة:</span>
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ match($lead->status) {
                                        'New' => 'bg-blue-100 text-blue-800',
                                        'Contacted' => 'bg-yellow-100 text-yellow-800',
                                        'Interested' => 'bg-green-100 text-green-800',
                                        'Confirmed' => 'bg-indigo-100 text-indigo-800',
                                        'Shipped' => 'bg-purple-100 text-purple-800',
                                        'Cancelled' => 'bg-red-100 text-red-800',
                                    } }}">
                                    {{ __('leads.status.' . $lead->status) }}
                                </span>
                            </div>
                            <div class="flex">
                                <span class="text-gray-500 w-32">القيمة المتوقعة:</span>
                                <span class="font-medium text-green-600">{{ number_format($lead->expected_value, 2) }} ر.س</span>
                            </div>
                            <div class="flex">
                                <span class="text-gray-500 w-32">تاريخ المتابعة:</span>
                                <span class="font-medium text-gray-900">{{ $lead->follow_up_date?->format('Y-m-d') ?? '-' }}</span>
                            </div>
                            <div class="flex">
                                <span class="text-gray-500 w-32">المسؤول:</span>
                                <span class="font-medium text-gray-900">{{ $lead->assignedTo?->name ?? '-' }}</span>
                            </div>
                        </div>

                        <div class="mt-6 flex gap-2">
                            <a href="{{ route('leads.edit', $lead) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md">
                                تعديل
                            </a>
                            <a href="{{ route('orders.create', $lead) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md">
                                + إضافة طلب
                            </a>
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow-lg rounded-lg mt-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">سجل النشاط</h3>
                    </div>
                    <div class="p-6">
                        @forelse($activities as $activity)
                            <div class="border-b border-gray-200 pb-4 mb-4 last:border-0">
                                <div class="flex justify-between items-start">
                                    <span class="font-medium text-gray-900">{{ $activity->description }}</span>
                                    <span class="text-sm text-gray-500">{{ $activity->created_at->format('Y-m-d H:i') }}</span>
                                </div>
                                <div class="mt-2 text-sm text-gray-500">
                                    بواسطة: {{ $activity->causer?->name ?? 'غير معروف' }}
                                </div>
                                @if(isset($activity->properties['changes']) && count($activity->properties['changes']) > 0)
                                    <div class="mt-2 text-sm text-gray-700">
                                        التغييرات:
                                        <ul class="list-disc list-inside mt-1">
                                            @foreach($activity->properties['changes'] as $key => $value)
                                                <li>{{ $key }}: {{ is_array($value) ? json_encode($value) : $value }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-4">لا توجد سجل نشاط</p>
                        @endforelse
                        {{ $activities->links() }}
                    </div>
                </div>

                <div class="bg-white shadow-lg rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">معلومات الحملة</h3>
                        @if($lead->campaign)
                            <div class="space-y-3">
                                <div class="flex">
                                    <span class="text-gray-500 w-32">اسم الحملة:</span>
                                    <span class="font-medium text-gray-900">{{ $lead->campaign->name }}</span>
                                </div>
                                <div class="flex">
                                    <span class="text-gray-500 w-32">المنصة:</span>
                                    <span class="font-medium text-gray-900">{{ __('campaigns.platform.' . $lead->campaign->platform) }}</span>
                                </div>
                                <div class="flex">
                                    <span class="text-gray-500 w-32">نوع الإعلان:</span>
                                    <span class="font-medium text-gray-900">{{ __('campaigns.ad_type.' . $lead->campaign->ad_type) }}</span>
                                </div>
                                <div class="flex">
                                    <span class="text-gray-500 w-32">المصدر:</span>
                                    <span class="font-medium text-gray-900">{{ $lead->campaign->source }}</span>
                                </div>
                                @if($lead->campaign->notes)
                                    <div class="mt-4">
                                        <span class="text-gray-500">ملاحظات:</span>
                                        <p class="mt-1 text-gray-900">{{ $lead->campaign->notes }}</p>
                                    </div>
                                @endif
                            </div>
                        @else
                            <p class="text-gray-500">لا توجد حملة مرتبطة</p>
                        @endif

                        @if($lead->notes)
                            <div class="mt-6">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">ملاحظات العميل:</h4>
                                <p class="text-gray-900">{{ $lead->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-lg rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">الطلبات</h3>
                </div>

                <div class="p-6">
                    @forelse($lead->orders as $order)
                        <div class="border-b border-gray-200 pb-4 mb-4 last:border-0">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <span class="text-sm font-medium text-gray-500">طلب #{{ $order->id }}</span>
                                    <div class="flex gap-2 mt-1">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                            {{ match($order->order_status) {
                                                'Pending' => 'معلق',
                                                'Confirmed' => 'مؤكد',
                                                'Shipped' => 'تم الشحن',
                                                'Cancelled' => 'ملغي',
                                            } }}
                                        </span>
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            {{ match($order->payment_method) {
                                                'Cash' => 'نقدي',
                                                'Transfer' => 'تحويل بنكي',
                                                'Online' => 'أونلاين',
                                                'COD' => 'الدفع عند الاستلام',
                                            } }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <a href="{{ route('orders.edit', $order) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">تعديل</a>
                                    @can('delete', $order)
                                        <form action="{{ route('orders.destroy', $order) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('هل أنت متأكد؟')" class="text-red-600 hover:text-red-900 text-sm">حذف</button>
                                        </form>
                                    @endcan
                                </div>
                            </div>

                            <table class="min-w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">المنتج</th>
                                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">المتغير</th>
                                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">الكمية</th>
                                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">السعر</th>
                                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">الإجمالي</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                        <tr class="border-t border-gray-100">
                                            <td class="px-3 py-2 text-sm text-gray-900">{{ $item->product_name }}</td>
                                            <td class="px-3 py-2 text-sm text-gray-900">{{ $item->variant }}</td>
                                            <td class="px-3 py-2 text-sm text-gray-900">{{ $item->quantity }}</td>
                                            <td class="px-3 py-2 text-sm text-gray-900">{{ number_format($item->unit_price, 2) }} ر.س</td>
                                            <td class="px-3 py-2 text-sm font-medium text-gray-900">{{ number_format($item->line_total, 2) }} ر.س</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="bg-gray-50">
                                        <td colspan="4" class="px-3 py-2 text-left text-sm font-medium text-gray-700">إجمالي الطلب:</td>
                                        <td class="px-3 py-2 text-left text-lg font-bold text-green-600">{{ number_format($order->total_value, 2) }} ر.س</td>
                                    </tr>
                                </tfoot>
                            </table>

                            @if($order->notes)
                                <div class="mt-3">
                                    <span class="text-sm font-medium text-gray-500">ملاحظات:</span>
                                    <p class="text-sm text-gray-900 mt-1">{{ $order->notes }}</p>
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">لا توجد طلبات لهذا العميل</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
