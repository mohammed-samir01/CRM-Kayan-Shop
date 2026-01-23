<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            الطلبات
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-lg mb-6">
                <div class="p-4">
                    <form method="GET" action="{{ route('orders.index') }}">
                        <div class="flex gap-4 items-end">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">بحث</label>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="رقم الطلب، اسم العميل..." class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">الحالة</label>
                                <select name="status" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">الكل</option>
                                    @foreach(['Pending', 'Confirmed', 'Shipped', 'Cancelled'] as $status)
                                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                            {{ __('orders.status.' . $status) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex items-end">
                                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md">
                                    بحث
                                </button>
                                <a href="{{ route('orders.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-md mr-2">
                                    إعادة تعيين
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white shadow-lg rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">قائمة الطلبات</h3>
                    <div class="flex gap-2">
                        @can('create orders')
                            <a href="{{ route('orders.create_selection') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md flex items-center">
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                إضافة طلب جديد
                            </a>
                        @endcan
                        @can('view reports')
                        <a href="{{ route('reports.orders', request()->query()) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md flex items-center">
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            تصدير CSV
                        </a>
                        @endcan
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">رقم الطلب</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العميل</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">طريقة الدفع</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجمالي</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($orders as $order)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600">
                                        #{{ $order->id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <a href="{{ route('leads.show', $order->lead) }}" class="text-indigo-600 hover:text-indigo-900">
                                            {{ $order->lead->lead_code }} - {{ $order->lead->customer_name }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ match($order->order_status) {
                                                'Pending' => 'bg-yellow-100 text-yellow-800',
                                                'Confirmed' => 'bg-green-100 text-green-800',
                                                'Shipped' => 'bg-indigo-100 text-indigo-800',
                                                'Cancelled' => 'bg-red-100 text-red-800',
                                            } }}">
                                            {{ __('orders.status.' . $order->order_status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ __('orders.payment_method.' . $order->payment_method) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ number_format($order->total_value, 2) }} ر.س</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <a href="{{ route('orders.show', $order) }}" class="text-indigo-600 hover:text-indigo-900 ml-2">عرض</a>
                                        <a href="{{ route('orders.invoice', $order) }}" class="text-green-600 hover:text-green-900 ml-2">PDF</a>
                                        @can('edit orders')
                                        <a href="{{ route('orders.edit', $order) }}" class="text-indigo-600 hover:text-indigo-900 ml-2">تعديل</a>
                                        @endcan
                                        @can('delete', $order)
                                            <form action="{{ route('orders.destroy', $order) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('هل أنت متأكد؟')" class="text-red-600 hover:text-red-900">حذف</button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">لا توجد طلبات</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
