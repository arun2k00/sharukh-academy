<x-layouts.admin title="Settings" heading="Settings">
    <x-admin.page-header title="Platform settings"
                         description="Copy and contact details used across the public website. Technical configuration lives in the environment file." />

    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid gap-6 lg:grid-cols-12">
            <div class="space-y-6 lg:col-span-8">
                <x-ui.panel title="Branding" description="Updates the logo, favicon and accent colours across the site immediately — no code changes needed.">
                    <div class="space-y-5">
                        <x-ui.field label="Academy name" for="academy_name" :error="$errors->first('academy_name')"
                                    hint="Shown in page titles and emails.">
                            <x-ui.input id="academy_name" name="academy_name" value="{{ old('academy_name', $settings['academy_name'] ?? config('academy.name')) }}"
                                        :invalid="$errors->has('academy_name')" />
                        </x-ui.field>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                @if ($settings['logo_url'] ?? null)
                                    <img src="{{ $settings['logo_url'] }}" alt="" class="mb-2 h-10">
                                @endif
                                <x-ui.field label="Logo" for="logo" :error="$errors->first('logo')" hint="PNG, JPG or SVG.">
                                    <input id="logo" name="logo" type="file" accept="image/png,image/jpeg,image/svg+xml"
                                           class="block w-full text-sm text-ink-600 file:mr-3 file:border file:border-ink-300 file:bg-white file:px-3 file:py-1.5 file:text-sm file:text-ink-800 hover:file:bg-sand-100" />
                                </x-ui.field>
                            </div>
                            <div>
                                @if ($settings['favicon_url'] ?? null)
                                    <img src="{{ $settings['favicon_url'] }}" alt="" class="mb-2 size-8">
                                @endif
                                <x-ui.field label="Favicon" for="favicon" :error="$errors->first('favicon')" hint="Square PNG or JPG works best.">
                                    <input id="favicon" name="favicon" type="file" accept="image/png,image/jpeg"
                                           class="block w-full text-sm text-ink-600 file:mr-3 file:border file:border-ink-300 file:bg-white file:px-3 file:py-1.5 file:text-sm file:text-ink-800 hover:file:bg-sand-100" />
                                </x-ui.field>
                            </div>
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <x-ui.field label="Primary colour" for="color_primary" :error="$errors->first('color_primary')"
                                        hint="Used for buttons and links.">
                                <input type="color" id="color_primary" name="color_primary"
                                       value="{{ old('color_primary', $settings['color_primary'] ?? '#1b4331') }}" class="h-[42px] w-24 border border-ink-300">
                            </x-ui.field>
                            <x-ui.field label="Secondary colour" for="color_secondary" :error="$errors->first('color_secondary')"
                                        hint="Used for the secondary button style.">
                                <input type="color" id="color_secondary" name="color_secondary"
                                       value="{{ old('color_secondary', $settings['color_secondary'] ?? '#1a1a18') }}" class="h-[42px] w-24 border border-ink-300">
                            </x-ui.field>
                        </div>
                    </div>
                </x-ui.panel>

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
