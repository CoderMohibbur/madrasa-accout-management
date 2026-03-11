<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700|hind-siliguri:400,500,600,700|source-serif-4:600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

@php
    $navLinks = [
        ['label' => 'হোম', 'url' => url('/')],
        ['label' => 'ভর্তি', 'url' => route('admission')],
        ['label' => 'আমাদের সম্পর্কে', 'url' => 'https://attawheedic.com/about/'],
        ['label' => 'যোগাযোগ', 'url' => 'https://attawheedic.com/contact/'],
        ['label' => 'অনুদান', 'url' => route('donations.guest.entry')],
    ];

    $pageMeta = match (true) {
        request()->routeIs('login') => [
            'eyebrow' => 'নিরাপদ প্রবেশ / Secure Access',
            'title' => 'আপনার অ্যাকাউন্টে আবার স্বাগতম',
            'description' => 'একই প্রতিষ্ঠানিক নকশাভাষার মধ্যে থেকে আপনার শেয়ারড অ্যাকাউন্টে প্রবেশ করুন। দাতা, অভিভাবক বা সাধারণ ব্যবহারকারী - যে সীমারেখাই প্রযোজ্য হোক, প্রবেশপথ একই থাকে কিন্তু অনুমতি আলাদা থাকে।',
        ],
        request()->routeIs('register.donor') => [
            'eyebrow' => 'দাতা ভিত্তি / Donor Foundation',
            'title' => 'একটি বিশ্বাসযোগ্য দাতা-প্রস্তুত অ্যাকাউন্ট তৈরি করুন',
            'description' => 'রেজিস্ট্রেশন এখানেই, কিন্তু দাতা পোর্টালের অনুমতি আলাদা। এই ধাপে কেবল একই শেয়ারড অ্যাকাউন্ট ভিত্তি ও donor intent নিরাপদভাবে রেকর্ড করা হয়।',
        ],
        request()->routeIs('register.guardian') => [
            'eyebrow' => 'অভিভাবক ভিত্তি / Guardian Foundation',
            'title' => 'অভিভাবক-প্রস্তুত অ্যাকাউন্ট শুরু করুন',
            'description' => 'শিক্ষার্থী, ইনভয়েস বা সুরক্ষিত guardian data প্রকাশ না করেই একই অ্যাকাউন্টে ভবিষ্যৎ guardian journey-এর ভিত্তি তৈরি করা হয়।',
        ],
        request()->routeIs('register') => [
            'eyebrow' => 'নতুন অ্যাকাউন্ট / Registration',
            'title' => 'একটি শেয়ারড অ্যাকাউন্ট খুলুন',
            'description' => 'ভবিষ্যৎ অনুদান, অভিভাবকীয় সহায়তা বা সাধারণ সুরক্ষিত ব্যবহারের জন্য একটিই অ্যাকাউন্ট যথেষ্ট - কিন্তু প্রতিটি প্রবেশ নিজস্ব নিয়মে সীমাবদ্ধ থাকবে।',
        ],
        request()->routeIs('registration.onboarding') => [
            'eyebrow' => 'পরবর্তী ধাপ / Onboarding',
            'title' => 'আপনার অ্যাকাউন্ট ভিত্তি প্রস্তুত',
            'description' => 'রেজিস্ট্রেশনের পর আপনার বর্তমান অবস্থান, যোগাযোগ যাচাই এবং পরবর্তী নিরাপদ পদক্ষেপগুলোকে একটি পরিষ্কার ও নিরপেক্ষ অনবোর্ডিং অভিজ্ঞতায় সাজানো হয়েছে।',
        ],
        request()->routeIs('password.request') => [
            'eyebrow' => 'পাসওয়ার্ড সহায়তা / Recovery',
            'title' => 'পাসওয়ার্ড পুনরুদ্ধার করুন',
            'description' => 'আপনার শেয়ারড অ্যাকাউন্টের ইমেইল দিন। আমরা সেখানেই একটি নিরাপদ পাসওয়ার্ড রিসেট লিংক পাঠাব।',
        ],
        request()->routeIs('password.reset') => [
            'eyebrow' => 'নতুন পাসওয়ার্ড / Reset',
            'title' => 'একটি নতুন পাসওয়ার্ড নির্ধারণ করুন',
            'description' => 'নিরাপদ অ্যাকাউন্ট প্রবেশ ফিরিয়ে আনতে নতুন পাসওয়ার্ড সেট করুন। রিসেটের এই ধাপটি শুধুমাত্র অ্যাক্সেস পুনরুদ্ধারের জন্য।',
        ],
        request()->routeIs('verification.notice') => [
            'eyebrow' => 'ইমেইল নিশ্চিতকরণ / Verification',
            'title' => 'আপনার ইমেইল যাচাই করুন',
            'description' => 'অ্যাকাউন্ট নিরাপত্তা ও বিদ্যমান verified boundary বজায় রাখতে ইমেইল চ্যানেলটি নিশ্চিত করুন। এটি অন্য কোনো role বা portal access স্বয়ংক্রিয়ভাবে খুলে দেয় না।',
        ],
        request()->routeIs('password.confirm') => [
            'eyebrow' => 'নিরাপত্তা যাচাই / Security Check',
            'title' => 'এগোনোর আগে পাসওয়ার্ড নিশ্চিত করুন',
            'description' => 'সুরক্ষিত পরবর্তী ধাপে যাওয়ার আগে এই অতিরিক্ত password check সম্পন্ন করুন। আচরণ অপরিবর্তিত, উপস্থাপন মাত্র উন্নত।',
        ],
        default => [
            'eyebrow' => 'নিরাপদ অ্যাকাউন্ট / Shared Identity',
            'title' => 'নিরাপদভাবে এগিয়ে চলুন',
            'description' => 'পাবলিক হোমপেজের একই প্রতিষ্ঠানিক ভিজ্যুয়াল ভাষা ধরে রেখে সুরক্ষিত অ্যাকাউন্ট ধাপগুলোকে সাজানো হয়েছে।',
        ],
    };

    $principles = [
        'একই নকশা-পরিবারের মধ্যে পাবলিক, অথেন্টিকেশন ও পরবর্তী নিরাপদ অভিজ্ঞতা।',
        'যে তথ্য খোলা থাকার কথা, তা খোলা; যে প্রবেশ সুরক্ষিত, তা নিজস্ব সীমার মধ্যে।',
        'হালকা, দ্রুত ও মর্যাদাপূর্ণ একটি Bangla-first institutional auth journey।',
    ];

    $quickLinks = [
        ['label' => 'অনুদান', 'href' => route('donations.guest.entry')],
        ['label' => 'ভর্তি তথ্য', 'href' => route('admission')],
        ['label' => 'পাবলিক হোম', 'href' => url('/')],
    ];
