@php
    $links = [
        ['label' => 'Courses', 'route' => 'courses.index', 'active' => request()->routeIs('courses.*')],
        ['label' => 'Career Plus', 'route' => 'career-paths.index', 'active' => request()->routeIs('career-paths.*')],
        ['label' => 'Our Faculty', 'route' => 'faculty', 'active' => request()->routeIs('faculty')],
        ['label' => 'About', 'route' => 'about', 'active' => request()->routeIs('about')],
        ['label' => 'Contact', 'route' => 'contact', 'active' => request()->routeIs('contact')],
    ];
@endphp

<header x-data="{ open: false }" class="sticky top-0 z-40 border-b border-ink-200 bg-sand-50/95 backdrop-blur-sm">
    <div class="mx-auto flex h-16 max-w-[1400px] items-center justify-between gap-6 px-6 lg:px-12">
        <x-site.logo />

        <nav class="hidden items-center gap-6 lg:flex" aria-label="Main">
            @foreach ($links as $link)
                <a href="{{ route($link['route']) }}"
                   class="text-sm {{ $link['active'] ? 'text-ink-900' : 'text-ink-600 hover:text-ink-900' }}"
                   @if($link['active']) aria-current="page" @endif>{{ $link['label'] }}</a>
            @endforeach
        </nav>

        <div class="hidden items-center gap-3 lg:flex">
            @auth
                <x-ui.button :href="auth()->user()->isAdmin() ? route('admin.dashboard') : route('dashboard')"
                             variant="outline" size="sm">
                    {{ auth()->user()->isAdmin() ? 'Admin panel' : 'My learning' }}
                </x-ui.button>
            @else
                <a href="{{ route('login') }}" class="text-sm text-ink-600 hover:text-ink-900">Sign in</a>
                <x-ui.button :href="route('register')" size="sm">Enroll now</x-ui.button>
            @endauth
        </div>

        <button type="button" @click="open = true"
                class="-mr-2.5 flex size-11 items-center justify-center text-ink-700 lg:hidden"
                aria-label="Open menu">
            <x-ui.icon name="menu" class="size-6" />
        </button>
    </div>

    {{-- Full-screen menu (mobile and tablet). Teleported to the end of
         <body> because the header's backdrop-blur establishes a containing
         block for fixed-position descendants — left in place, this panel
         would be trapped inside the header's own 4rem height instead of
         covering the viewport. --}}
    <template x-teleport="body">
        <div x-show="open" x-cloak
             class="fixed inset-0 z-50 flex flex-col bg-ink-950 lg:hidden"
             x-transition:enter="transition duration-300 ease-out"
             x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition duration-200 ease-in"
             x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             role="dialog" aria-modal="true">
            <div class="flex h-16 shrink-0 items-center justify-between px-6 sm:px-10">
                <x-site.logo tone="light" />
                <button type="button" @click="open = false"
                        class="-mr-2.5 flex size-11 items-center justify-center text-white/80 hover:text-white"
                        aria-label="Close menu">
                    <x-ui.icon name="close" class="size-6" />
                </button>
            </div>

            <nav class="flex flex-1 flex-col justify-center gap-1 px-6 sm:px-10" aria-label="Mobile">
                @foreach ($links as $link)
                    <a href="{{ route($link['route']) }}"
                       class="flex items-center justify-between border-b border-white/10 py-4 font-serif text-3xl leading-none text-white/90 transition-colors hover:text-pine-300 sm:text-4xl {{ $link['active'] ? 'text-pine-300' : '' }}"
                       @if($link['active']) aria-current="page" @endif>
                        {{ $link['label'] }}
                        <x-ui.icon name="arrow-right" class="size-6 shrink-0 text-white/30" />
                    </a>
                @endforeach
            </nav>

            <div class="shrink-0 space-y-3 px-6 pt-6 pb-10 sm:px-10">
                @auth
                    <x-ui.button :href="auth()->user()->isAdmin() ? route('admin.dashboard') : route('dashboard')" size="lg" class="w-full">
                        {{ auth()->user()->isAdmin() ? 'Admin panel' : 'My learning' }}
                    </x-ui.button>
                @else
                    <x-ui.button :href="route('register')" size="lg" class="w-full">Enroll now</x-ui.button>
                    <x-ui.button :href="route('login')" variant="outline" size="lg"
                                 class="w-full !border-white/25 !bg-transparent !text-white hover:!bg-white/10">
                        Sign in
                    </x-ui.button>
                @endauth
            </div>
        </div>
    </template>
</header>
