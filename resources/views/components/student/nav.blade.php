@php
    $items = [
        ['label' => 'Dashboard', 'icon' => 'dashboard', 'route' => 'dashboard', 'active' => request()->routeIs('dashboard')],
        ['label' => 'My courses', 'icon' => 'book-open', 'route' => 'my-courses', 'active' => request()->routeIs('my-courses') || request()->routeIs('learn.*')],
        ['label' => 'Live classes', 'icon' => 'broadcast', 'route' => 'live-classes.index', 'active' => request()->routeIs('live-classes.*')],
        ['label' => 'Certificates', 'icon' => 'award', 'route' => 'certificates.index', 'active' => request()->routeIs('certificates.*')],
        ['label' => 'Profile', 'icon' => 'user', 'route' => 'profile.edit', 'active' => request()->routeIs('profile.*')],
    ];
@endphp

<nav class="space-y-0.5" aria-label="Student">
    @foreach ($items as $item)
        <a href="{{ route($item['route']) }}"
           @if($item['active']) aria-current="page" @endif
           class="flex items-center gap-3 px-3 py-2 text-sm {{ $item['active'] ? 'bg-ink-900 text-white' : 'text-ink-600 hover:bg-sand-200 hover:text-ink-900' }}">
            <x-ui.icon :name="$item['icon']" class="size-[1.05rem] shrink-0" />
            {{ $item['label'] }}
        </a>
    @endforeach
</nav>

<form method="POST" action="{{ route('logout') }}" class="mt-6 border-t border-ink-200 pt-4">
    @csrf
    <button type="submit" class="flex w-full items-center gap-3 px-3 py-2 text-sm text-ink-600 hover:bg-sand-200 hover:text-ink-900">
        <x-ui.icon name="log-out" class="size-[1.05rem] shrink-0" />
        Sign out
    </button>
</form>
