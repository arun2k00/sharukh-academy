<x-layouts.auth title="Verify your email" heading="Check your inbox"
                subheading="We sent a verification link to {{ auth()->user()->email }}. Open it to finish setting up your account.">
    <div class="space-y-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-ui.button type="submit" class="w-full">Send the link again</x-ui.button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <x-ui.button type="submit" variant="outline" class="w-full">Sign out</x-ui.button>
        </form>
    </div>

    <p class="mt-8 border-t border-ink-200 pt-6 text-[0.8125rem] text-ink-500">
        Nothing arrived? Check your spam folder, or write to
        <a href="mailto:{{ config('academy.support_email') }}" class="text-pine-700 hover:underline">{{ config('academy.support_email') }}</a>.
    </p>
</x-layouts.auth>