@endphp

<body class="font-sans antialiased">
    <div class="ui-shell ui-shell--guest">
        <div class="ui-shell__backdrop"></div>

        <div class="relative min-h-screen">
            <x-public-header :nav-links="$navLinks" />

            <main class="ui-container ui-container--public ui-auth-main">
                <div class="ui-auth-stage">
                    <div class="grid gap-6 xl:grid-cols-[0.92fr,1.08fr] xl:items-start">
                        <section class="space-y-5">
                            <div class="ui-auth-aside">
                                <span class="ui-public-kicker">{{ $pageMeta['eyebrow'] }}</span>
                                <h1 class="ui-auth-page__title">{{ $pageMeta['title'] }}</h1>
                                <p class="ui-auth-page__description">{{ $pageMeta['description'] }}</p>

                                <div class="mt-5 flex flex-wrap gap-2.5">
                                    <span class="ui-public-chip">Bangla-first</span>
                                    <span class="ui-public-chip">Trusted access</span>
                                    <span class="ui-public-chip">Institutional continuity</span>
                                </div>
                            </div>

                            <div class="grid gap-4">
                                @foreach ($principles as $principle)
                                    <div class="ui-auth-principle">
                                        {{ $principle }}
                                    </div>
                                @endforeach
                            </div>

                            <div class="grid gap-3 sm:grid-cols-3">
                                @foreach ($quickLinks as $link)
                                    <a href="{{ $link['href'] }}" class="ui-auth-quick-link">
                                        {{ $link['label'] }}
                                    </a>
                                @endforeach
                            </div>
                        </section>

                        <section class="w-full">
                            <div class="ui-auth-card">
                                <div class="ui-auth-card__top">
                                    <span class="ui-public-kicker">ফর্ম ও নির্দেশনা</span>
                                    <h2 class="ui-auth-card__title">{{ $pageMeta['title'] }}</h2>
                                    <p class="ui-auth-card__description">
                                        প্রয়োজনীয় তথ্য পূরণ করুন। সব route, validation, auth rule এবং backend behavior অপরিবর্তিত রাখা হয়েছে।
                                    </p>
                                </div>

                                <div class="mt-8">
                                    {{ $slot }}
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </main>

            <x-public-footer :nav-links="$navLinks" :show-cta="false" />
        </div>
    </div>
</body>

</html>
