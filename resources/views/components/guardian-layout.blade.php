@props([
    'title' => 'Guardian Portal',
    'description' => null,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }} - {{ config('app.name', 'Madrasa Account Management') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-950 text-slate-100 antialiased">
    <div class="relative min-h-screen overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(16,185,129,0.16),_transparent_36%),radial-gradient(circle_at_bottom_right,_rgba(245,158,11,0.14),_transparent_30%)]"></div>

        <div class="relative">
            <header class="border-b border-white/10 bg-slate-950/80 backdrop-blur">
                <div class="mx-auto flex max-w-6xl flex-col gap-4 px-4 py-5 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
                    <div>
                        <a href="{{ route('guardian.dashboard') }}" class="text-xs font-semibold uppercase tracking-[0.32em] text-emerald-300">
                            Guardian Portal
                        </a>
                        <div class="mt-2 flex flex-wrap gap-2 text-sm text-slate-300">
                            <span class="rounded-full border border-emerald-400/20 bg-emerald-400/10 px-3 py-1">
                                {{ Auth::user()->name }}
                            </span>
                            <span class="rounded-full border border-white/10 px-3 py-1 text-slate-400">
                                {{ Auth::user()->email }}
                            </span>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 text-sm">
                        <a href="{{ route('guardian.dashboard') }}"
                            class="{{ request()->routeIs('guardian.dashboard') ? 'bg-white text-slate-950' : 'border border-white/10 text-slate-300 hover:bg-white/5' }} rounded-full px-4 py-2 font-medium transition">
                            Overview
                        </a>
                        <a href="{{ route('guardian.invoices.index') }}"
                            class="{{ request()->routeIs('guardian.invoices.*') ? 'bg-white text-slate-950' : 'border border-white/10 text-slate-300 hover:bg-white/5' }} rounded-full px-4 py-2 font-medium transition">
                            Invoices
                        </a>
                        <a href="{{ route('guardian.history.index') }}"
                            class="{{ request()->routeIs('guardian.history.*') ? 'bg-white text-slate-950' : 'border border-white/10 text-slate-300 hover:bg-white/5' }} rounded-full px-4 py-2 font-medium transition">
                            Payment History
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="rounded-full border border-rose-400/20 bg-rose-400/10 px-4 py-2 font-medium text-rose-100 transition hover:bg-rose-400/20">
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
                <div class="mb-8 flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <h1 class="text-3xl font-semibold text-white">{{ $title }}</h1>
                        @if ($description)
                            <p class="mt-2 max-w-3xl text-sm leading-6 text-slate-300">{{ $description }}</p>
                        @endif
                    </div>

                    <a href="{{ route('profile.edit') }}"
                        class="inline-flex items-center rounded-full border border-white/10 px-4 py-2 text-sm font-medium text-slate-200 transition hover:bg-white/5">
                        Profile Settings
                    </a>
                </div>

                @if (session('success'))
                    <div class="mb-6 rounded-3xl border border-emerald-400/20 bg-emerald-400/10 px-5 py-4 text-sm text-emerald-100">
                        {{ session('success') }}
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>
</body>

</html>
