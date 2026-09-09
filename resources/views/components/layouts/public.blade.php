@props(['title' => null, 'description' => null, 'academySettings' => [], 'ogImage' => null])

@php
    $pageTitle = $title ? $title.' — '.config('academy.name') : config('academy.name');
    $pageDescription = $description ?? ($academySettings['site_tagline'] ?? 'Expert-led online courses with live classes, practical resources and certification.');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">

    {{-- So a shared link (live class, certificate) unfurls with a real
         preview on WhatsApp, LinkedIn and similar, rather than a bare URL. --}}
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    @if ($ogImage)
        <meta property="og:image" content="{{ $ogImage }}">
        <meta name="twitter:card" content="summary_large_image">
    @else
        <meta name="twitter:card" content="summary">
    @endif

    {{ Vite::fonts() }}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-full flex-col">
    <x-site.header />

    <main class="flex-1">
        {{ $slot }}
    </main>

    <x-site.footer :settings="$academySettings" />
</body>
</html>
