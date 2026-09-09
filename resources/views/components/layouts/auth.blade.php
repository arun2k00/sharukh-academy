@props(['title', 'heading', 'subheading' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} — {{ $academySettings['academy_name'] ?? config('academy.name') }}</title>
    <x-site.brand-head />
    {{ Vite::fonts() }}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full">
    <div class="grid min-h-screen lg:grid-cols-2">
        {{-- Editorial panel: sets the tone without decorative filler. --}}
        <div class="hidden flex-col justify-between bg-ink-950 p-12 lg:flex">
            <x-site.logo tone="light" />

            <div class="max-w-md">
                <p class="font-serif text-[1.75rem]/tight text-white">
                    Considered teaching, a small catalogue, and instructors who stay with you through the work.
                </p>
                <p class="mt-6 text-sm/7 text-white/60">
                    Every course pairs recorded lessons with live sessions, downloadable material and a certificate
                    you can share when you finish.
                </p>
            </div>

            <p class="text-[0.8125rem] text-white/40">&copy; {{ now()->year }} {{ $academySettings['academy_name'] ?? config('academy.name') }}</p>
        </div>

        <div class="flex flex-col justify-center px-5 py-12 sm:px-10 lg:px-16">
            <div class="mx-auto w-full max-w-sm">
                <div class="lg:hidden"><x-site.logo /></div>

                <div class="mt-8 lg:mt-0">
                    <h1 class="text-2xl">{{ $heading }}</h1>
                    @if ($subheading)
                        <p class="mt-2 text-sm/6 text-ink-600">{{ $subheading }}</p>
                    @endif
                </div>

                <div class="mt-8">
                    @if (session('status'))
                        <x-ui.alert variant="success" class="mb-6">{{ session('status') }}</x-ui.alert>
                    @endif

                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>
