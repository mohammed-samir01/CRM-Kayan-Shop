<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            العملاء المتوقعين
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('leads.index') }}">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">بحث</label>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="الاسم، الهاتف، أو الكود"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">المنصة</label>
                                <select name="platform" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">الكل</option>
                                    @foreach(['TikTok', 'Facebook', 'Instagram', 'Google', 'Snapchat', 'X', 'YouTube', 'LinkedIn', 'Other'] as $platform)
                                        <option value="{{ $platform }}" {{ request('platform') == $platform ? 'selected' : '' }}>
                                            {{ __('campaigns.platform.' . $platform) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">الحالة</label>
                                <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">الكل</option>
                                    @foreach(['New', 'Contacted', 'Interested', 'Confirmed', 'Shipped', 'Cancelled'] as $status)
                                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                            {{ __('leads.status.' . $status) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">من تاريخ</label>
                                <input type="date" name="date_from" value="{{ request('date_from') }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">إلى تاريخ</label>
                                <input type="date" name="date_to" value="{{ request('date_to') }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>
                        <div class="mt-4 flex gap-2">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md">
                                بحث
                            </button>
                            <a href="{{ route('leads.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-md">
                                إعادة تعيين
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white shadow-lg rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">قائمة العملاء المتوقعين</h3>
                    <div class="flex gap-2">
                        @can('view reports')
                        <a href="{{ route('reports.leads', request()->query()) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md flex items-center">
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            تصدير CSV
                        </a>
                        @endcan
                        @can('create leads')
                        <a href="{{ route('leads.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md">
                            + إضافة عميل
                        </a>
                        @endcan
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الكود</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">اسم العميل</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">رقم الهاتف</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">القيمة المتوقعة</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ المتابعة</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($leads as $lead)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600">
                                        <a href="{{ route('leads.show', $lead) }}">{{ $lead->lead_code }}</a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $lead->customer_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $lead->phone }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
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
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($lead->expected_value, 2) }} ج.م</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $lead->follow_up_date?->format('Y-m-d') ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <a href="{{ route('leads.show', $lead) }}" class="text-indigo-600 hover:text-indigo-900 ml-2">عرض</a>
                                        @can('edit leads')
                                        <a href="{{ route('leads.edit', $lead) }}" class="text-indigo-600 hover:text-indigo-900 ml-2">تعديل</a>
                                        @endcan
                                        @can('delete', $lead)
                                            <form action="{{ route('leads.destroy', $lead) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('هل أنت متأكد؟')" class="text-red-600 hover:text-red-900">حذف</button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">لا يوجد عملاء متوقعين</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $leads->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
