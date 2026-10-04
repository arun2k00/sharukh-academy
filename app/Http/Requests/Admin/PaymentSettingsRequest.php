<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PaymentSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    /**
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'razorpay_enabled' => ['nullable', 'boolean'],
            'razorpay_key_id' => ['nullable', 'string', 'max:255'],
            'razorpay_key_secret' => ['nullable', 'string', 'max:255'],
            'razorpay_webhook_secret' => ['nullable', 'string', 'max:255'],

            'phonepe_enabled' => ['nullable', 'boolean'],
            'phonepe_merchant_id' => ['nullable', 'string', 'max:255'],
            'phonepe_salt_key' => ['nullable', 'string', 'max:255'],
            'phonepe_salt_index' => ['nullable', 'string', 'max:10'],
            'phonepe_environment' => ['nullable', 'in:sandbox,production'],
        ];
    }
}
