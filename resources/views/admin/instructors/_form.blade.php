@php $credentialsText = $instructor->credentials ? implode("\n", $instructor->credentials) : ''; @endphp

<div class="grid gap-6 lg:grid-cols-12">
    <div class="lg:col-span-8">
        <x-ui.panel title="Profile">
            <div class="space-y-5">
                <x-ui.field label="Name" for="name" :error="$errors->first('name')" required>
                    <x-ui.input id="name" name="name" value="{{ old('name', $instructor->name) }}" required
                                :invalid="$errors->has('name')" />
                </x-ui.field>

                <x-ui.field label="URL slug" for="slug" :error="$errors->first('slug')"
                            hint="Leave blank to generate it from the name.">
                    <x-ui.input id="slug" name="slug" value="{{ old('slug', $instructor->slug) }}"
                                :invalid="$errors->has('slug')" />
                </x-ui.field>

                <x-ui.field label="Headline" for="headline" :error="$errors->first('headline')"
                            hint="One line, shown under the name.">
                    <x-ui.input id="headline" name="headline" value="{{ old('headline', $instructor->headline) }}"
                                :invalid="$errors->has('headline')" />
                </x-ui.field>

                <x-ui.field label="Biography" for="bio" :error="$errors->first('bio')"
                            hint="Separate paragraphs with a blank line.">
                    <x-ui.textarea id="bio" name="bio" rows="8"
                                   :invalid="$errors->has('bio')">{{ old('bio', $instructor->bio) }}</x-ui.textarea>
                </x-ui.field>

                <x-ui.field label="Credentials" for="credentials" :error="$errors->first('credentials')"
                            hint="One per line.">
                    <x-ui.textarea id="credentials" name="credentials" rows="5"
                                   :invalid="$errors->has('credentials')">{{ old('credentials', $credentialsText) }}</x-ui.textarea>
                </x-ui.field>
            </div>
        </x-ui.panel>
    </div>

    <div class="lg:col-span-4">
        <x-ui.panel title="Details">
            <div class="space-y-5">
                @if ($instructor->avatar_url)
                    <img src="{{ $instructor->avatar_url }}" alt="" class="aspect-[4/5] w-full border border-ink-200 object-cover">
                @endif

                <x-ui.field label="Photo" for="avatar" :error="$errors->first('avatar')" hint="Portrait crop works best.">
                    <input id="avatar" name="avatar" type="file" accept="image/*"
                           class="block w-full text-sm text-ink-600 file:mr-3 file:border file:border-ink-300 file:bg-white file:px-3 file:py-1.5 file:text-sm file:text-ink-800 hover:file:bg-sand-100" />
                </x-ui.field>

                <x-ui.field label="Linked trainer account" for="user_id" :error="$errors->first('user_id')"
                            hint="Lets this person sign in and manage their own courses from the Tutor dashboard.">
                    <x-ui.select id="user_id" name="user_id" :invalid="$errors->has('user_id')">
                        <option value="">— No login (profile only) —</option>
                        @foreach ($trainers as $trainer)
                            <option value="{{ $trainer->id }}" @selected((string) old('user_id', $instructor->user_id) === (string) $trainer->id)>
                                {{ $trainer->name }} ({{ $trainer->email }})
                            </option>
                        @endforeach
                    </x-ui.select>
                </x-ui.field>

                <x-ui.field label="Years in practice" for="experience_years" :error="$errors->first('experience_years')">
                    <x-ui.input id="experience_years" name="experience_years" type="number" min="0" max="80"
                                value="{{ old('experience_years', $instructor->experience_years) }}"
                                :invalid="$errors->has('experience_years')" />
                </x-ui.field>

                <x-ui.field label="LinkedIn" for="linkedin_url" :error="$errors->first('linkedin_url')">
                    <x-ui.input id="linkedin_url" name="linkedin_url" value="{{ old('linkedin_url', $instructor->linkedin_url) }}"
                                placeholder="https://www.linkedin.com/in/..." :invalid="$errors->has('linkedin_url')" />
                </x-ui.field>

                <x-ui.field label="Website" for="website_url" :error="$errors->first('website_url')">
                    <x-ui.input id="website_url" name="website_url" value="{{ old('website_url', $instructor->website_url) }}"
                                :invalid="$errors->has('website_url')" />
                </x-ui.field>

                <x-ui.checkbox name="is_featured" value="1" label="Feature on the homepage"
                               hint="The featured instructor appears in the homepage biography section."
                               :checked="(bool) old('is_featured', $instructor->is_featured)" />
            </div>
        </x-ui.panel>
    </div>
</div>
