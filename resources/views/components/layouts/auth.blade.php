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

            <div
                class="relative min-h-[240px] max-w-md"
                x-data="{ active: 0, total: 5 }"
                x-init="setInterval(() => active = (active + 1) % total, 10000)"
                x-cloak
            >
                <div
                    x-show="active === 0"
                    x-transition:enter="transition ease-out duration-700"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-700"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="absolute inset-0"
                >
                    <p class="font-serif text-[1.75rem]/tight text-white">
                        Considered teaching, a small catalogue, and instructors who stay with you through the work.
                    </p>
                    <p class="mt-6 text-sm/7 text-white/60">
                        Every course pairs recorded lessons with live sessions, downloadable material and a
                        certificate you can share when you finish.
                    </p>
                </div>

                <div
                    x-show="active === 1"
                    x-transition:enter="transition ease-out duration-700"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-700"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="absolute inset-0"
                >
                    <p class="font-serif text-[1.75rem]/tight text-white">
                        Depth over breadth — courses built to be finished, not just started.
                    </p>
                    <p class="mt-6 text-sm/7 text-white/60">
                        Real instructors, real feedback, and a certificate that means you actually did the work.
                    </p>
                </div>

                <div
                    x-show="active === 2"
                    x-transition:enter="transition ease-out duration-700"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-700"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="absolute inset-0"
                >
                    <p class="font-serif text-[1.75rem]/tight text-white">
                        Live classes with instructors who answer your questions, not a forum that goes quiet.
                    </p>
                    <p class="mt-6 text-sm/7 text-white/60">
                        Join scheduled sessions, ask what's actually confusing you, and revisit the recording
                        whenever you need to.
                    </p>
                </div>

                <div
                    x-show="active === 3"
                    x-transition:enter="transition ease-out duration-700"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-700"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="absolute inset-0"
                >
                    <p class="font-serif text-[1.75rem]/tight text-white">
                        A curriculum you can preview before you commit a single rupee.
                    </p>
                    <p class="mt-6 text-sm/7 text-white/60">
                        Watch the free lessons and read the syllabus, then enroll only once you're certain it's
                        right for you.
                    </p>
                </div>

                <div
                    x-show="active === 4"
                    x-transition:enter="transition ease-out duration-700"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-700"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="absolute inset-0"
                >
                    <p class="font-serif text-[1.75rem]/tight text-white">
                        One flagship path for the modern data stack, or six focused courses on their own.
                    </p>
                    <p class="mt-6 text-sm/7 text-white/60">
                        Choose the depth that matches where you are today — every course stands on its own.
                    </p>
                </div>
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
