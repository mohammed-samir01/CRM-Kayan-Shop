<x-app-layout>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container .select2-selection--single {
            height: 42px;
            border-color: #d1d5db;
            border-radius: 0.375rem;
            padding-top: 5px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
        }
        .select2-container--default .select2-search--dropdown .select2-search__field {
            border-color: #d1d5db;
            border-radius: 0.375rem;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 30px;
        }
    </style>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            @if($lead)
                {{ __('إضافة طلب جديد للعميل') }}: {{ $lead->customer_name }}
            @else
                {{ __('إضافة طلب جديد') }}
            @endif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('orders.store') }}" method="POST">
                        @csrf

                        <!-- Customer Section -->
                        <div class="mb-8 border-b border-gray-200 pb-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('بيانات العميل') }}</h3>
                            
                            @if($lead)
                                <input type="hidden" name="customer_type" value="existing">
                                <input type="hidden" name="lead_id" value="{{ $lead->id }}">
                                <div class="bg-blue-50 p-4 rounded-md flex items-center gap-4 border border-blue-100">
                                    <div class="bg-blue-100 p-2 rounded-full">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $lead->customer_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $lead->phone }}</div>
                                    </div>
                                    <a href="{{ route('orders.create_any') }}" class="mr-auto text-sm text-indigo-600 hover:text-indigo-900">
                                        {{ __('تغيير العميل') }}
                                    </a>
                                </div>
                            @else
                                <div class="flex space-x-4 space-x-reverse mb-6">
                                    <button type="button" onclick="setCustomerType('existing')" id="btn-existing" 
                                        class="px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200 {{ old('customer_type', 'existing') == 'existing' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                        {{ __('اختيار عميل حالي') }}
                                    </button>
                                    <button type="button" onclick="setCustomerType('new')" id="btn-new" 
                                        class="px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200 {{ old('customer_type') == 'new' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                        {{ __('إضافة عميل جديد') }}
                                    </button>
                                </div>
                                
                                <input type="hidden" name="customer_type" id="customer_type" value="{{ old('customer_type', 'existing') }}">
                                
                                <!-- Existing Customer Selection -->
                                <div id="section-existing" class="{{ old('customer_type', 'existing') == 'existing' ? '' : 'hidden' }}">
                                    <div class="max-w-xl">
                                        <x-input-label for="lead_id" :value="__('ابحث عن العميل')" />
                                        <select id="lead_id" name="lead_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="">{{ __('اختر عميلاً') }}</option>
                                            @if(isset($leads))
                                                @foreach($leads as $l)
                                                    <option value="{{ $l->id }}" {{ old('lead_id') == $l->id ? 'selected' : '' }}>
                                                        {{ $l->customer_name }} - {{ $l->phone }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <x-input-error class="mt-2" :messages="$errors->get('lead_id')" />
                                    </div>
                                </div>

                                <!-- New Customer Fields -->
                                <div id="section-new" class="{{ old('customer_type') == 'new' ? '' : 'hidden' }}">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <x-input-label for="customer_name" :value="__('اسم العميل')" />
                                            <x-text-input id="customer_name" class="block mt-1 w-full" type="text" name="customer_name" :value="old('customer_name')" />
                                            <x-input-error class="mt-2" :messages="$errors->get('customer_name')" />
                                        </div>
                                        <div>
                                            <x-input-label for="phone" :value="__('رقم الهاتف')" />
                                            <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" />
                                            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                                        </div>
                                        <div>
                                            <x-input-label for="email" :value="__('البريد الإلكتروني (اختياري)')" />
                                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" />
                                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                                        </div>
                                        <div>
                                            <x-input-label for="city" :value="__('المدينة (اختياري)')" />
                                            <x-text-input id="city" class="block mt-1 w-full" type="text" name="city" :value="old('city')" />
                                            <x-input-error class="mt-2" :messages="$errors->get('city')" />
                                        </div>
                                        <div class="md:col-span-2">
                                            <x-input-label for="address" :value="__('العنوان (اختياري)')" />
                                            <x-text-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address')" />
                                            <x-input-error class="mt-2" :messages="$errors->get('address')" />
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <!-- Payment Method -->
                            <div>
                                <x-input-label for="payment_method" :value="__('طريقة الدفع')" />
                                <select id="payment_method" name="payment_method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="">{{ __('اختر طريقة الدفع') }}</option>
                                    @foreach(['Cash', 'Transfer', 'Online', 'COD'] as $method)
                                        <option value="{{ $method }}" {{ old('payment_method') == $method ? 'selected' : '' }}>
                                            {{ __('orders.payment_method.' . $method) }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('payment_method')" />
                            </div>

                            <!-- Order Status -->
                            <div>
                                <x-input-label for="order_status" :value="__('حالة الطلب')" />
                                <select id="order_status" name="order_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="Pending" {{ old('order_status') == 'Pending' ? 'selected' : '' }}>{{ __('orders.status.Pending') }}</option>
                                    <option value="Confirmed" {{ old('order_status') == 'Confirmed' ? 'selected' : '' }}>{{ __('orders.status.Confirmed') }}</option>
                                    <option value="Shipped" {{ old('order_status') == 'Shipped' ? 'selected' : '' }}>{{ __('orders.status.Shipped') }}</option>
                                    <option value="Cancelled" {{ old('order_status') == 'Cancelled' ? 'selected' : '' }}>{{ __('orders.status.Cancelled') }}</option>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('order_status')" />
                            </div>

                            <!-- Notes -->
                            <div class="col-span-full">
                                <x-input-label for="notes" :value="__('ملاحظات')" />
                                <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('notes')" />
                            </div>
                        </div>

                        <!-- Order Items -->
                        <div class="mt-8 border-t border-gray-200 pt-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('المنتجات') }}</h3>
                            
                            <div id="items-container">
                                <div class="grid grid-cols-12 gap-2 mb-2 font-medium text-gray-700 text-sm">
                                    <div class="col-span-3">{{ __('المنتج') }}</div>
                                    <div class="col-span-2">{{ __('المقاس') }}</div>
                                    <div class="col-span-2">{{ __('اللون') }}</div>
                                    <div class="col-span-1">{{ __('الكمية') }}</div>
                                    <div class="col-span-2">{{ __('السعر') }}</div>
                                    <div class="col-span-2">{{ __('الإجمالي') }}</div>
                                </div>
                                
                                <!-- Initial Row -->
                                <div class="item-row grid grid-cols-12 gap-2 items-end" id="row-0">
                                    <div class="col-span-3">
                                        <select name="items[0][product_id]" onchange="updateProductDetails(this, 0)" required
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                            <option value="">{{ __('اختر منتجاً') }}</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" 
                                                    data-price="{{ $product->price }}" 
                                                    data-name="{{ $product->name }}"
                                                    data-sizes="{{ json_encode($product->sizes ?? []) }}"
                                                    data-colors="{{ json_encode($product->colors ?? []) }}">
                                                    {{ $product->name }} ({{ number_format($product->price, 2) }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="items[0][product_name]" class="product-name-input">
                                    </div>
                                    <div class="col-span-2">
                                        <select name="items[0][size]" id="size-0" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                            <option value="">-</option>
                                        </select>
                                    </div>
                                    <div class="col-span-2">
                                        <select name="items[0][color]" id="color-0" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                            <option value="">-</option>
                                        </select>
                                    </div>
                                    <div class="col-span-1">
                                        <input type="number" name="items[0][quantity]" min="1" value="1" required onchange="calculateTotal(0)"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-2">
                                    </div>
                                    <div class="col-span-2">
                                        <input type="number" name="items[0][unit_price]" min="0" step="0.01" required onchange="calculateTotal(0)"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-2">
                                    </div>
                                    <div class="col-span-2 flex gap-1">
                                        <input type="text" name="items[0][line_total]" readonly
                                            class="w-full rounded-md border-gray-300 shadow-sm bg-gray-50 focus:border-indigo-500 focus:ring-indigo-500 text-sm px-2">
                                        <button type="button" disabled class="text-gray-300 cursor-not-allowed">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <button type="button" onclick="addRow()" class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                {{ __('إضافة منتج آخر') }}
                            </button>
                        </div>

                        <div class="flex items-center justify-end mt-6 border-t border-gray-200 pt-6">
                            @if($lead)
                                <a href="{{ route('leads.show', $lead->id) }}" class="text-gray-600 hover:text-gray-900 ml-4">{{ __('إلغاء') }}</a>
                            @else
                                <a href="{{ route('orders.index') }}" class="text-gray-600 hover:text-gray-900 ml-4">{{ __('إلغاء') }}</a>
                            @endif
                            <x-primary-button>
                                {{ __('حفظ الطلب') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#lead_id').select2({
                placeholder: "{{ __('اختر عميلاً') }}",
                allowClear: true,
                width: '100%',
                dir: "rtl",
                language: {
                    noResults: function() {
                        return "لا توجد نتائج";
                    }
                }
            });
        });

        // Customer Type Toggle
        function setCustomerType(type) {
            document.getElementById('customer_type').value = type;
            
            const btnExisting = document.getElementById('btn-existing');
            const btnNew = document.getElementById('btn-new');
            const sectionExisting = document.getElementById('section-existing');
            const sectionNew = document.getElementById('section-new');
            
            if (type === 'existing') {
                btnExisting.classList.add('bg-indigo-600', 'text-white', 'shadow-sm');
                btnExisting.classList.remove('bg-gray-100', 'text-gray-600', 'hover:bg-gray-200');
                
                btnNew.classList.remove('bg-indigo-600', 'text-white', 'shadow-sm');
                btnNew.classList.add('bg-gray-100', 'text-gray-600', 'hover:bg-gray-200');
                
                sectionExisting.classList.remove('hidden');
                sectionNew.classList.add('hidden');
            } else {
                btnNew.classList.add('bg-indigo-600', 'text-white', 'shadow-sm');
                btnNew.classList.remove('bg-gray-100', 'text-gray-600', 'hover:bg-gray-200');
                
                btnExisting.classList.remove('bg-indigo-600', 'text-white', 'shadow-sm');
                btnExisting.classList.add('bg-gray-100', 'text-gray-600', 'hover:bg-gray-200');
                
                sectionNew.classList.remove('hidden');
                sectionExisting.classList.add('hidden');
            }
        }

        let rowCount = 1;

        function addRow() {
            const container = document.getElementById('items-container');
            const newRow = `
                <div class="item-row grid grid-cols-12 gap-2 items-end mt-3" id="row-${rowCount}">
                    <div class="col-span-3">
                        <select name="items[${rowCount}][product_id]" onchange="updateProductDetails(this, ${rowCount})" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">{{ __('اختر منتجاً') }}</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" 
                                    data-price="{{ $product->price }}" 
                                    data-name="{{ $product->name }}"
                                    data-sizes="{{ json_encode($product->sizes ?? []) }}"
                                    data-colors="{{ json_encode($product->colors ?? []) }}">
                                    {{ $product->name }} ({{ number_format($product->price, 2) }})
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="items[${rowCount}][product_name]" class="product-name-input">
                    </div>
                    <div class="col-span-2">
                        <select name="items[${rowCount}][size]" id="size-${rowCount}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">-</option>
                        </select>
                    </div>
                    <div class="col-span-2">
                        <select name="items[${rowCount}][color]" id="color-${rowCount}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">-</option>
                        </select>
                    </div>
                    <div class="col-span-1">
                        <input type="number" name="items[${rowCount}][quantity]" min="1" value="1" required onchange="calculateTotal(${rowCount})"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-2">
                    </div>
                    <div class="col-span-2">
                        <input type="number" name="items[${rowCount}][unit_price]" min="0" step="0.01" required onchange="calculateTotal(${rowCount})"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-2">
                    </div>
                    <div class="col-span-2 flex gap-1">
                        <input type="text" name="items[${rowCount}][line_total]" readonly
                            class="w-full rounded-md border-gray-300 shadow-sm bg-gray-50 focus:border-indigo-500 focus:ring-indigo-500 text-sm px-2">
                        <button type="button" onclick="removeRow(${rowCount})" class="text-red-500 hover:text-red-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', newRow);
            rowCount++;
        }

        function removeRow(id) {
            document.getElementById(`row-${id}`).remove();
        }

        function updateProductDetails(select, index) {
            const option = select.options[select.selectedIndex];
            const price = option.getAttribute('data-price');
            const name = option.getAttribute('data-name');
            const sizes = JSON.parse(option.getAttribute('data-sizes') || '[]');
            const colors = JSON.parse(option.getAttribute('data-colors') || '[]');
            
            if (price) {
                document.querySelector(`input[name="items[${index}][unit_price]"]`).value = price;
                document.querySelector(`input[name="items[${index}][product_name]"]`).value = name;
                calculateTotal(index);

                // Update sizes
                const sizeSelect = document.getElementById(`size-${index}`);
                sizeSelect.innerHTML = '<option value="">-</option>';
                if (sizes.length > 0) {
                    sizes.forEach(size => {
                        const opt = document.createElement('option');
                        opt.value = size;
                        opt.text = size;
                        sizeSelect.add(opt);
                    });
                }

                // Update colors
                const colorSelect = document.getElementById(`color-${index}`);
                colorSelect.innerHTML = '<option value="">-</option>';
                if (colors.length > 0) {
                    colors.forEach(color => {
                        const opt = document.createElement('option');
                        opt.value = color;
                        opt.text = color;
                        colorSelect.add(opt);
                    });
                }
            }
        }

        function calculateTotal(index) {
            const quantity = document.querySelector(`input[name="items[${index}][quantity]"]`).value;
            const price = document.querySelector(`input[name="items[${index}][unit_price]"]`).value;
            const total = (quantity * price).toFixed(2);
            document.querySelector(`input[name="items[${index}][line_total]"]`).value = total;
        }
    </script>
    @endpush
</x-app-layout>