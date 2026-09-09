{{-- Favicon and optional brand-color overrides (§23, §28).

     Only `--color-pine-700/800` (the primary interactive colour, used by
     primary buttons and links) and `--color-ink-900` (the secondary button
     colour) are ever overridden — the neutral ink/sand palette that carries
     the rest of the design stays fixed, so a brand-color change can never
     accidentally break contrast or the overall visual system. --}}
@php
    $settings = $academySettings ?? [];
    $favicon = $settings['favicon_url'] ?? null;
    $primary = $settings['color_primary'] ?? null;
    $secondary = $settings['color_secondary'] ?? null;
@endphp

@if ($favicon)
    <link rel="icon" href="{{ $favicon }}">
@endif

@if ($primary || $secondary)
    <style>
        :root {
            @if ($primary)
                --color-pine-700: {{ $primary }};
                --color-pine-800: color-mix(in srgb, {{ $primary }} 80%, black);
                --color-pine-600: color-mix(in srgb, {{ $primary }} 90%, white);
                --color-pine-50: color-mix(in srgb, {{ $primary }} 8%, white);
            @endif
            @if ($secondary)
                --color-ink-900: {{ $secondary }};
                --color-ink-800: color-mix(in srgb, {{ $secondary }} 85%, white);
            @endif
        }
    </style>
@endif
