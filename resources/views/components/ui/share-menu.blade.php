@props(['url', 'title', 'label' => 'Share'])

@php
    $encodedUrl = rawurlencode($url);
    $encodedTitle = rawurlencode($title);
@endphp

<div x-data="{ open: false, copied: false }" @click.outside="open = false" class="relative inline-block">
    <x-ui.button type="button" variant="outline" size="sm" icon="external-link" @click="open = !open">
        {{ $label }}
    </x-ui.button>

    <div x-show="open" x-cloak x-transition
         class="absolute right-0 z-20 mt-2 w-56 border border-ink-200 bg-white py-1.5 shadow-sm">
        <button type="button"
                @click="navigator.clipboard.writeText('{{ $url }}'); copied = true; setTimeout(() => copied = false, 1500)"
                class="flex w-full items-center justify-between px-4 py-2 text-left text-[0.8125rem] text-ink-700 hover:bg-sand-100">
            <span x-text="copied ? 'Copied!' : 'Copy link'"></span>
        </button>
        <a href="https://wa.me/?text={{ $encodedTitle }}%20{{ $encodedUrl }}" target="_blank" rel="noopener noreferrer"
           class="block px-4 py-2 text-[0.8125rem] text-ink-700 hover:bg-sand-100">WhatsApp</a>
        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $encodedUrl }}" target="_blank" rel="noopener noreferrer"
           class="block px-4 py-2 text-[0.8125rem] text-ink-700 hover:bg-sand-100">LinkedIn</a>
        <a href="mailto:?subject={{ $encodedTitle }}&body={{ $encodedUrl }}"
           class="block px-4 py-2 text-[0.8125rem] text-ink-700 hover:bg-sand-100">Email</a>
    </div>
</div>
