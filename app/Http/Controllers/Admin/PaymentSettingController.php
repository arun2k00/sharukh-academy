<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PaymentSettingsRequest;
use App\Services\Payments\PaymentSettings;
use App\Services\SettingsRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PaymentSettingController extends Controller
{
    public function __construct(
        private readonly SettingsRepository $settings,
        private readonly PaymentSettings $paymentSettings,
    ) {}

    public function edit(): View
    {
        return view('admin.settings.payments', [
            'razorpayEnabled' => $this->paymentSettings->razorpayEnabled(),
            'razorpayKeyId' => $this->settings->get('razorpay_key_id'),
            'razorpayHasSecret' => filled($this->settings->get('razorpay_key_secret')),
            'razorpayHasWebhookSecret' => filled($this->settings->get('razorpay_webhook_secret')),
            'phonePeEnabled' => $this->paymentSettings->phonePeEnabled(),
            'phonePeMerchantId' => $this->settings->get('phonepe_merchant_id'),
            'phonePeHasSaltKey' => filled($this->settings->get('phonepe_salt_key')),
            'phonePeSaltIndex' => $this->paymentSettings->phonePeSaltIndex(),
            'phonePeEnvironment' => $this->paymentSettings->phonePeEnvironment(),
        ]);
    }

    public function update(PaymentSettingsRequest $request): RedirectResponse
    {
        $values = [
            'razorpay_enabled' => $request->boolean('razorpay_enabled') ? '1' : '0',
            'razorpay_key_id' => $request->input('razorpay_key_id'),
            'phonepe_enabled' => $request->boolean('phonepe_enabled') ? '1' : '0',
            'phonepe_merchant_id' => $request->input('phonepe_merchant_id'),
            'phonepe_salt_index' => $request->input('phonepe_salt_index', '1'),
            'phonepe_environment' => $request->input('phonepe_environment', 'sandbox'),
        ];

        // Secret fields only overwrite the stored value when the admin
        // actually typed a replacement — the form never redisplays them, so
        // a blank field means "keep what's already saved", not "clear it".
        foreach ([
            'razorpay_key_secret' => 'razorpay_key_secret',
            'razorpay_webhook_secret' => 'razorpay_webhook_secret',
            'phonepe_salt_key' => 'phonepe_salt_key',
        ] as $input => $settingKey) {
            if ($request->filled($input)) {
                $values[$settingKey] = $this->paymentSettings->encrypt($request->input($input));
            }
        }

        $this->settings->setMany($values, group: 'payments');

        return redirect()->route('admin.settings.payments.edit')->with('status', 'Payment settings saved.');
    }
}
