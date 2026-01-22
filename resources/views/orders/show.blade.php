<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            تفاصيل الطلب #{{ $order->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-lg rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">معلومات الطلب</h3>
                            <div class="space-y-3">
                                <div class="flex">
                                    <span class="text-gray-500 w-32">رقم الطلب:</span>
                                    <span class="font-medium text-gray-900">#{{ $order->id }}</span>
                                </div>
                                <div class="flex">
                                    <span class="text-gray-500 w-32">العميل:</span>
                                    <a href="{{ route('leads.show', $order->lead) }}" class="font-medium text-indigo-600">
                                        {{ $order->lead->lead_code }} - {{ $order->lead->customer_name }}
                                    </a>
                                </div>
                                <div class="flex">
                                    <span class="text-gray-500 w-32">حالة الطلب:</span>
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ match($order->order_status) {
                                            'Pending' => 'bg-yellow-100 text-yellow-800',
                                            'Confirmed' => 'bg-green-100 text-green-800',
                                            'Shipped' => 'bg-indigo-100 text-indigo-800',
                                            'Cancelled' => 'bg-red-100 text-red-800',
                                        } }}">
                                        {{ __('orders.status.' . $order->order_status) }}
                                    </span>
                                </div>
                                <div class="flex">
                                    <span class="text-gray-500 w-32">طريقة الدفع:</span>
                                    <span class="font-medium text-gray-900">
                                        {{ __('orders.payment_method.' . $order->payment_method) }}
                                    </span>
                                </div>
                                <div class="flex">
                                    <span class="text-gray-500 w-32">تاريخ الإنشاء:</span>
                                    <span class="font-medium text-gray-900">{{ $order->created_at->format('Y-m-d') }}</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">معلومات العميل</h3>
                            <div class="space-y-3">
                                <div class="flex">
                                    <span class="text-gray-500 w-32">اسم العميل:</span>
                                    <span class="font-medium text-gray-900">{{ $order->lead->customer_name }}</span>
                                </div>
                                <div class="flex">
                                    <span class="text-gray-500 w-32">رقم الهاتف:</span>
                                    <span class="font-medium text-gray-900">{{ $order->lead->phone }}</span>
                                </div>
                                <div class="flex">
                                    <span class="text-gray-500 w-32">البريد الإلكتروني:</span>
                                    <span class="font-medium text-gray-900">{{ $order->lead->email ?? '-' }}</span>
                                </div>
                                <div class="flex">
                                    <span class="text-gray-500 w-32">المدينة:</span>
                                    <span class="font-medium text-gray-900">{{ $order->lead->city ?? '-' }}</span>
                                </div>
                                <div class="flex">
                                    <span class="text-gray-500 w-32">العنوان:</span>
                                    <span class="font-medium text-gray-900">{{ $order->lead->address ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($order->notes)
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">ملاحظات الطلب:</h4>
                            <p class="text-gray-900">{{ $order->notes }}</p>
                        </div>
                    @endif

                    <div class="flex gap-2">
                        <a href="{{ route('orders.invoice', $order) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md">
                            تحميل الفاتورة PDF
                        </a>
                        <a href="{{ route('orders.edit', $order) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md">
                            تعديل الطلب
                        </a>
                        <a href="{{ route('leads.show', $order->lead) }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-md">
                            العودة للعميل
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
                    <h3 class="text-lg font-medium text-gray-900 mb-4">المنتجات</h3>
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المنتج</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المتغير</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الكمية</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">السعر</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr class="border-t border-gray-200">
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ $item->product_name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ $item->variant }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ $item->quantity }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ number_format($item->unit_price, 2) }} ر.س</td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ number_format($item->line_total, 2) }} ر.س</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-50">
                                <td colspan="4" class="px-6 py-4 text-left text-sm font-medium text-gray-700">إجمالي الطلب:</td>
                                <td class="px-6 py-4 text-left text-xl font-bold text-green-600">{{ number_format($order->total_value, 2) }} ر.س</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
