<x-layouts.auth title="Choose a new password" heading="Choose a new password">
    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <x-ui.field label="Email address" for="email" :error="$errors->first('email')">
            <x-ui.input id="email" name="email" type="email" value="{{ old('email', $email) }}"
                        autocomplete="username" required :invalid="$errors->has('email')" />
        </x-ui.field>

        <x-ui.field label="New password" for="password" :error="$errors->first('password')"
                    hint="At least eight characters." required>
            <x-ui.input id="password" name="password" type="password"
                        autocomplete="new-password" required autofocus :invalid="$errors->has('password')" />
        </x-ui.field>

        <x-ui.field label="Confirm new password" for="password_confirmation" required>
            <x-ui.input id="password_confirmation" name="password_confirmation" type="password"
                        autocomplete="new-password" required />
        </x-ui.field>

        <x-ui.button type="submit" class="w-full">Save new password</x-ui.button>
    </form>
</x-layouts.auth>
