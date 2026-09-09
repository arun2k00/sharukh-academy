<x-layouts.admin title="Settings" heading="Settings">
    <x-admin.page-header title="Platform settings"
                         description="Copy and contact details used across the public website. Technical configuration lives in the environment file." />

    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf
        @method('PUT')

        <div class="grid gap-6 lg:grid-cols-12">
            <div class="space-y-6 lg:col-span-8">
                <x-ui.panel title="Homepage">
                    <div class="space-y-5">
                        <x-ui.field label="Hero heading" for="hero_heading" :error="$errors->first('hero_heading')">
                            <x-ui.input id="hero_heading" name="hero_heading"
                                        value="{{ old('hero_heading', $settings['hero_heading'] ?? '') }}"
                                        :invalid="$errors->has('hero_heading')" />
                        </x-ui.field>

                        <x-ui.field label="Hero paragraph" for="hero_subheading" :error="$errors->first('hero_subheading')">
                            <x-ui.textarea id="hero_subheading" name="hero_subheading" rows="4"
                                           :invalid="$errors->has('hero_subheading')">{{ old('hero_subheading', $settings['hero_subheading'] ?? '') }}</x-ui.textarea>
                        </x-ui.field>

                        <x-ui.field label="Site tagline" for="site_tagline" :error="$errors->first('site_tagline')"
                                    hint="Used as the meta description on pages that do not set their own.">
                            <x-ui.input id="site_tagline" name="site_tagline"
                                        value="{{ old('site_tagline', $settings['site_tagline'] ?? '') }}"
                                        :invalid="$errors->has('site_tagline')" />
                        </x-ui.field>

                        <x-ui.field label="Footer note" for="footer_note" :error="$errors->first('footer_note')">
                            <x-ui.textarea id="footer_note" name="footer_note" rows="3"
                                           :invalid="$errors->has('footer_note')">{{ old('footer_note', $settings['footer_note'] ?? '') }}</x-ui.textarea>
                        </x-ui.field>
                    </div>
                </x-ui.panel>

                <x-ui.panel title="Contact details">
                    <div class="space-y-5">
                        <x-ui.field label="Email address" for="contact_email" :error="$errors->first('contact_email')">
                            <x-ui.input id="contact_email" name="contact_email" type="email"
                                        value="{{ old('contact_email', $settings['contact_email'] ?? '') }}"
                                        :invalid="$errors->has('contact_email')" />
                        </x-ui.field>

                        <x-ui.field label="Phone" for="contact_phone" :error="$errors->first('contact_phone')">
                            <x-ui.input id="contact_phone" name="contact_phone"
                                        value="{{ old('contact_phone', $settings['contact_phone'] ?? '') }}"
                                        :invalid="$errors->has('contact_phone')" />
                        </x-ui.field>

                        <x-ui.field label="Address" for="contact_address" :error="$errors->first('contact_address')">
                            <x-ui.textarea id="contact_address" name="contact_address" rows="3"
                                           :invalid="$errors->has('contact_address')">{{ old('contact_address', $settings['contact_address'] ?? '') }}</x-ui.textarea>
                        </x-ui.field>
                    </div>
                </x-ui.panel>
            </div>

            <div class="lg:col-span-4">
                <x-ui.panel title="Social links" description="Leave a field blank to hide that link in the footer.">
                    <div class="space-y-5">
                        @foreach ([
                            'social_linkedin' => 'LinkedIn',
                            'social_youtube' => 'YouTube',
                            'social_instagram' => 'Instagram',
                            'social_x' => 'X',
                        ] as $field => $label)
                            <x-ui.field :label="$label" :for="$field" :error="$errors->first($field)">
                                <x-ui.input :id="$field" :name="$field"
                                            value="{{ old($field, $settings[$field] ?? '') }}"
                                            placeholder="https://" :invalid="$errors->has($field)" />
                            </x-ui.field>
                        @endforeach
                    </div>
                </x-ui.panel>
            </div>
        </div>

        <div class="mt-6">
            <x-ui.button type="submit">Save settings</x-ui.button>
        </div>
    </form>
</x-layouts.admin>
