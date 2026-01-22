<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'city' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'assigned_to' => 'nullable|exists:users,id',
            'status' => ['required', Rule::in(['New', 'Contacted', 'Interested', 'Confirmed', 'Shipped', 'Cancelled'])],
            'follow_up_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ];
    }

    public function attributes(): array
    {
        return [
            'customer_name' => 'اسم العميل',
            'phone' => 'رقم الهاتف',
            'email' => 'البريد الإلكتروني',
            'city' => 'المدينة',
            'address' => 'العنوان',
            'campaign_id' => 'الحملة',
            'assigned_to' => 'المسؤول',
            'status' => 'الحالة',
            'follow_up_date' => 'تاريخ المتابعة',
            'notes' => 'ملاحظات',
        ];
    }
}
