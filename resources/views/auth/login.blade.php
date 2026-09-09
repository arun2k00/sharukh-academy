<x-layouts.auth title="Sign in" heading="Sign in" subheading="Continue where you left off.">
    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <x-ui.field label="Email address" for="email" :error="$errors->first('email')">
            <x-ui.input id="email" name="email" type="email" value="{{ old('email') }}"
                        autocomplete="username" required autofocus :invalid="$errors->has('email')" />
        </x-ui.field>

        <x-ui.field label="Password" for="password" :error="$errors->first('password')">
            <x-ui.input id="password" name="password" type="password"
                        autocomplete="current-password" required :invalid="$errors->has('password')" />
        </x-ui.field>

        <div class="flex items-center justify-between gap-4">
            <x-ui.checkbox name="remember" value="1" label="Stay signed in" :checked="(bool) old('remember')" />
            <a href="{{ route('password.request') }}" class="text-[0.8125rem] text-pine-700 hover:underline">
                Forgot password?
            </a>
        </div>

        <x-ui.button type="submit" class="w-full">Sign in</x-ui.button>
    </form>

    <p class="mt-8 border-t border-ink-200 pt-6 text-sm text-ink-600">
        New here?
        <a href="{{ route('register') }}" class="font-medium text-pine-700 hover:underline">Create an account</a>
    </p>
</x-layouts.auth>
