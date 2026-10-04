<x-layouts.auth title="Create account" heading="Create your account"
                subheading="It takes a minute, and gives you the full curriculum and preview lessons.">
    @if (config('academy.demo_login_enabled'))
        <div class="mb-8 border border-dashed border-ink-300 bg-sand-50 p-5">
            <p class="mt-1 text-[0.8125rem] text-ink-600">Preview the experience with a demo account. Nothing you do can be saved or changed.</p>

            <div class="mt-4 grid grid-cols-2 gap-2">
                <form method="POST" action="{{ route('demo-login', 'trainer') }}">
                    @csrf
                    <x-ui.button type="submit" variant="outline" size="sm" class="w-full">New Trainer (Demo)</x-ui.button>
                </form>
                <form method="POST" action="{{ route('demo-login', 'student') }}">
                    @csrf
                    <x-ui.button type="submit" variant="outline" size="sm" class="w-full">Existing Student (Demo)</x-ui.button>
                </form>
            </div>
        </div>

        <div class="mb-6 flex items-center gap-3 text-[0.75rem] text-ink-400">
            <span class="h-px flex-1 bg-ink-200"></span>
            Or create your own account
            <span class="h-px flex-1 bg-ink-200"></span>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <x-ui.field label="Full name" for="name" :error="$errors->first('name')" required>
            <x-ui.input id="name" name="name" value="{{ old('name') }}" autocomplete="name"
                        required autofocus :invalid="$errors->has('name')" />
        </x-ui.field>

        <x-ui.field label="Email address" for="email" :error="$errors->first('email')" required>
            <x-ui.input id="email" name="email" type="email" value="{{ old('email') }}"
                        autocomplete="username" required :invalid="$errors->has('email')" />
        </x-ui.field>

        <x-ui.field label="Password" for="password" :error="$errors->first('password')"
                    hint="At least eight characters." required>
            <x-ui.input id="password" name="password" type="password"
                        autocomplete="new-password" required :invalid="$errors->has('password')" />
        </x-ui.field>

        <x-ui.field label="Confirm password" for="password_confirmation" required>
            <x-ui.input id="password_confirmation" name="password_confirmation" type="password"
                        autocomplete="new-password" required />
        </x-ui.field>

        <div>
            <x-ui.checkbox name="terms" value="1" :checked="(bool) old('terms')" required
                           label="I agree to the academy's terms of study and privacy policy." />
            <x-ui.error :messages="$errors->first('terms')" />
        </div>

        <x-ui.button type="submit" class="w-full">Create account</x-ui.button>
    </form>

    <p class="mt-8 border-t border-ink-200 pt-6 text-sm text-ink-600">
        Already enrolled?
        <a href="{{ route('login') }}" class="font-medium text-pine-700 hover:underline">Sign in</a>
    </p>
</x-layouts.auth>
