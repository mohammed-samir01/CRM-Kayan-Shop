<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            إضافة حملة إعلانية
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('campaigns.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">اسم الحملة <span class="text-red-500">*</span></label>
                                <input type="text" name="name" value="{{ old('name') }}" required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">المنصة <span class="text-red-500">*</span></label>
                                <select name="platform[]" required multiple class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 h-32">
                                    <option value="TikTok" {{ in_array('TikTok', old('platform', [])) ? 'selected' : '' }}>TikTok</option>
                                    <option value="Facebook" {{ in_array('Facebook', old('platform', [])) ? 'selected' : '' }}>Facebook</option>
                                    <option value="Instagram" {{ in_array('Instagram', old('platform', [])) ? 'selected' : '' }}>Instagram</option>
                                    <option value="Google" {{ in_array('Google', old('platform', [])) ? 'selected' : '' }}>Google</option>
                                    <option value="Snapchat" {{ in_array('Snapchat', old('platform', [])) ? 'selected' : '' }}>Snapchat</option>
                                    <option value="X" {{ in_array('X', old('platform', [])) ? 'selected' : '' }}>X</option>
                                    <option value="YouTube" {{ in_array('YouTube', old('platform', [])) ? 'selected' : '' }}>YouTube</option>
                                    <option value="LinkedIn" {{ in_array('LinkedIn', old('platform', [])) ? 'selected' : '' }}>LinkedIn</option>
                                    <option value="Other" {{ in_array('Other', old('platform', [])) ? 'selected' : '' }}>أخرى</option>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">يمكنك اختيار أكثر من منصة بالضغط على Ctrl (أو Cmd) أثناء الاختيار</p>
                                @error('platform')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">نوع الإعلان <span class="text-red-500">*</span></label>
                                <select name="ad_type" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="Video" {{ old('ad_type') == 'Video' ? 'selected' : '' }}>فيديو</option>
                                    <option value="Image" {{ old('ad_type') == 'Image' ? 'selected' : '' }}>صورة</option>
                                    <option value="Carousel" {{ old('ad_type') == 'Carousel' ? 'selected' : '' }}>سلايدر</option>
                                    <option value="Search" {{ old('ad_type') == 'Search' ? 'selected' : '' }}>بحث</option>
                                    <option value="Story" {{ old('ad_type') == 'Story' ? 'selected' : '' }}>ستوري</option>
                                    <option value="Other" {{ old('ad_type') == 'Other' ? 'selected' : '' }}>أخرى</option>
                                </select>
                                @error('ad_type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">المصدر <span class="text-red-500">*</span></label>
                                <select name="source" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="Form" {{ old('source') == 'Form' ? 'selected' : '' }}>نموذج</option>
                                    <option value="WhatsApp" {{ old('source') == 'WhatsApp' ? 'selected' : '' }}>واتساب</option>
                                    <option value="Phone Call" {{ old('source') == 'Phone Call' ? 'selected' : '' }}>مكالمة هاتفية</option>
                                    <option value="Website" {{ old('source') == 'Website' ? 'selected' : '' }}>الموقع الإلكتروني</option>
                                    <option value="DM" {{ old('source') == 'DM' ? 'selected' : '' }}">رسالة مباشرة</option>
                                    <option value="Other" {{ old('source') == 'Other' ? 'selected' : '' }}>أخرى</option>
                                </select>
                                @error('source')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">ملاحظات</label>
                                <textarea name="notes" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <div class="mt-6 flex gap-2">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-md">
                                حفظ
                            </button>
                            <a href="{{ route('campaigns.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-md">
                                إلغاء
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
