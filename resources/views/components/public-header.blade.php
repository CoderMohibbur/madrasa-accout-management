@props([
    'navLinks' => [],
    'showAuthActions' => true,
])

@php
    $appName = config('app.name', 'Madrasa Account Management');
@endphp

<header class="ui-public-header" x-data="{ open: false }" @keydown.escape.window="open = false">
    <div class="ui-public-navbar-wrap">
        <div class="ui-container ui-container--public">
            <div class="ui-public-navbar">
                <div class="ui-public-navbar__top">
                    <a href="{{ url('/') }}" class="ui-public-brand" aria-label="{{ $appName }}">
                        <span class="ui-brand-mark ui-public-brand__mark">
                            <img src="{{ asset('img/logo .png') }}" alt="{{ $appName }}" class="h-full w-full object-contain">
                        </span>

                        <span class="ui-public-brand__body min-w-0">
                            <span class="ui-brand-kicker">At-Tawheed Islami Complex</span>
                            <span class="ui-public-brand__title">প্রতিষ্ঠানিক তথ্য, অনুদান, ভর্তি সহায়তা ও নিরাপদ প্রবেশ</span>
                        </span>
                    </a>

                    <div class="ui-public-navbar__desktop">
                        <nav class="ui-public-nav" aria-label="Public navigation">
                            @foreach ($navLinks as $link)
                                @php
                                    $isExternal = str_starts_with($link['url'], 'http://') || str_starts_with($link['url'], 'https://');
                                @endphp

                                <a
                                    href="{{ $link['url'] }}"
                                    class="ui-public-nav__link"
                                    @if ($isExternal) target="_blank" rel="noreferrer" @endif
                                >
                                    {{ $link['label'] }}
                                </a>
                            @endforeach
                        </nav>

                        @if ($showAuthActions)
                            <div class="ui-public-nav__auth">
                                @if (Route::has('login'))
                                    <a href="{{ route('login') }}" class="ui-public-action ui-public-action--ghost">লগইন</a>
                                @endif

                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="ui-public-action ui-public-action--primary">রেজিস্ট্রেশন</a>
                                @endif
                            </div>
                        @endif
                    </div>

                    <button
                        type="button"
                        class="ui-public-nav-toggle lg:hidden"
                        @click="open = !open"
                        :aria-expanded="open ? 'true' : 'false'"
                        :aria-label="open ? 'মেনু বন্ধ করুন' : 'মেনু খুলুন'"
                        aria-controls="public-navigation-panel"
                    >
                        <span class="ui-public-nav-toggle__label" x-text="open ? 'বন্ধ' : 'মেনু'"></span>
                        <span class="ui-public-nav-toggle__icon">
                            <svg x-show="!open" x-cloak class="h-[1.125rem] w-[1.125rem]" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M4 7h16M4 12h16M4 17h16" />
                            </svg>
                            <svg x-show="open" x-cloak class="h-[1.125rem] w-[1.125rem]" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M6 6l12 12M18 6L6 18" />
                            </svg>
                        </span>
                    </button>
                </div>

                <div
                    id="public-navigation-panel"
                    class="ui-public-navbar__panel lg:hidden"
                    x-cloak
                    x-show="open"
                    x-transition:enter="transition duration-200 ease-out"
                    x-transition:enter-start="translate-y-[-8px] opacity-0"
                    x-transition:enter-end="translate-y-0 opacity-100"
                    x-transition:leave="transition duration-150 ease-in"
                    x-transition:leave-start="translate-y-0 opacity-100"
                    x-transition:leave-end="translate-y-[-6px] opacity-0"
                    @click.outside="open = false"
                >
                    <nav class="ui-public-nav ui-public-nav--mobile" aria-label="Public navigation">
                        @foreach ($navLinks as $link)
                            @php
                                $isExternal = str_starts_with($link['url'], 'http://') || str_starts_with($link['url'], 'https://');
                            @endphp

                            <a
                                href="{{ $link['url'] }}"
                                class="ui-public-nav__link"
                                @click="open = false"
                                @if ($isExternal) target="_blank" rel="noreferrer" @endif
                            >
                                {{ $link['label'] }}
                            </a>
                        @endforeach
                    </nav>

                    @if ($showAuthActions)
                        <div class="ui-public-nav__auth">
                            @if (Route::has('login'))
                                <a href="{{ route('login') }}" class="ui-public-action ui-public-action--ghost" @click="open = false">লগইন</a>
                            @endif

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="ui-public-action ui-public-action--primary" @click="open = false">রেজিস্ট্রেশন</a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</header>
