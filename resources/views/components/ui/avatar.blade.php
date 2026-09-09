@props(['src' => null, 'name' => '', 'size' => 'md'])

@php
    $sizes = [
        'xs' => 'size-7 text-[0.6875rem]',
        'sm' => 'size-9 text-xs',
        'md' => 'size-11 text-sm',
        'lg' => 'size-14 text-base',
    ];

    $words = preg_split('/\s+/', trim($name)) ?: [];
    $initials = implode('', array_slice(array_filter(array_map(
        fn (string $word): string => mb_strtoupper(mb_substr($word, 0, 1)),
        $words,
    )), 0, 2)) ?: '—';
@endphp

@if ($src)
    <img src="{{ $src }}" alt="{{ $name }}"
         {{ $attributes->merge(['class' => ($sizes[$size] ?? $sizes['md']).' shrink-0 rounded-full object-cover']) }} />
@else
    <span aria-hidden="true"
          {{ $attributes->merge(['class' => ($sizes[$size] ?? $sizes['md']).' flex shrink-0 items-center justify-center rounded-full bg-ink-900 font-medium text-sand-100']) }}>
        {{ $initials }}
    </span>
@endif
