@props(['tone' => 'dark'])

@php
    $primary = $tone === 'light' ? 'text-white' : 'text-ink-900';
    $mark = $tone === 'light' ? 'border-white/40 text-white' : 'border-pine-700 text-pine-700';
    $logoUrl = ($academySettings['logo_url'] ?? null);
    $name = $academySettings['academy_name'] ?? config('academy.name');
@endphp

<a href="{{ route('home') }}" {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5']) }}>
    @if ($logoUrl)
        {{-- An uploaded logo replaces the drawn wordmark entirely (§23, §28). --}}
        <img src="{{ $logoUrl }}" alt="{{ $name }}" class="h-8 w-auto">
    @else
        <span class="flex size-8 items-center justify-center border {{ $mark }} font-serif text-[0.95rem] leading-none">{{ str($name)->substr(0, 1) }}</span>
        <span class="font-serif text-[1.05rem] tracking-tight {{ $primary }}">{{ $name }}</span>
    @endif
</a>
