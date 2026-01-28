<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_type' => 'nullable|in:existing,new',
            'lead_id' => 'required_if:customer_type,existing|nullable|exists:leads,id',
            'customer_name' => 'required_if:customer_type,new|nullable|string|max:255',
            'phone' => 'required_if:customer_type,new|nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'city' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'payment_method' => ['required', Rule::in(['Cash', 'Transfer', 'Online', 'COD'])],
            'order_status' => ['required', Rule::in(['Pending', 'Confirmed', 'Shipped', 'Cancelled'])],
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.product_name' => 'required|string|max:255',
            'items.*.variant' => 'nullable|string|max:255',
            'items.*.size' => 'nullable|string|max:255',
            'items.*.color' => 'nullable|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ];
    }

    public function attributes(): array
    {
        return [
            'customer_type' => 'نوع العميل',
            'lead_id' => 'العميل',
            'customer_name' => 'اسم العميل',
            'phone' => 'رقم الهاتف',
            'payment_method' => 'طريقة الدفع',
            'order_status' => 'حالة الطلب',
            'notes' => 'ملاحظات',
            'items' => 'المنتجات',
            'items.*.product_name' => 'اسم المنتج',
            'items.*.variant' => 'المتغير',
            'items.*.size' => 'المقاس',
            'items.*.color' => 'اللون',
            'items.*.quantity' => 'الكمية',
            'items.*.unit_price' => 'سعر الوحدة',
        ];
    }
}
