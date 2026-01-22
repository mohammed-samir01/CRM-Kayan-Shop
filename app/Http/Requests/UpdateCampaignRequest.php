<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('campaigns')->ignore($this->route('campaign'))],
            'platform' => ['required', Rule::in(['TikTok', 'Facebook', 'Instagram', 'Google', 'Snapchat', 'X', 'YouTube', 'LinkedIn', 'Other'])],
            'ad_type' => ['required', Rule::in(['Video', 'Image', 'Carousel', 'Search', 'Story', 'Other'])],
            'source' => ['required', Rule::in(['Form', 'WhatsApp', 'Phone Call', 'Website', 'DM', 'Other'])],
            'notes' => 'nullable|string',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'اسم الحملة',
            'platform' => 'المنصة',
            'ad_type' => 'نوع الإعلان',
            'source' => 'المصدر',
            'notes' => 'ملاحظات',
        ];
    }
}
