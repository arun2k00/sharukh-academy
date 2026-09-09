@props(['title', 'course', 'academySettings' => []])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} — {{ $course->title }}</title>
    {{ Vite::fonts() }}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-white" x-data="{ curriculum: false }">
    <header class="sticky top-0 z-40 border-b border-ink-200 bg-white">
        <div class="flex h-14 items-center justify-between gap-4 px-4 lg:px-6">
            <div class="flex min-w-0 items-center gap-4">
                <a href="{{ route('my-courses') }}"
                   class="flex shrink-0 items-center gap-2 text-sm text-ink-600 hover:text-ink-900">
                    <x-ui.icon name="arrow-left" class="size-4" />
                    <span class="hidden sm:inline">My courses</span>
                </a>
                <span class="hidden h-5 w-px bg-ink-200 sm:block"></span>
                <p class="truncate text-sm font-medium text-ink-900">{{ $course->title }}</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('questions.index', $course) }}"
                   class="hidden items-center gap-1.5 text-[0.8125rem] text-ink-600 hover:text-ink-900 sm:flex">
                    <x-ui.icon name="message" class="size-4" />
                    Questions
                </a>
                {{ $headerMeta ?? '' }}
                <button type="button" @click="curriculum = true"
                        class="flex items-center gap-2 border border-ink-300 px-3 py-1.5 text-[0.8125rem] text-ink-700 xl:hidden">
                    <x-ui.icon name="list" class="size-4" />
                    Contents
                </button>
            </div>
        </div>
    </header>

    <div class="flex">
        <main class="min-w-0 flex-1 pb-16">
            {{ $slot }}
        </main>

        {{-- Curriculum: a sticky rail on wide screens, a drawer everywhere else. --}}
        <aside class="hidden w-[21rem] shrink-0 border-l border-ink-200 xl:block">
            <div class="sticky top-14 h-[calc(100vh-3.5rem)] overflow-y-auto">
                {{ $sidebar }}
            </div>
        </aside>

        <div x-show="curriculum" x-cloak class="fixed inset-0 z-50 xl:hidden" role="dialog" aria-modal="true">
            <div class="absolute inset-0 bg-ink-950/40" @click="curriculum = false"></div>
            <div class="absolute inset-y-0 right-0 flex w-[min(22rem,90vw)] flex-col bg-white"
                 x-transition:enter="transition duration-200 ease-out"
                 x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0">
                <div class="flex h-14 shrink-0 items-center justify-between border-b border-ink-200 px-4">
                    <p class="text-sm font-medium text-ink-900">Course contents</p>
                    <button type="button" @click="curriculum = false" class="p-1.5 text-ink-700" aria-label="Close contents">
                        <x-ui.icon name="close" class="size-5" />
                    </button>
                </div>
                <div class="flex-1 overflow-y-auto">{{ $sidebar }}</div>
            </div>
        </div>
    </div>
</body>
</html>
