@php /** @var \App\Models\User $student */ @endphp

<div class="grid gap-6 lg:grid-cols-12">
    <div class="lg:col-span-8">
        <x-ui.panel title="Account">
            <div class="space-y-5">
                <x-ui.field label="Full name" for="name" :error="$errors->first('name')" required>
                    <x-ui.input id="name" name="name" value="{{ old('name', $student->name) }}" required
                                :invalid="$errors->has('name')" />
                </x-ui.field>

                <x-ui.field label="Email address" for="email" :error="$errors->first('email')" required>
                    <x-ui.input id="email" name="email" type="email" value="{{ old('email', $student->email) }}"
                                required :invalid="$errors->has('email')" />
                </x-ui.field>

                <x-ui.field label="Password" for="password" :error="$errors->first('password')"
                            :hint="$student->exists ? 'Leave blank to keep the current password.' : 'At least eight characters.'"
                            :required="! $student->exists">
                    <x-ui.input id="password" name="password" type="password" autocomplete="new-password"
                                :required="! $student->exists" :invalid="$errors->has('password')" />
                </x-ui.field>

                <x-ui.field label="Confirm password" for="password_confirmation">
                    <x-ui.input id="password_confirmation" name="password_confirmation" type="password"
                                autocomplete="new-password" />
                </x-ui.field>

                <x-ui.field label="Phone" for="phone" :error="$errors->first('phone')">
                    <x-ui.input id="phone" name="phone" value="{{ old('phone', $student->phone) }}"
                                :invalid="$errors->has('phone')" />
                </x-ui.field>

                <x-ui.field label="Notes" for="bio" :error="$errors->first('bio')">
                    <x-ui.textarea id="bio" name="bio" rows="3"
                                   :invalid="$errors->has('bio')">{{ old('bio', $student->bio) }}</x-ui.textarea>
                </x-ui.field>
            </div>
        </x-ui.panel>
    </div>

    <div class="lg:col-span-4">
        <x-ui.panel title="Access">
            <div class="space-y-5">
                <x-ui.field label="Role" for="role" :error="$errors->first('role')"
                            hint="Administrators can reach the whole admin panel." required>
                    <x-ui.select id="role" name="role" :invalid="$errors->has('role')">
                        @foreach (\App\Enums\UserRole::cases() as $role)
                            <option value="{{ $role->value }}"
                                @selected(old('role', $student->role?->value ?? 'student') === $role->value)>{{ $role->label() }}</option>
                        @endforeach
                    </x-ui.select>
                </x-ui.field>

                <x-ui.checkbox name="email_verified" value="1" label="Mark email as verified"
                               hint="Skips the verification email for accounts you create by hand."
                               :checked="(bool) old('email_verified', $student->hasVerifiedEmail())" />

                <div class="border-t border-ink-200 pt-5">
                    @if ($student->avatar_url)
                        <img src="{{ $student->avatar_url }}" alt="" class="mb-4 size-20 rounded-full object-cover">
                    @endif

                    <x-ui.field label="Profile photo" for="avatar" :error="$errors->first('avatar')">
                        <input id="avatar" name="avatar" type="file" accept="image/*"
                               class="block w-full text-sm text-ink-600 file:mr-3 file:border file:border-ink-300 file:bg-white file:px-3 file:py-1.5 file:text-sm file:text-ink-800 hover:file:bg-sand-100" />
                    </x-ui.field>
                </div>
            </div>
        </x-ui.panel>
    </div>
</div>
