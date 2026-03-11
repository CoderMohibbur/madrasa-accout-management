@props([
    'title' => null,
    'description' => null,
    'navLinks' => [],
])

@php
    $appName = config('app.name', 'Madrasa Account Management');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? "{$title} - " : '' }}{{ $appName }}</title>
    @if ($description)
        <meta name="description" content="{{ $description }}">
    @endif

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700|hind-siliguri:400,500,600,700|source-serif-4:600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="ui-shell ui-shell--public">
        <div class="ui-shell__backdrop"></div>

        <div class="relative min-h-screen">
            <x-public-header :nav-links="$navLinks" />

            <main class="ui-container ui-container--public ui-public-main">
                {{ $slot }}
            </main>

            <x-public-footer :nav-links="$navLinks" />
        </div>
    </div>
</body>

</html>
