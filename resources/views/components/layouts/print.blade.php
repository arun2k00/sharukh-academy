@props(['title'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    {{ Vite::fonts() }}
    @vite(['resources/css/app.css'])
    <style>
        @page { size: A4 landscape; margin: 0; }
        @media print { body { background: #fff; } }
    </style>
</head>
<body class="bg-ink-100">
    {{ $slot }}
</body>
</html>
