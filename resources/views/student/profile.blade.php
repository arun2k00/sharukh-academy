<x-layouts.app title="Profile" heading="Your profile"
               description="Update your details and change your password.">

    <div class="grid gap-8 lg:grid-cols-12 lg:gap-10">
        <div class="space-y-8 lg:col-span-7">
            <x-ui.panel title="Personal details" description="This is the name shown on your certificates.">
                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    @method('PATCH')

                    <div class="flex items-center gap-5">
                        <x-ui.avatar :src="$user->avatar_url" :name="$user->name" size="lg" />
                        <div class="min-w-0 flex-1">
                            <x-ui.field label="Profile photo" for="avatar" :error="$errors->first('avatar')"
                                        hint="JPG, PNG or WebP, up to 4 MB.">
                                <input id="avatar" name="avatar" type="file" accept="image/*"
                                       class="block w-full text-sm text-ink-600 file:mr-3 file:border file:border-ink-300 file:bg-white file:px-3 file:py-1.5 file:text-sm file:text-ink-800 hover:file:bg-sand-100" />
                            </x-ui.field>
                        </div>
                    </div>

                    @if ($user->avatar_path)
                        <div class="-mt-2">
                            <button type="submit" form="remove-avatar"
                                    class="text-[0.8125rem] text-ink-500 underline underline-offset-2 hover:text-red-700">
                                Remove current photo
                            </button>
                        </div>
                    @endif

                    <x-ui.field label="Full name" for="name" :error="$errors->first('name')" required>
                        <x-ui.input id="name" name="name" value="{{ old('name', $user->name) }}" required
                                    :invalid="$errors->has('name')" />
                    </x-ui.field>

                    <x-ui.field label="Email address" for="email"
                                hint="Contact the academy if you need to change the address on your account.">
                        <x-ui.input id="email" type="email" value="{{ $user->email }}" disabled />
                    </x-ui.field>

                    <x-ui.field label="Phone" for="phone" :error="$errors->first('phone')">
                        <x-ui.input id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                                    :invalid="$errors->has('phone')" />
                    </x-ui.field>

                    <x-ui.field label="About you" for="bio" :error="$errors->first('bio')"
                                hint="Optional. Helps your instructor pitch the live sessions.">
                        <x-ui.textarea id="bio" name="bio" rows="4" :invalid="$errors->has('bio')">{{ old('bio', $user->bio) }}</x-ui.textarea>
                    </x-ui.field>

                    <x-ui.button type="submit">Save changes</x-ui.button>
                </form>
            </x-ui.panel>

            <x-ui.panel title="Password" description="Choose something you do not use anywhere else.">
                <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <x-ui.field label="Current password" for="current_password"
                                :error="$errors->updatePassword->first('current_password')" required>
                        <x-ui.input id="current_password" name="current_password" type="password"
                                    autocomplete="current-password" required
                                    :invalid="$errors->updatePassword->has('current_password')" />
                    </x-ui.field>

                    <x-ui.field label="New password" for="new_password" :error="$errors->updatePassword->first('password')"
                                hint="At least eight characters." required>
                        <x-ui.input id="new_password" name="password" type="password"
                                    autocomplete="new-password" required
                                    :invalid="$errors->updatePassword->has('password')" />
                    </x-ui.field>

                    <x-ui.field label="Confirm new password" for="password_confirmation" required>
                        <x-ui.input id="password_confirmation" name="password_confirmation" type="password"
                                    autocomplete="new-password" required />
                    </x-ui.field>

                    <x-ui.button type="submit">Change password</x-ui.button>
                </form>
            </x-ui.panel>
        </div>

        <div class="lg:col-span-5">
            <x-ui.panel title="Account">
                <dl class="space-y-4 text-sm">
                    <div class="flex justify-between gap-4">
                        <dt class="text-ink-500">Email</dt>
                        <dd class="text-right text-ink-900">{{ $user->email }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-ink-500">Email verified</dt>
                        <dd class="text-right text-ink-900">
                            {{ $user->hasVerifiedEmail() ? $user->email_verified_at->format('j M Y') : 'Not yet' }}
                        </dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-ink-500">Member since</dt>
                        <dd class="text-right text-ink-900">{{ $user->created_at->format('j M Y') }}</dd>
                    </div>
                </dl>
            </x-ui.panel>
        </div>
    </div>

    {{-- Kept outside the profile form so the two submissions never collide. --}}
    <form id="remove-avatar" method="POST" action="{{ route('profile.avatar.destroy') }}" class="hidden">
        @csrf
        @method('DELETE')
    </form>
</x-layouts.app>
