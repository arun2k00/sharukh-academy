@php
    $links = [
        ['label' => 'Courses', 'route' => 'courses.index', 'active' => request()->routeIs('courses.*')],
        ['label' => 'About', 'route' => 'about', 'active' => request()->routeIs('about')],
        ['label' => 'Contact', 'route' => 'contact', 'active' => request()->routeIs('contact')],
    ];
@endphp

<header x-data="{ open: false }" class="sticky top-0 z-40 border-b border-ink-200 bg-sand-50/95 backdrop-blur-sm">
    <div class="mx-auto flex h-16 max-w-6xl items-center justify-between gap-6 px-5 lg:px-8">
        <x-site.logo />

        <nav class="hidden items-center gap-8 md:flex" aria-label="Main">
            @foreach ($links as $link)
                <a href="{{ route($link['route']) }}"
                   class="text-sm {{ $link['active'] ? 'text-ink-900' : 'text-ink-600 hover:text-ink-900' }}"
                   @if($link['active']) aria-current="page" @endif>{{ $link['label'] }}</a>
            @endforeach
        </nav>

        <div class="hidden items-center gap-3 md:flex">
            @auth
                <x-ui.button :href="auth()->user()->isAdmin() ? route('admin.dashboard') : route('dashboard')"
                             variant="outline" size="sm">
                    {{ auth()->user()->isAdmin() ? 'Admin panel' : 'My learning' }}
                </x-ui.button>
            @else
                <a href="{{ route('login') }}" class="text-sm text-ink-600 hover:text-ink-900">Sign in</a>
                <x-ui.button :href="route('register')" size="sm">Enrol now</x-ui.button>
            @endauth
        </div>

        <button type="button" @click="open = true" class="p-1.5 text-ink-700 md:hidden" aria-label="Open menu">
            <x-ui.icon name="menu" class="size-6" />
        </button>
    </div>

    {{-- Mobile drawer --}}
    <div x-show="open" x-cloak class="fixed inset-0 z-50 md:hidden" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-ink-950/40" @click="open = false"></div>
        <div class="absolute inset-y-0 right-0 flex w-[min(20rem,85vw)] flex-col bg-sand-50"
             x-transition:enter="transition duration-200 ease-out"
             x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
             x-transition:leave="transition duration-150 ease-in"
             x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full">
            <div class="flex h-16 items-center justify-between border-b border-ink-200 px-5">
                <x-site.logo />
                <button type="button" @click="open = false" class="p-1.5 text-ink-700" aria-label="Close menu">
                    <x-ui.icon name="close" class="size-5" />
                </button>
            </div>

            <nav class="flex flex-col divide-y divide-ink-200 border-b border-ink-200" aria-label="Mobile">
                @foreach ($links as $link)
                    <a href="{{ route($link['route']) }}" class="px-5 py-3.5 text-[0.95rem] text-ink-800">{{ $link['label'] }}</a>
                @endforeach
            </nav>

            <div class="mt-auto space-y-3 p-5">
                @auth
                    <x-ui.button :href="auth()->user()->isAdmin() ? route('admin.dashboard') : route('dashboard')" class="w-full">
                        {{ auth()->user()->isAdmin() ? 'Admin panel' : 'My learning' }}
                    </x-ui.button>
                @else
                    <x-ui.button :href="route('register')" class="w-full">Enrol now</x-ui.button>
                    <x-ui.button :href="route('login')" variant="outline" class="w-full">Sign in</x-ui.button>
                @endauth
            </div>
        </div>
    </div>
</header>
