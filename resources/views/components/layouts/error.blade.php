@props(['title', 'code'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} — {{ $academySettings['academy_name'] ?? config('academy.name') }}</title>
    {{ Vite::fonts() }}
    @vite(['resources/css/app.css'])
</head>
<body class="flex min-h-full flex-col">
    <header class="border-b border-ink-200">
        <div class="mx-auto flex h-16 max-w-6xl items-center px-5 lg:px-8">
            <x-site.logo />
        </div>
    </header>

    <main class="flex flex-1 items-center px-5 py-16 lg:px-8">
        <div class="mx-auto w-full max-w-xl">
            <p class="font-mono text-[0.8125rem] tracking-[0.2em] text-ink-400">ERROR {{ $code }}</p>
            <h1 class="mt-4 text-[2rem] leading-tight">{{ $title }}</h1>
            <div class="mt-4 text-[0.975rem]/7 text-ink-600">{{ $slot }}</div>

            <div class="mt-8 flex flex-wrap gap-3">
                <x-ui.button :href="url()->previous() !== url()->current() ? url()->previous() : route('home')" variant="outline">
                    Go back
                </x-ui.button>
                <x-ui.button :href="route('home')">Return to the academy</x-ui.button>
            </div>
        </div>
    </main>

    <footer class="border-t border-ink-200 py-6">
        <p class="mx-auto max-w-6xl px-5 text-[0.8125rem] text-ink-500 lg:px-8">
            Need help? Write to
            <a href="mailto:{{ config('academy.support_email') }}" class="text-pine-700 hover:underline">{{ config('academy.support_email') }}</a>
        </p>
    </footer>
</body>
</html>
