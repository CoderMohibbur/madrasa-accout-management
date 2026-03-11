@props([
    'title' => null,
    'description' => null,
    'portalLabel' => 'Portal',
    'homeRoute' => 'dashboard',
    'navItems' => [],
    'badgeVariant' => 'neutral',
])

@php
    $currentRouteName = request()->route()?->getName();
    $contextResolver = app(\App\Services\MultiRole\MultiRoleContextResolver::class);
    $switchableContexts = Auth::check()
        ? $contextResolver->switchableContexts(Auth::user(), $currentRouteName)
        : [];
    $showSharedHomeLink = Auth::check() && $contextResolver->hasMultipleEligibleContexts(Auth::user());
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }} - {{ config('app.name', 'Madrasa Account Management') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700|source-serif-4:600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="ui-shell ui-shell--portal">
        <div class="ui-shell__backdrop"></div>

        <div class="relative min-h-screen">
            <header class="border-b border-slate-200/80 bg-white/90 backdrop-blur">
                <div class="ui-container flex flex-col gap-5 py-5 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex flex-col gap-4">
                        <div class="flex flex-wrap items-center gap-4">
                            <a href="{{ route($homeRoute) }}" class="inline-flex items-center gap-3">
                                <span class="ui-brand-mark">
                                    <img src="{{ asset('img/image.png') }}" alt="{{ config('app.name', 'Madrasa Account Management') }}" class="h-full w-full object-contain">
                                </span>

                                <span class="min-w-0">
                                    <span class="ui-brand-kicker">Madrasa Account</span>
                                    <span class="mt-1 block text-lg font-semibold text-slate-950">{{ config('app.name', 'Madrasa Account Management') }}</span>
                                </span>
                            </a>

                            <x-ui.badge :variant="$badgeVariant">{{ $portalLabel }}</x-ui.badge>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <span class="ui-identity-chip">{{ Auth::user()->name }}</span>
                            <span class="ui-identity-chip">{{ Auth::user()->email }}</span>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 lg:items-end">
                        @if ($navItems !== [])
                            <nav class="ui-portal-nav" aria-label="{{ $portalLabel }} navigation">
                                @foreach ($navItems as $item)
                                    @php
                                        $patterns = is_array($item['pattern'] ?? null)
                                            ? $item['pattern']
                                            : [$item['pattern'] ?? ($item['route'] ?? '')];
                                        $isActive = request()->routeIs(...array_filter($patterns));
                                    @endphp

                                    <a href="{{ route($item['route']) }}"
                                        class="ui-button ui-button--tab {{ $isActive ? 'ui-button--tab-active' : '' }}">
                                        {{ $item['label'] }}
                                    </a>
                                @endforeach
                            </nav>
                        @endif

                        @if ($switchableContexts !== [] && $currentRouteName !== 'dashboard')
                            <div class="rounded-2xl border border-slate-200 bg-white/80 px-4 py-3 text-left shadow-sm">
                                <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Switch context</div>

                                <div class="mt-3 flex flex-wrap gap-2">
                                    @if ($showSharedHomeLink)
                                        <a href="{{ route('dashboard') }}" class="ui-button ui-button--ghost">
                                            Shared Home
                                        </a>
                                    @endif

                                    @foreach ($switchableContexts as $context)
                                        <a href="{{ route($context['route_name']) }}" class="ui-button ui-button--secondary">
                                            {{ $context['title'] }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('profile.edit') }}" class="ui-button ui-button--secondary">
                                Profile Settings
                            </a>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="ui-button ui-button--ghost">
                                    Log Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <main class="ui-container py-8 sm:py-10">
                <x-ui.page-header :eyebrow="$portalLabel" :title="$title" :description="$description" />

                <div class="mt-6 space-y-4">
                    @if (session('success'))
                        <x-ui.alert variant="success">
                            {{ session('success') }}
                        </x-ui.alert>
                    @endif

                    @if (session('error'))
                        <x-ui.alert variant="error">
                            {{ session('error') }}
                        </x-ui.alert>
                    @endif
                </div>

                <div class="mt-8">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
</body>

</html>
