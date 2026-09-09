@props(['title', 'heading' => null, 'description' => null, 'academySettings' => [], 'unreadNotifications' => 0])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} — {{ config('academy.name') }}</title>
    {{ Vite::fonts() }}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full" x-data="{ nav: false }">
    <header class="sticky top-0 z-40 border-b border-ink-200 bg-sand-50">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-5 lg:px-8">
            <div class="flex items-center gap-3">
                <button type="button" @click="nav = true" class="-ml-1.5 p-1.5 text-ink-700 lg:hidden" aria-label="Open navigation">
                    <x-ui.icon name="menu" class="size-6" />
                </button>
                <x-site.logo />
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('courses.index') }}" class="hidden text-sm text-ink-600 hover:text-ink-900 sm:block">Browse courses</a>
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5">
                    <span class="hidden text-sm text-ink-700 sm:block">{{ auth()->user()->name }}</span>
                    <x-ui.avatar :src="auth()->user()->avatar_url" :name="auth()->user()->name" size="sm" />
                </a>
            </div>
        </div>
    </header>

    <div class="mx-auto flex max-w-7xl gap-10 px-5 py-8 lg:px-8 lg:py-10">
        <aside class="hidden w-52 shrink-0 lg:block">
            <div class="sticky top-24"><x-student.nav :unread-notifications="$unreadNotifications" /></div>
        </aside>

        {{-- Mobile navigation drawer --}}
        <div x-show="nav" x-cloak class="fixed inset-0 z-50 lg:hidden" role="dialog" aria-modal="true">
            <div class="absolute inset-0 bg-ink-950/40" @click="nav = false"></div>
            <div class="absolute inset-y-0 left-0 flex w-[min(17rem,80vw)] flex-col bg-sand-50 p-5"
                 x-transition:enter="transition duration-200 ease-out"
                 x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0">
                <div class="mb-6 flex items-center justify-between">
                    <x-site.logo />
                    <button type="button" @click="nav = false" class="p-1.5 text-ink-700" aria-label="Close navigation">
                        <x-ui.icon name="close" class="size-5" />
                    </button>
                </div>
                <x-student.nav :unread-notifications="$unreadNotifications" />
            </div>
        </div>

        <main class="min-w-0 flex-1">
            @if ($heading)
                <div class="mb-7">
                    <h1 class="text-[1.6rem] leading-tight">{{ $heading }}</h1>
                    @if ($description)
                        <p class="mt-1.5 text-sm text-ink-600">{{ $description }}</p>
                    @endif
                </div>
            @endif

            @unless (auth()->user()->hasVerifiedEmail())
                <x-ui.alert variant="warning" class="mb-6" title="Confirm your email address">
                    <p class="mt-1">We sent a verification link to {{ auth()->user()->email }}.</p>
                    <form method="POST" action="{{ route('verification.send') }}" class="mt-2">
                        @csrf
                        <button type="submit" class="text-[0.8125rem] font-medium text-brass-700 underline underline-offset-2">
                            Send it again
                        </button>
                    </form>
                </x-ui.alert>
            @endunless

            <x-ui.flash />

            {{ $slot }}
        </main>
    </div>
</body>
</html>
