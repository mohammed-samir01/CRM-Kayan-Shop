<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            تفاصيل المنتج: {{ $product->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-lg rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">معلومات المنتج</h3>
                    <div class="space-y-4">
                        <div class="flex">
                            <span class="text-gray-500 w-32">الاسم:</span>
                            <span class="font-medium text-gray-900">{{ $product->name }}</span>
                        </div>
                        <div class="flex">
                            <span class="text-gray-500 w-32">SKU:</span>
                            <span class="font-medium text-gray-900">{{ $product->sku ?? '-' }}</span>
                        </div>
                        <div class="flex">
                            <span class="text-gray-500 w-32">السعر:</span>
                            <span class="font-medium text-green-600">{{ number_format($product->price, 2) }} ر.س</span>
                        </div>
                        <div class="flex">
                            <span class="text-gray-500 w-32">المخزون:</span>
                            <span class="font-medium text-gray-900">{{ $product->stock }}</span>
                        </div>
                        <div class="flex">
                            <span class="text-gray-500 w-32">الحالة:</span>
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $product->is_active ? 'نشط' : 'غير نشط' }}
                            </span>
                        </div>
                        
                        <div class="flex flex-col sm:flex-row sm:items-start">
                            <span class="text-gray-500 w-32 mb-2 sm:mb-0">المقاسات المتوفرة:</span>
                            <div class="flex flex-wrap gap-2">
                                @forelse($product->sizes ?? [] as $size)
                                    <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-sm">{{ $size }}</span>
                                @empty
                                    <span class="text-gray-400">لا يوجد مقاسات محددة</span>
                                @endforelse
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-start">
                            <span class="text-gray-500 w-32 mb-2 sm:mb-0">الألوان المتوفرة:</span>
                            <div class="flex flex-wrap gap-2">
                                @forelse($product->colors ?? [] as $color)
                                    <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-sm">{{ $color }}</span>
                                @empty
                                    <span class="text-gray-400">لا يوجد ألوان محددة</span>
                                @endforelse
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-start">
                            <span class="text-gray-500 w-32 mb-2 sm:mb-0">الوصف:</span>
                            <p class="text-gray-900">{{ $product->description ?? 'لا يوجد وصف' }}</p>
                        </div>
                    </div>

                    <div class="mt-6 flex gap-2">
                        <a href="{{ route('products.edit', $product) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md">
                            تعديل
                        </a>
                        <a href="{{ route('products.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-md">
                            عودة للقائمة
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>