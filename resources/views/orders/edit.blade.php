<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('تعديل الطلب') }} #{{ $order->id }} - {{ $order->lead->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('orders.update', $order->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <!-- Payment Method -->
                            <div>
                                <x-input-label for="payment_method" :value="__('طريقة الدفع')" />
                                <select id="payment_method" name="payment_method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="">{{ __('اختر طريقة الدفع') }}</option>
                                    @foreach(['Cash', 'Transfer', 'Online', 'COD'] as $method)
                                        <option value="{{ $method }}" {{ old('payment_method', $order->payment_method) == $method ? 'selected' : '' }}>
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
                                    <option value="Pending" {{ old('order_status', $order->order_status) == 'Pending' ? 'selected' : '' }}>{{ __('orders.status.Pending') }}</option>
                                    <option value="Confirmed" {{ old('order_status', $order->order_status) == 'Confirmed' ? 'selected' : '' }}>{{ __('orders.status.Confirmed') }}</option>
                                    <option value="Shipped" {{ old('order_status', $order->order_status) == 'Shipped' ? 'selected' : '' }}>{{ __('orders.status.Shipped') }}</option>
                                    <option value="Cancelled" {{ old('order_status', $order->order_status) == 'Cancelled' ? 'selected' : '' }}>{{ __('orders.status.Cancelled') }}</option>
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('order_status')" />
                            </div>

                            <!-- Notes -->
                            <div class="col-span-full">
                                <x-input-label for="notes" :value="__('ملاحظات')" />
                                <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes', $order->notes) }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('notes')" />
                            </div>
                        </div>

                        <!-- Order Items -->
                        <div class="mt-8">
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
                                
                                @foreach($order->items as $index => $item)
                                <div class="item-row grid grid-cols-12 gap-2 items-end mt-3" id="row-{{ $index }}">
                                    <div class="col-span-3">
                                        <select name="items[{{ $index }}][product_id]" onchange="updateProductDetails(this, {{ $index }})" required
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                            <option value="">{{ __('اختر منتجاً') }}</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" {{ $item->product_id == $product->id ? 'selected' : '' }} 
                                                    data-price="{{ $product->price }}" 
                                                    data-name="{{ $product->name }}"
                                                    data-sizes="{{ json_encode($product->sizes ?? []) }}"
                                                    data-colors="{{ json_encode($product->colors ?? []) }}">
                                                    {{ $product->name }} ({{ number_format($product->price, 2) }} ر.س)
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="items[{{ $index }}][product_name]" value="{{ $item->product_name }}" class="product-name-input">
                                    </div>
                                    <div class="col-span-2">
                                        <select name="items[{{ $index }}][size]" id="size-{{ $index }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                            <option value="">-</option>
                                            @if($item->product && $item->product->sizes)
                                                @foreach($item->product->sizes as $size)
                                                    <option value="{{ $size }}" {{ $item->size == $size ? 'selected' : '' }}>{{ $size }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-span-2">
                                        <select name="items[{{ $index }}][color]" id="color-{{ $index }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                            <option value="">-</option>
                                            @if($item->product && $item->product->colors)
                                                @foreach($item->product->colors as $color)
                                                    <option value="{{ $color }}" {{ $item->color == $color ? 'selected' : '' }}>{{ $color }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-span-1">
                                        <input type="number" name="items[{{ $index }}][quantity]" min="1" value="{{ $item->quantity }}" required onchange="calculateTotal({{ $index }})"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-2">
                                    </div>
                                    <div class="col-span-2">
                                        <input type="number" name="items[{{ $index }}][unit_price]" min="0" step="0.01" value="{{ $item->unit_price }}" required onchange="calculateTotal({{ $index }})"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-2">
                                    </div>
                                    <div class="col-span-2 flex gap-1">
                                        <input type="text" name="items[{{ $index }}][line_total]" value="{{ number_format($item->quantity * $item->unit_price, 2, '.', '') }}" readonly
                                            class="w-full rounded-md border-gray-300 shadow-sm bg-gray-50 focus:border-indigo-500 focus:ring-indigo-500 text-sm px-2">
                                        <button type="button" onclick="removeRow({{ $index }})" class="text-red-500 hover:text-red-700">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <button type="button" onclick="addRow()" class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                {{ __('إضافة منتج آخر') }}
                            </button>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('orders.show', $order->id) }}" class="text-gray-600 hover:text-gray-900 ml-4">{{ __('إلغاء') }}</a>
                            <x-primary-button>
                                {{ __('تحديث الطلب') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let rowCount = {{ $order->items->count() > 0 ? $order->items->count() : 0 }};
        // Ensure rowCount is at least 1 to avoid conflicts if we add new rows
        rowCount = Math.max(rowCount, {{ $order->items->count() }});

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
                                    {{ $product->name }} ({{ number_format($product->price, 2) }} ر.س)
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
            const row = document.getElementById(`row-${id}`);
            if(row) row.remove();
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
                sizes.forEach(size => {
                    const opt = document.createElement('option');
                    opt.value = size;
                    opt.innerText = size;
                    sizeSelect.appendChild(opt);
                });

                // Update colors
                const colorSelect = document.getElementById(`color-${index}`);
                colorSelect.innerHTML = '<option value="">-</option>';
                colors.forEach(color => {
                    const opt = document.createElement('option');
                    opt.value = color;
                    opt.innerText = color;
                    colorSelect.appendChild(opt);
                });
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