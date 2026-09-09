<x-layouts.auth title="Reset password" heading="Reset your password"
                subheading="Give us the address you signed up with and we will send a reset link.">
    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <x-ui.field label="Email address" for="email" :error="$errors->first('email')">
            <x-ui.input id="email" name="email" type="email" value="{{ old('email') }}"
                        autocomplete="username" required autofocus :invalid="$errors->has('email')" />
        </x-ui.field>

        <x-ui.button type="submit" class="w-full">Send reset link</x-ui.button>
    </form>

    <p class="mt-8 border-t border-ink-200 pt-6 text-sm text-ink-600">
        <a href="{{ route('login') }}" class="font-medium text-pine-700 hover:underline">Back to sign in</a>
    </p>
</x-layouts.auth>
