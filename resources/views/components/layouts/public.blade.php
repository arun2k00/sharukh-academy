@props(['title' => null, 'description' => null, 'academySettings' => []])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ? $title.' — '.config('academy.name') : config('academy.name') }}</title>
    <meta name="description" content="{{ $description ?? ($academySettings['site_tagline'] ?? 'Expert-led online courses with live classes, practical resources and certification.') }}">

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
