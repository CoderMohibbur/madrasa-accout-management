@props([
    'navLinks' => [],
    'showAuthActions' => true,
])

@php
    $appName = config('app.name', 'Madrasa Account Management');
    $contactUrl = 'https://attawheedic.com/contact/';
@endphp

<header class="ui-public-header">
    <div class="ui-public-utility">
        <div class="ui-container ui-container--public ui-public-utility__inner">
            <div class="ui-public-utility__items">
                <span class="ui-public-mini-pill">বাংলা-প্রথম পাবলিক ইন্টারফেস</span>
                <span class="ui-public-mini-pill">শিক্ষা, দাওয়াহ, সেবা ও নিরাপদ সহায়তার কেন্দ্রীয় প্রবেশদ্বার</span>
            </div>

            <div class="ui-public-utility__actions">
                <a href="{{ route('donations.guest.entry') }}" class="ui-public-mini-link">অনুদান</a>
                <a href="{{ route('admission') }}" class="ui-public-mini-link">ভর্তি</a>
                <a href="{{ $contactUrl }}" class="ui-public-mini-link" target="_blank" rel="noreferrer">যোগাযোগ</a>
            </div>
        </div>
    </div>

    <div class="ui-public-navbar-wrap">
        <div class="ui-container ui-container--public">
            <div class="ui-public-navbar">
                <a href="{{ url('/') }}" class="ui-public-brand" aria-label="{{ $appName }}">
                    <span class="ui-brand-mark ui-public-brand__mark">
                        <img src="{{ asset('img/logo .png') }}" alt="{{ $appName }}" class="h-full w-full object-contain">
                    </span>

                    <span class="min-w-0">
                        <span class="ui-brand-kicker">At-Tawheed Islami Complex</span>
                        <span class="ui-public-brand__title">প্রতিষ্ঠানিক তথ্য, অনুদান, ভর্তি সহায়তা ও নিরাপদ প্রবেশ</span>
                    </span>
                </a>

                <div class="ui-public-navbar__panel">
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
            </div>
        </div>
    </div>
</header>
