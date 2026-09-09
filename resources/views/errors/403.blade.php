<x-layouts.error title="You do not have access to that" code="403">
    @auth
        Your account is not permitted to open this page. If you believe you should have access —
        for example you have paid for a course that is not showing — please contact us.
    @else
        You may need to sign in first.
        <a href="{{ route('login') }}" class="text-pine-700 underline underline-offset-2">Sign in to your account</a>.
    @endauth
</x-layouts.error>
