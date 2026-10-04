<x-layouts.admin title="Payment settings" heading="Settings">
    <x-admin.page-header title="Payment gateways"
                         description="Credentials entered here are encrypted and take effect immediately. No deploy or config cache needed." />

    <form method="POST" action="{{ route('admin.settings.payments.update') }}">
        @csrf
        @method('PUT')

        <div class="space-y-6">
            <x-ui.panel title="Razorpay" description="Hosted checkout via Razorpay Payment Links.">
                <div class="space-y-5">
                    <x-ui.checkbox name="razorpay_enabled" value="1" label="Accept payments via Razorpay"
                                   :checked="(bool) old('razorpay_enabled', $razorpayEnabled)" />

                    <x-ui.field label="Key ID" for="razorpay_key_id" :error="$errors->first('razorpay_key_id')">
                        <x-ui.input id="razorpay_key_id" name="razorpay_key_id"
                                    value="{{ old('razorpay_key_id', $razorpayKeyId) }}"
                                    :invalid="$errors->has('razorpay_key_id')" />
                    </x-ui.field>

                    <x-ui.field label="Key secret" for="razorpay_key_secret" :error="$errors->first('razorpay_key_secret')"
                                :hint="$razorpayHasSecret ? 'A secret is already saved. Leave blank to keep it.' : 'Not set yet.'">
                        <x-ui.input id="razorpay_key_secret" name="razorpay_key_secret" type="password"
                                    placeholder="{{ $razorpayHasSecret ? '••••••••••••' : '' }}"
                                    :invalid="$errors->has('razorpay_key_secret')" />
                    </x-ui.field>

                    <x-ui.field label="Webhook secret" for="razorpay_webhook_secret" :error="$errors->first('razorpay_webhook_secret')"
                                :hint="($razorpayHasWebhookSecret ? 'A secret is already saved. Leave blank to keep it. ' : 'Not set yet. ').'From Razorpay Dashboard → Webhooks.'">
                        <x-ui.input id="razorpay_webhook_secret" name="razorpay_webhook_secret" type="password"
                                    placeholder="{{ $razorpayHasWebhookSecret ? '••••••••••••' : '' }}"
                                    :invalid="$errors->has('razorpay_webhook_secret')" />
                    </x-ui.field>

                    <p class="text-[0.8125rem] text-ink-500">
                        Webhook URL: <code class="bg-sand-100 px-1.5 py-0.5">{{ route('webhooks.razorpay') }}</code>
                    </p>
                </div>
            </x-ui.panel>

            <x-ui.panel title="PhonePe" description="Hosted checkout via PhonePe Standard Checkout.">
                <div class="space-y-5">
                    <x-ui.checkbox name="phonepe_enabled" value="1" label="Accept payments via PhonePe"
                                   :checked="(bool) old('phonepe_enabled', $phonePeEnabled)" />

                    <x-ui.field label="Merchant ID" for="phonepe_merchant_id" :error="$errors->first('phonepe_merchant_id')">
                        <x-ui.input id="phonepe_merchant_id" name="phonepe_merchant_id"
                                    value="{{ old('phonepe_merchant_id', $phonePeMerchantId) }}"
                                    :invalid="$errors->has('phonepe_merchant_id')" />
                    </x-ui.field>

                    <x-ui.field label="Salt key" for="phonepe_salt_key" :error="$errors->first('phonepe_salt_key')"
                                :hint="$phonePeHasSaltKey ? 'A salt key is already saved. Leave blank to keep it.' : 'Not set yet.'">
                        <x-ui.input id="phonepe_salt_key" name="phonepe_salt_key" type="password"
                                    placeholder="{{ $phonePeHasSaltKey ? '••••••••••••' : '' }}"
                                    :invalid="$errors->has('phonepe_salt_key')" />
                    </x-ui.field>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <x-ui.field label="Salt index" for="phonepe_salt_index" :error="$errors->first('phonepe_salt_index')">
                            <x-ui.input id="phonepe_salt_index" name="phonepe_salt_index"
                                        value="{{ old('phonepe_salt_index', $phonePeSaltIndex) }}"
                                        :invalid="$errors->has('phonepe_salt_index')" />
                        </x-ui.field>

                        <x-ui.field label="Environment" for="phonepe_environment" :error="$errors->first('phonepe_environment')">
                            <x-ui.select id="phonepe_environment" name="phonepe_environment" :invalid="$errors->has('phonepe_environment')">
                                <option value="sandbox" @selected(old('phonepe_environment', $phonePeEnvironment) === 'sandbox')>Sandbox</option>
                                <option value="production" @selected(old('phonepe_environment', $phonePeEnvironment) === 'production')>Production</option>
                            </x-ui.select>
                        </x-ui.field>
                    </div>

                    <p class="text-[0.8125rem] text-ink-500">
                        Webhook / callback URL: <code class="bg-sand-100 px-1.5 py-0.5">{{ route('webhooks.phonepe') }}</code>
                    </p>
                </div>
            </x-ui.panel>
        </div>

        <div class="mt-6">
            <x-ui.button type="submit">Save payment settings</x-ui.button>
        </div>
    </form>
</x-layouts.admin>
