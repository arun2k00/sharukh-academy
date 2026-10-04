@props(['title', 'heading' => null, 'description' => null, 'academySettings' => []])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} — {{ $academySettings['academy_name'] ?? config('academy.name') }} trainer</title>
    <x-site.brand-head />
    {{ Vite::fonts() }}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-sand-100" x-data="{ nav: false }">
    <div class="flex min-h-screen">
        <aside class="hidden w-60 shrink-0 flex-col bg-ink-950 lg:flex">
            <div class="flex h-16 items-center px-5">
                <x-site.logo tone="light" />
            </div>
            <div class="flex-1 overflow-y-auto px-2 py-4">
                <x-tutor.nav />
            </div>
            <div class="border-t border-white/10 p-4">
                <a href="{{ route('home') }}" class="flex items-center gap-2 px-1 text-[0.8125rem] text-ink-400 hover:text-white">
                    <x-ui.icon name="external-link" class="size-4" />
                    View website
                </a>
            </div>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="sticky top-0 z-30 border-b border-ink-200 bg-white">
                <div class="flex h-16 items-center justify-between gap-4 px-5 lg:px-8">
                    <div class="flex items-center gap-3">
                        <button type="button" @click="nav = true" class="-ml-1.5 p-1.5 text-ink-700 lg:hidden" aria-label="Open navigation">
                            <x-ui.icon name="menu" class="size-6" />
                        </button>
                        <p class="font-serif text-lg text-ink-900">{{ $heading ?? $title }}</p>
                    </div>

                    <div class="flex items-center gap-4">
                        {{ $actions ?? '' }}
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center gap-2 text-sm text-ink-600 hover:text-ink-900">
                                <x-ui.avatar :src="auth()->user()->avatar_url" :name="auth()->user()->name" size="xs" />
                                <span class="hidden sm:inline">Sign out</span>
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="flex-1 px-5 py-8 lg:px-8">
                @if ($description)
                    <p class="-mt-2 mb-6 max-w-2xl text-sm text-ink-600">{{ $description }}</p>
                @endif

                <x-ui.flash />

                {{ $slot }}
            </main>
        </div>
    </div>

    <div x-show="nav" x-cloak class="fixed inset-0 z-50 lg:hidden" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-ink-950/50" @click="nav = false"></div>
        <div class="absolute inset-y-0 left-0 flex w-[min(15rem,80vw)] flex-col bg-ink-950"
             x-transition:enter="transition duration-200 ease-out"
             x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0">
            <div class="flex h-16 items-center justify-between px-5">
                <x-site.logo tone="light" />
                <button type="button" @click="nav = false" class="p-1.5 text-white/70" aria-label="Close navigation">
                    <x-ui.icon name="close" class="size-5" />
                </button>
            </div>
            <div class="flex-1 overflow-y-auto px-2 py-4">
                <x-tutor.nav />
            </div>
        </div>
    </div>
</body>
</html>
