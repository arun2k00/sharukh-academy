@props(['tone' => 'dark'])

@php
    $primary = $tone === 'light' ? 'text-white' : 'text-ink-900';
    $secondary = $tone === 'light' ? 'text-white/60' : 'text-ink-500';
    $mark = $tone === 'light' ? 'border-white/40 text-white' : 'border-pine-700 text-pine-700';
    $logoUrl = ($academySettings['logo_url'] ?? null);
@endphp

<a href="{{ route('home') }}" {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5']) }}>
    @if ($logoUrl)
        {{-- An uploaded logo replaces the drawn wordmark entirely (§23, §28). --}}
        <img src="{{ $logoUrl }}" alt="{{ $academySettings['academy_name'] ?? config('academy.name') }}" class="h-8 w-auto">
    @else
        <span class="flex size-8 items-center justify-center border {{ $mark }} font-serif text-[0.95rem] leading-none">O</span>
        <span class="leading-none">
            <span class="block font-serif text-[1.05rem] tracking-tight {{ $primary }}">Oacer</span>
            <span class="mt-0.5 block text-[0.5625rem] font-medium tracking-[0.22em] uppercase {{ $secondary }}">Academy</span>
        </span>
    @endif
</a>
