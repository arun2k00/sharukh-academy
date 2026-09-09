@props(['name', 'class' => 'size-5'])

@php
    // One stroke-based icon set for the whole application. Adding an icon means
    // adding a case here, which keeps the visual language consistent.
    $paths = match ($name) {
        'menu' => '<path d="M4 7h16M4 12h16M4 17h16"/>',
        'close' => '<path d="M6 6l12 12M18 6L6 18"/>',
        'chevron-down' => '<path d="m6 9 6 6 6-6"/>',
        'chevron-right' => '<path d="m9 6 6 6-6 6"/>',
        'chevron-left' => '<path d="m15 6-6 6 6 6"/>',
        'arrow-right' => '<path d="M5 12h14M13 6l6 6-6 6"/>',
        'arrow-left' => '<path d="M19 12H5M11 18l-6-6 6-6"/>',
        'arrow-up' => '<path d="M12 19V5M6 11l6-6 6 6"/>',
        'arrow-down' => '<path d="M12 5v14M18 13l-6 6-6-6"/>',
        'check' => '<path d="m5 13 4 4L19 7"/>',
        'check-circle' => '<circle cx="12" cy="12" r="9"/><path d="m8.5 12 2.5 2.5 4.5-5"/>',
        'circle' => '<circle cx="12" cy="12" r="9"/>',
        'lock' => '<rect x="4.5" y="10.5" width="15" height="10" rx="2"/><path d="M8 10.5V7.5a4 4 0 0 1 8 0v3"/>',
        'play' => '<path d="M8 5.5v13l11-6.5z"/>',
        'play-circle' => '<circle cx="12" cy="12" r="9"/><path d="M10.5 9.2v5.6l4.5-2.8z"/>',
        'video' => '<rect x="2.5" y="6.5" width="13" height="11" rx="2"/><path d="m15.5 10.5 6-3v9l-6-3z"/>',
        'file-text' => '<path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5M9 13h6M9 17h4"/>',
        'download' => '<path d="M12 4v11M8 11l4 4 4-4M5 19h14"/>',
        'calendar' => '<rect x="3.5" y="5.5" width="17" height="15" rx="2"/><path d="M3.5 10h17M8 3.5v4M16 3.5v4"/>',
        'clock' => '<circle cx="12" cy="12" r="9"/><path d="M12 7.5V12l3 2"/>',
        'users' => '<path d="M16.5 20v-1.5a4 4 0 0 0-4-4h-5a4 4 0 0 0-4 4V20"/><circle cx="10" cy="7.5" r="3.5"/><path d="M21 20v-1.5a4 4 0 0 0-3-3.87M16.5 4.13a4 4 0 0 1 0 7.75"/>',
        'user' => '<circle cx="12" cy="8" r="4"/><path d="M4.5 20a7.5 7.5 0 0 1 15 0"/>',
        'book-open' => '<path d="M12 7.5C10.5 6 8.5 5.5 4 5.5v12c4.5 0 6.5.5 8 2 1.5-1.5 3.5-2 8-2v-12c-4.5 0-6.5.5-8 2z"/><path d="M12 7.5v12"/>',
        'graduation-cap' => '<path d="M12 4 2.5 9 12 14l9.5-5z"/><path d="M6.5 11.2V16c0 1.4 2.5 2.5 5.5 2.5s5.5-1.1 5.5-2.5v-4.8M21.5 9v5"/>',
        'award' => '<circle cx="12" cy="9" r="5.5"/><path d="m8.5 13.8-1 6.7 4.5-2.5 4.5 2.5-1-6.7"/>',
        'settings' => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.6 1.6 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.6 1.6 0 0 0-1.8-.3 1.6 1.6 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1A1.6 1.6 0 0 0 9 19.4a1.6 1.6 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.6 1.6 0 0 0 .3-1.8 1.6 1.6 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1A1.6 1.6 0 0 0 4.6 9a1.6 1.6 0 0 0-.3-1.8l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.6 1.6 0 0 0 1.8.3H9a1.6 1.6 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.6 1.6 0 0 0 1 1.5 1.6 1.6 0 0 0 1.8-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.6 1.6 0 0 0-.3 1.8V9a1.6 1.6 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.6 1.6 0 0 0-1.5 1z"/>',
        'log-out' => '<path d="M15 17l5-5-5-5M20 12H9M12 20H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h6"/>',
        'dashboard' => '<rect x="3.5" y="3.5" width="7" height="8" rx="1.5"/><rect x="13.5" y="3.5" width="7" height="5" rx="1.5"/><rect x="13.5" y="11.5" width="7" height="9" rx="1.5"/><rect x="3.5" y="14.5" width="7" height="6" rx="1.5"/>',
        'search' => '<circle cx="11" cy="11" r="6.5"/><path d="m20 20-4.2-4.2"/>',
        'plus' => '<path d="M12 5v14M5 12h14"/>',
        'pencil' => '<path d="M4 20h4L19.5 8.5a2.1 2.1 0 0 0-3-3L5 17v3z"/>',
        'trash' => '<path d="M4.5 7h15M9.5 7V5.5a1.5 1.5 0 0 1 1.5-1.5h2a1.5 1.5 0 0 1 1.5 1.5V7M6.5 7l.8 12a2 2 0 0 0 2 1.9h5.4a2 2 0 0 0 2-1.9l.8-12"/>',
        'external-link' => '<path d="M13.5 4.5H19.5V10.5M19.5 4.5 11 13"/><path d="M17.5 14v4.5a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h4.5"/>',
        'mail' => '<rect x="3" y="5.5" width="18" height="13" rx="2"/><path d="m3.6 7 8.4 6 8.4-6"/>',
        'phone' => '<path d="M6.5 3.5h3l1.5 4-2 1.5a12 12 0 0 0 6 6l1.5-2 4 1.5v3a1.5 1.5 0 0 1-1.6 1.5C11.2 19.2 4.8 12.8 5 5.1A1.5 1.5 0 0 1 6.5 3.5z"/>',
        'map-pin' => '<path d="M12 21s7-6.3 7-11a7 7 0 1 0-14 0c0 4.7 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/>',
        'star' => '<path d="m12 4 2.4 5 5.6.7-4 3.9 1 5.4-5-2.7-5 2.7 1-5.4-4-3.9 5.6-.7z"/>',
        'alert' => '<path d="M12 4 2.8 20h18.4z"/><path d="M12 10v4M12 17.2v.1"/>',
        'info' => '<circle cx="12" cy="12" r="9"/><path d="M12 11v5M12 7.8v.1"/>',
        'eye' => '<path d="M2.5 12S6 6 12 6s9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6z"/><circle cx="12" cy="12" r="3"/>',
        'list' => '<path d="M8 6h12M8 12h12M8 18h12M4 6h.01M4 12h.01M4 18h.01"/>',
        'layers' => '<path d="m12 3 9 5-9 5-9-5z"/><path d="m3 13 9 5 9-5M3 17l9 5 9-5"/>',
        'broadcast' => '<circle cx="12" cy="12" r="2.5"/><path d="M7.8 7.8a6 6 0 0 0 0 8.4M16.2 16.2a6 6 0 0 0 0-8.4M4.9 4.9a10 10 0 0 0 0 14.2M19.1 19.1a10 10 0 0 0 0-14.2"/>',
        'message' => '<path d="M20.5 12.5a7.5 7.5 0 0 1-10.9 6.7L4 20.5l1.3-5.6A7.5 7.5 0 1 1 20.5 12.5z"/>',
        'tag' => '<path d="M3.5 11.4V4.5h6.9l9.1 9.1a2 2 0 0 1 0 2.8l-4.1 4.1a2 2 0 0 1-2.8 0z"/><path d="M7.5 8.5h.01"/>',
        'folder' => '<path d="M3.5 6.5a2 2 0 0 1 2-2h3.3l2 2.5h7.7a2 2 0 0 1 2 2v8.5a2 2 0 0 1-2 2h-13a2 2 0 0 1-2-2z"/>',
        'sliders' => '<path d="M5 20v-6M5 10V4M12 20v-9M12 7V4M19 20v-4M19 12V4M2.5 14h5M9.5 7h5M16.5 16h5"/>',
        default => '<circle cx="12" cy="12" r="9"/>',
    };
@endphp

<svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 24 24" fill="none" stroke="currentColor"
     stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    {!! $paths !!}
</svg>
