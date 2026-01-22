<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            تعديل عميل متوقع: {{ $lead->lead_code }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('leads.update', $lead) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">اسم العميل <span class="text-red-500">*</span></label>
                                <input type="text" name="customer_name" value="{{ old('customer_name', $lead->customer_name) }}" required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('customer_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">رقم الهاتف <span class="text-red-500">*</span></label>
                                <input type="text" name="phone" value="{{ old('phone', $lead->phone) }}" required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">البريد الإلكتروني</label>
                                <input type="email" name="email" value="{{ old('email', $lead->email) }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">المدينة</label>
                                <input type="text" name="city" value="{{ old('city', $lead->city) }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">العنوان</label>
                                <textarea name="address" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('address', $lead->address) }}</textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">الحملة الإعلانية</label>
                                <select name="campaign_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">اختر الحملة</option>
                                    @foreach(\App\Models\Campaign::all() as $campaign)
                                        <option value="{{ $campaign->id }}" {{ old('campaign_id', $lead->campaign_id) == $campaign->id ? 'selected' : '' }}>
                                            {{ $campaign->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">المسؤول</label>
                                <select name="assigned_to" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">اختر المسؤول</option>
                                    @foreach(\App\Models\User::all() as $user)
                                        <option value="{{ $user->id }}" {{ old('assigned_to', $lead->assigned_to) == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">الحالة <span class="text-red-500">*</span></label>
                                <select name="status" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="New" {{ old('status', $lead->status) == 'New' ? 'selected' : '' }}>جديد</option>
                                    <option value="Contacted" {{ old('status', $lead->status) == 'Contacted' ? 'selected' : '' }}>تم التواصل</option>
                                    <option value="Interested" {{ old('status', $lead->status) == 'Interested' ? 'selected' : '' }}>مهتم</option>
                                    <option value="Confirmed" {{ old('status', $lead->status) == 'Confirmed' ? 'selected' : '' }}>مؤكد</option>
                                    <option value="Shipped" {{ old('status', $lead->status) == 'Shipped' ? 'selected' : '' }}>تم الشحن</option>
                                    <option value="Cancelled" {{ old('status', $lead->status) == 'Cancelled' ? 'selected' : '' }}>ملغي</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">تاريخ المتابعة</label>
                                <input type="date" name="follow_up_date" value="{{ old('follow_up_date', $lead->follow_up_date?->format('Y-m-d')) }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">ملاحظات</label>
                                <textarea name="notes" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes', $lead->notes) }}</textarea>
                            </div>
                        </div>

                        <div class="mt-6 flex gap-2">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-md">
                                تحديث
                            </button>
                            <a href="{{ route('leads.show', $lead) }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-md">
                                إلغاء
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
