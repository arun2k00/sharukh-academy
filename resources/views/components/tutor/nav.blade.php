@php
    $groups = [
        'Overview' => [
            ['label' => 'Dashboard', 'icon' => 'dashboard', 'route' => 'tutor.dashboard', 'pattern' => 'tutor.dashboard'],
        ],
        'Teaching' => [
            ['label' => 'My courses', 'icon' => 'book-open', 'route' => 'tutor.courses.index', 'pattern' => 'tutor.courses.*|tutor.modules.*|tutor.lessons.*'],
            ['label' => 'Live classes', 'icon' => 'broadcast', 'route' => 'tutor.live-classes.index', 'pattern' => 'tutor.live-classes.*'],
        ],
    ];
@endphp

<nav class="space-y-6" aria-label="Tutor dashboard">
    @foreach ($groups as $group => $items)
        <div>
            <p class="px-3 text-[0.625rem] font-medium tracking-[0.14em] text-ink-400 uppercase">{{ $group }}</p>
            <div class="mt-2 space-y-0.5">
                @foreach ($items as $item)
                    @php $active = request()->routeIs(...explode('|', $item['pattern'])); @endphp
                    <a href="{{ route($item['route']) }}"
                       @if($active) aria-current="page" @endif
                       class="flex items-center gap-3 px-3 py-2 text-sm {{ $active ? 'bg-white text-ink-900' : 'text-ink-300 hover:bg-ink-800 hover:text-white' }}">
                        <x-ui.icon :name="$item['icon']" class="size-[1.05rem] shrink-0" />
                        <span class="flex-1">{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    @endforeach
</nav>
