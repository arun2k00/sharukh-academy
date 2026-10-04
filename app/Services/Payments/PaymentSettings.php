<?php

namespace App\Services\Payments;

use App\Services\SettingsRepository;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

/**
 * Gateway credentials, editable from the admin dashboard instead of the
 * environment file.
 *
 * Secrets are encrypted before they reach the `settings` table and decrypted
 * only here — never redisplayed in the edit form. Falls back to
 * `config/services.php` (populated from `.env`) so a self-hoster who never
 * opens the admin screen can still configure gateways the old way.
 */
class PaymentSettings
{
    public function __construct(
        private readonly SettingsRepository $settings,
    ) {}

    public function razorpayEnabled(): bool
    {
        return $this->settings->get('razorpay_enabled') === '1';
    }

    public function razorpayKeyId(): ?string
    {
        return $this->settings->get('razorpay_key_id') ?? config('services.razorpay.key');
    }

    public function razorpayKeySecret(): ?string
    {
        return $this->decrypt($this->settings->get('razorpay_key_secret')) ?? config('services.razorpay.secret');
    }

    public function razorpayWebhookSecret(): ?string
    {
        return $this->decrypt($this->settings->get('razorpay_webhook_secret')) ?? config('services.razorpay.webhook_secret');
    }

    public function phonePeEnabled(): bool
    {
        return $this->settings->get('phonepe_enabled') === '1';
    }

    public function phonePeMerchantId(): ?string
    {
        return $this->settings->get('phonepe_merchant_id') ?? config('services.phonepe.merchant_id');
    }

    public function phonePeSaltKey(): ?string
    {
        return $this->decrypt($this->settings->get('phonepe_salt_key')) ?? config('services.phonepe.salt_key');
    }

    public function phonePeSaltIndex(): string
    {
        return $this->settings->get('phonepe_salt_index') ?? (string) config('services.phonepe.salt_index', '1');
    }

    public function phonePeEnvironment(): string
    {
        return $this->settings->get('phonepe_environment') ?? (string) config('services.phonepe.env', 'sandbox');
    }

    /**
     * Encrypt a secret for storage. Blank input clears the stored value
     * instead of encrypting an empty string, so an admin can leave the
     * "replace secret" field empty to keep what's already saved.
     */
    public function encrypt(?string $value): ?string
    {
        return filled($value) ? Crypt::encryptString($value) : null;
    }

    private function decrypt(?string $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        try {
            return Crypt::decryptString($value);
        } catch (DecryptException) {
            return null;
        }
    }
}
