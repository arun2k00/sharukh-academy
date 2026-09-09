@props(['items' => []])

<nav aria-label="Breadcrumb" {{ $attributes->merge(['class' => 'flex flex-wrap items-center gap-1.5 text-[0.8125rem] text-ink-500']) }}>
    @foreach ($items as $index => $item)
        @if ($index > 0)
            <x-ui.icon name="chevron-right" class="size-3.5 text-ink-300" />
        @endif

        @if (! empty($item['url']) && ! $loop->last)
            <a href="{{ $item['url'] }}" class="hover:text-ink-900">{{ $item['label'] }}</a>
        @else
            <span class="truncate text-ink-700" @if($loop->last) aria-current="page" @endif>{{ $item['label'] }}</span>
        @endif
    @endforeach
</nav>
