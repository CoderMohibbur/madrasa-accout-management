@php
    $navLinks = [
        ['label' => 'হোম', 'url' => url('/')],
        ['label' => 'ভর্তি', 'url' => route('admission')],
        ['label' => 'আমাদের সম্পর্কে', 'url' => 'https://attawheedic.com/about/'],
        ['label' => 'যোগাযোগ', 'url' => 'https://attawheedic.com/contact/'],
        ['label' => 'অনুদান', 'url' => route('donations.guest.entry')],
    ];

    $heroSignals = [
        [
            'label' => 'শিক্ষা ও তারবিয়াহ',
            'value' => 'কুরআন, আদব ও চরিত্র গঠন',
            'meta' => 'দ্বীনি শিক্ষা যেন জীবনের চালিকাশক্তি হয়, সেই পরিবেশ নির্মাণই লক্ষ্য।',
        ],
        [
            'label' => 'অনুদান সহায়তা',
            'value' => 'পরিষ্কার ও সম্মানজনক প্রবেশ',
            'meta' => 'জনসেবামূলক সহায়তাকে দ্রুত সঠিক জায়গায় পৌঁছে দিতে নিরাপদ পথ রাখা হয়েছে।',
        ],
        [
            'label' => 'নিরাপদ প্রবেশ',
            'value' => 'লগইন, ভর্তি ও পাবলিক তথ্য পৃথক',
            'meta' => 'যা জনসাধারণের জন্য, তা খোলা; যা সুরক্ষিত, তা নিজস্ব সীমার মধ্যে।',
        ],
    ];

    $quickCtas = [
        [
            'eyebrow' => 'সাধারণ অনুদান',
            'title' => 'শিক্ষা, দাওয়াহ ও সেবামূলক কাজে সরাসরি সহায়তা করুন',
            'description' => 'সংক্ষিপ্ত ধাপে অনুদান শুরু করুন এবং প্রতিষ্ঠানের চলমান কাজের অংশীদার হোন।',
            'href' => route('donations.guest.entry'),
            'action' => 'অনুদান শুরু করুন',
            'variant' => 'accent',
        ],
        [
            'eyebrow' => 'ভর্তি নির্দেশনা',
            'title' => 'ভর্তি প্রস্তুতি, প্রয়োজনীয় ধাপ ও নিরাপদ বাহ্যিক হ্যান্ডঅফ দেখুন',
            'description' => 'ভর্তি আবেদন এই সাইটে জমা হয় না; নির্দেশনা দেখে অনুমোদিত বহিরাগত প্রক্রিয়ায় এগিয়ে যান।',
            'href' => route('admission'),
            'action' => 'ভর্তি তথ্য দেখুন',
            'variant' => 'soft',
        ],
        [
            'eyebrow' => 'অ্যাকাউন্ট প্রবেশ',
            'title' => 'দাতা, অভিভাবক ও নিবন্ধিত ব্যবহারকারীর জন্য নিরাপদ সাইন-ইন',
            'description' => 'পাবলিক তথ্যের বাইরে প্রয়োজন হলে বিদ্যমান অ্যাকাউন্ট দিয়ে নিরাপদভাবে প্রবেশ করুন।',
            'href' => Route::has('login') ? route('login') : url('/'),
            'action' => 'লগইন করুন',
            'variant' => 'default',
        ],
    ];

    $activities = [
        [
            'tag' => 'কুরআন শিক্ষা',
            'title' => 'হিফজ, নাজেরা ও তিলাওয়াতের যত্নশীল পরিবেশ',
            'description' => 'শুদ্ধ পাঠ, মুখস্থকরণ, তাজবীদ ও নিয়মিত মুরাজাআহভিত্তিক ধারাবাহিক শিক্ষার চর্চা।',
        ],
        [
            'tag' => 'আদব ও তারবিয়াহ',
            'title' => 'শিক্ষার সাথে আখলাক ও আমল গঠনের সমন্বয়',
            'description' => 'দৈনন্দিন আচরণ, ইবাদত, শৃঙ্খলা ও দায়িত্ববোধকে শিক্ষাজীবনের অবিচ্ছেদ্য অংশ করা।',
        ],
        [
            'tag' => 'শিশু ও প্রাথমিক',
            'title' => 'নূরানী, মৌলিক দীনশিক্ষা ও ভিত্তি নির্মাণ',
            'description' => 'ছোটদের জন্য সহজ, স্নেহপূর্ণ এবং ধাপে ধাপে অগ্রসরমান ইসলামি শিক্ষাব্যবস্থা।',
        ],
        [
            'tag' => 'দাওয়াহ কার্যক্রম',
            'title' => 'খুতবা, বয়ান, তালিম ও সামাজিক সচেতনতার আয়োজন',
            'description' => 'সমাজে সুন্নাহভিত্তিক জ্ঞান, ভারসাম্যপূর্ণ চিন্তা ও কল্যাণমুখী বার্তা পৌঁছে দেওয়া।',
        ],
        [
            'tag' => 'মানবসেবা',
            'title' => 'অসহায়, শিক্ষার্থী ও পরিবারকেন্দ্রিক সহায়তার উদ্যোগ',
            'description' => 'সামাজিক প্রয়োজন, মৌসুমি সহায়তা ও প্রাতিষ্ঠানিক সহমর্মিতাকে সংগঠিতভাবে এগিয়ে নেওয়া।',
        ],
        [
            'tag' => 'রিসোর্স ও প্রকাশনা',
            'title' => 'বই, শিক্ষাসামগ্রী, অডিও-ভিডিও ও জ্ঞানভিত্তিক সম্পদ',
            'description' => 'ক্লাস, আলোচনা, নসিহত ও প্রিন্ট/ডিজিটাল উপকরণকে ধীরে ধীরে সুশৃঙ্খলভাবে উপস্থাপন।',
        ],
    ];

    $causes = [
        [
            'title' => 'সাধারণ শিক্ষা তহবিল',
            'description' => 'প্রাতিষ্ঠানিক শিক্ষার ধারাবাহিকতা, প্রয়োজনীয় পরিচালনা এবং শিক্ষাবান্ধব পরিবেশ রক্ষার সহায়তা।',
        ],
        [
            'title' => 'শিক্ষার্থী সহায়তা ও অভিভাবকীয় সহমর্মিতা',
            'description' => 'অসচ্ছল শিক্ষার্থী, মৌলিক শিক্ষাসামগ্রী ও প্রয়োজনভিত্তিক সহায়ক ব্যবস্থায় অংশগ্রহণের সুযোগ।',
        ],
        [
            'title' => 'দাওয়াহ, তালিম ও জনকল্যাণমূলক কর্মসূচি',
            'description' => 'ইলম, নসিহত, সামাজিক উপকার এবং জনসম্পৃক্ত কার্যক্রমে অবদান রাখার ক্ষেত্র।',
        ],
        [
            'title' => 'প্রকাশনা, লাইব্রেরি ও অবকাঠামোগত উন্নয়ন',
            'description' => 'জ্ঞানচর্চা, পাঠাগার, মিডিয়া ও গ্রহণযোগ্য প্রতিষ্ঠানের উপযোগী সৌন্দর্য ও সক্ষমতা নির্মাণ।',
        ],
    ];

    $publications = [
        [
            'type' => 'বই ও কিতাব',
            'title' => 'শিক্ষার্থীদের জন্য নির্বাচিত দ্বীনি পাঠ্য ও রেফারেন্স',
            'description' => 'পাঠ্য, সহায়ক নোট, সংক্ষিপ্ত ব্যাখ্যা ও বইভিত্তিক তালিকা প্রকাশের জন্য উপযোগী বিভাগ।',
            'href' => 'https://attawheedic.com/',
            'action' => 'মূল সাইট দেখুন',
        ],
        [
            'type' => 'ভিডিও ও বয়ান',
            'title' => 'দাওয়াহ, নসিহত ও প্রাতিষ্ঠানিক ভিডিও আর্কাইভ',
            'description' => 'ওয়াজ, খুতবা, আলোচনা ও কার্যক্রমের নির্বাচিত ভিডিও সংরক্ষণ ও উপস্থাপনার জন্য প্রস্তুত।',
            'href' => 'https://www.youtube.com/@AtTawheedIslamiComplex',
            'action' => 'ইউটিউব চ্যানেল',
        ],
        [
            'type' => 'ডিজিটাল রিসোর্স',
            'title' => 'ভর্তি নির্দেশনা, প্রাতিষ্ঠানিক তথ্য ও যোগাযোগ সহায়তা',
            'description' => 'যারা দ্রুত সঠিক সেকশনে যেতে চান, তাদের জন্য সংক্ষিপ্ত ও নির্ভরযোগ্য রিসোর্স হাব।',
            'href' => route('admission'),
            'action' => 'রিসোর্স খুলুন',
        ],
    ];

    $galleryItems = [
        [
            'title' => 'প্রতিষ্ঠানিক নান্দনিকতা',
            'label' => 'ফটো গ্যালারি',
            'image' => asset('img/background .png'),
            'action' => 'পরিবেশ ও স্থাপত্যভাষা',
        ],
        [
            'title' => 'শান্ত ও সুশৃঙ্খল শিক্ষা-পরিবেশ',
            'label' => 'ক্যাম্পাস ভিউ',
            'image' => asset('img/backgroun image.png'),
            'action' => 'পরিচ্ছন্নতা, সৌন্দর্য ও গ্রহণযোগ্যতা',
        ],
        [
            'title' => 'দাওয়াহ, ক্লাস ও প্রোগ্রামের জন্য ভিডিও প্রস্তুতি',
            'label' => 'ভিডিও আর্কাইভ',
            'image' => asset('img/background image.png'),
            'action' => 'ইউটিউব ও মিডিয়া উপস্থিতি',
        ],
    ];

    $updates = [
        [
            'tag' => 'পাবলিক আপডেট',
            'title' => 'প্রতিষ্ঠানিক তথ্য, অনুদান ও ভর্তি পথ একত্রে সহজ করা হয়েছে',
            'description' => 'দর্শনার্থী যেন দ্রুত বিভ্রান্তিহীনভাবে প্রয়োজনীয় সেকশনে যেতে পারেন, সেই লক্ষ্যেই এই বিন্যাস।',
            'href' => route('admission'),
            'action' => 'ভর্তি নির্দেশনা',
        ],
        [
            'tag' => 'সাপোর্ট প্রস্তুতি',
            'title' => 'অনুদান প্রবেশকে সংক্ষিপ্ত, স্বচ্ছ ও দায়িত্বশীলভাবে সাজানো হয়েছে',
            'description' => 'অপ্রয়োজনীয় জটিলতা ছাড়াই যারা অংশ নিতে চান, তারা নিরাপদভাবে অনুদানের ধাপ শুরু করতে পারবেন।',
            'href' => route('donations.guest.entry'),
            'action' => 'অনুদান এন্ট্রি',
        ],
        [
            'tag' => 'মিডিয়া ও যোগাযোগ',
            'title' => 'প্রকাশনা, ভিডিও ও সামাজিক উপস্থিতির জন্য আলাদা রিদম রাখা হয়েছে',
            'description' => 'মূল বার্তা, প্রচার ও জনসম্পৃক্ত উপস্থিতিকে বিশ্বাসযোগ্যভাবে দেখানোর জন্য কনটেন্ট ব্লক প্রস্তুত রাখা হয়েছে।',
            'href' => 'https://attawheedic.com/contact/',
            'action' => 'যোগাযোগ করুন',
        ],
    ];

    $trustLinks = [
        [
            'label' => 'প্রধান ওয়েবসাইট',
            'title' => 'মূল প্রতিষ্ঠানিক উপস্থিতি',
            'href' => 'https://attawheedic.com/',
        ],
        [
            'label' => 'Facebook',
            'title' => 'জনসম্পৃক্ত আপডেট ও কার্যক্রম',
            'href' => 'https://www.facebook.com/AtTawheedIslamiComplex/',
        ],
        [
            'label' => 'YouTube',
            'title' => 'দাওয়াহ, বয়ান ও ভিডিও উপস্থিতি',
            'href' => 'https://www.youtube.com/@AtTawheedIslamiComplex',
        ],
        [
            'label' => 'Secure Access',
            'title' => 'লগইন, রেজিস্ট্রেশন ও সুরক্ষিত প্রবেশ',
            'href' => Route::has('login') ? route('login') : url('/'),
        ],
    ];
@endphp

<x-public-shell
    title="আত-তাওহীদ ইসলামী কমপ্লেক্স"
    description="বাংলা-প্রথম, পরিষ্কার, দায়িত্বশীল এবং বিশ্বাসযোগ্য একটি পাবলিক মুখ্যপৃষ্ঠা; যেখানে প্রতিষ্ঠান পরিচিতি, অনুদান, ভর্তি ও নিরাপদ প্রবেশ সুসংগঠিত।"
    :nav-links="$navLinks"
>
    <section class="ui-public-hero">
        <div class="ui-public-hero__grid">
            <div>
                <span class="ui-public-kicker">কুরআন, সুন্নাহ, তারবিয়াহ ও জনসেবা</span>

                <h1 class="ui-public-hero__title">
                    আত-তাওহীদ ইসলামী কমপ্লেক্সে স্বাগতম
                </h1>

                <p class="ui-public-hero__lead">
                    এটি একটি মর্যাদাপূর্ণ, পরিষ্কার ও Bangla-first পাবলিক প্রবেশদ্বার, যেখানে প্রতিষ্ঠান পরিচিতি,
                    অনুদান, ভর্তি নির্দেশনা এবং নিরাপদ অ্যাকাউন্ট প্রবেশকে একই ছাদের নিচে কিন্তু সঠিক সীমারেখায়
                    সাজানো হয়েছে।
                </p>

                <div class="mt-5 flex flex-wrap gap-2.5">
                    <span class="ui-public-chip">যশোরভিত্তিক প্রতিষ্ঠানিক উপস্থিতি</span>
                    <span class="ui-public-chip">অনুদান, ভর্তি ও জনসেবা পৃথকভাবে গুছানো</span>
                    <span class="ui-public-chip">English-ready structure</span>
                </div>

                <div class="ui-public-actions">
                    <a href="{{ route('donations.guest.entry') }}" class="ui-public-action ui-public-action--primary">
                        অনুদান করুন
                    </a>
                    <a href="{{ route('admission') }}" class="ui-public-action ui-public-action--secondary">
                        ভর্তি তথ্য দেখুন
                    </a>
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="ui-public-action ui-public-action--ghost">
                            নিরাপদ লগইন
                        </a>
                    @endif
                </div>

                <div class="mt-8 grid gap-4 sm:grid-cols-3">
                    @foreach ($heroSignals as $signal)
                        <article class="ui-public-metric">
                            <p class="ui-public-metric__label">{{ $signal['label'] }}</p>
                            <h2 class="ui-public-metric__value">{{ $signal['value'] }}</h2>
                            <p class="ui-public-metric__meta">{{ $signal['meta'] }}</p>
                        </article>
                    @endforeach
                </div>
            </div>

            <div class="space-y-4">
                <figure class="ui-public-hero__media">
                    <img src="{{ asset('img/background image.png') }}" alt="Islamic institutional environment" class="h-full min-h-[360px] w-full object-cover">
                    <figcaption class="ui-public-hero__caption">
                        <span class="ui-public-kicker text-white/80">Institutional Presence</span>
                        <h2 class="mt-2 text-2xl font-semibold text-white">আনুষ্ঠানিক মর্যাদা, উষ্ণ অভ্যর্থনা এবং আধুনিক পাবলিক অভিজ্ঞতা</h2>
                        <p class="mt-2 max-w-md text-sm leading-7 text-white/80">
                            একটি বিশ্বাসযোগ্য ইসলামি প্রতিষ্ঠানের জন্য পরিষ্কার ভাষা, উঁচু মানের ভিজ্যুয়াল শ্রেণিবিন্যাস
                            এবং সুশৃঙ্খল তথ্যপ্রবাহকে সামনে আনা হয়েছে।
                        </p>
                    </figcaption>
                </figure>

                <div class="grid gap-4 sm:grid-cols-2">
                    <article class="ui-public-card ui-public-card--accent">
                        <p class="ui-public-card__eyebrow">প্রতিষ্ঠান প্রধানের বার্তা</p>
                        <h2 class="ui-public-card__title text-white">ইলম, আমল ও আস্থা - এই তিনের সমন্বিত পরিবেশ গড়ে তোলাই আমাদের উদ্দেশ্য</h2>
                        <p class="ui-public-card__body text-white/85">
                            শিক্ষা যেন শুধু পাঠে সীমাবদ্ধ না থাকে; বরং আদব, দায়িত্ববোধ, দাওয়াহ ও সেবার মাধ্যমে
                            সমাজের কল্যাণে পৌঁছে যায়, সেই লক্ষ্যেই প্রতিটি উদ্যোগকে সাজানো হচ্ছে।
                        </p>
                    </article>

                    <article class="ui-public-card ui-public-card--soft">
                        <p class="ui-public-card__eyebrow">পাবলিক অভিজ্ঞতার প্রতিশ্রুতি</p>
                        <h2 class="ui-public-card__title">যা সবার জন্য, তা সহজ; যা সুরক্ষিত, তা দায়িত্বশীলভাবে সীমাবদ্ধ</h2>
                        <p class="ui-public-card__body">
                            হোমপেজটি দর্শনার্থীদের অনুদান, ভর্তি নির্দেশনা, প্রকাশনা ও যোগাযোগে দ্রুত পৌঁছে দেয়,
                            কিন্তু সুরক্ষিত ব্যবহারকারীর তথ্য ও প্রক্রিয়া আলাদা রাখে।
                        </p>
                    </article>
                </div>
            </div>
        </div>
    </section>

    <section class="ui-public-section">
        <div class="grid gap-5 lg:grid-cols-3">
            @foreach ($quickCtas as $cta)
                <article class="ui-public-card {{ $cta['variant'] === 'accent' ? 'ui-public-card--accent' : ($cta['variant'] === 'soft' ? 'ui-public-card--soft' : '') }}">
                    <p class="ui-public-card__eyebrow {{ $cta['variant'] === 'accent' ? 'text-white/75' : '' }}">{{ $cta['eyebrow'] }}</p>
                    <h2 class="ui-public-card__title {{ $cta['variant'] === 'accent' ? 'text-white' : '' }}">{{ $cta['title'] }}</h2>
                    <p class="ui-public-card__body {{ $cta['variant'] === 'accent' ? 'text-white/85' : '' }}">{{ $cta['description'] }}</p>
                    <a href="{{ $cta['href'] }}" class="ui-public-inline-link {{ $cta['variant'] === 'accent' ? 'ui-public-inline-link--light' : '' }}">
                        {{ $cta['action'] }}
                    </a>
                </article>
            @endforeach
        </div>
    </section>

    <section class="ui-public-section">
        <div class="ui-public-section__header">
            <div class="max-w-3xl">
                <span class="ui-public-kicker">প্রতিষ্ঠান পরিচিতি</span>
                <h2 class="ui-public-title">দীনি শিক্ষা, নৈতিকতা এবং জনকল্যাণকে একই মানচিত্রে রাখা একটি আধুনিক প্রতিষ্ঠানিক উপস্থাপন</h2>
                <p class="ui-public-subtitle">
                    আমাদের লক্ষ্য শুধু একটি তথ্যপাতা নয়; বরং এমন একটি মুখ্যপৃষ্ঠা তৈরি করা, যেখানে দর্শনার্থী
                    প্রথম মুহূর্তেই বুঝতে পারেন এই প্রতিষ্ঠান কীসের প্রতিনিধিত্ব করে, কীভাবে অংশ নিতে হবে এবং
                    কোন পথটি কার জন্য।
                </p>
            </div>

            <div class="ui-public-quote">
                <img src="{{ asset('img/logo .png') }}" alt="At-Tawheed Islami Complex emblem" class="h-14 w-14 rounded-2xl bg-white/80 p-2">
                <div>
                    <p class="text-sm font-semibold text-emerald-950">বিশ্বাস, শৃঙ্খলা ও সৌন্দর্যের সমন্বয়</p>
                    <p class="mt-1 text-sm leading-6 text-emerald-900/75">
                        পরিচ্ছন্ন লেআউট, মর্যাদাপূর্ণ ভাষা এবং সীমারেখা-সংরক্ষিত ডিজিটাল প্রবেশ আমাদের মূল নকশাগত নীতি।
                    </p>
                </div>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-[1.08fr,0.92fr]">
            <article class="ui-public-card">
                <p class="ui-public-card__eyebrow">মিশন ও মূল্যবোধ</p>
                <h3 class="ui-public-card__title">বিশুদ্ধ জ্ঞান, সুশৃঙ্খল তারবিয়াহ এবং সেবামূলক অংশীদারিত্ব</h3>
                <p class="ui-public-card__body">
                    এই পাবলিক হোমপেজটি এমনভাবে গড়া হয়েছে, যাতে একজন আগ্রহী দর্শনার্থী, একজন সম্ভাব্য অভিভাবক,
                    একজন শুভানুধ্যায়ী দাতা কিংবা একজন নিবন্ধিত ব্যবহারকারী সবার জন্য আলাদা কিন্তু সামঞ্জস্যপূর্ণ পথ
                    দেখতে পান। প্রতিষ্ঠানিক ভাষা যেন একই সাথে আন্তরিক, দৃঢ় ও দায়িত্বশীল থাকে, সেটি বিশেষভাবে
                    বিবেচনায় রাখা হয়েছে।
                </p>

                <div class="mt-6 grid gap-4 md:grid-cols-2">
                    <div class="ui-public-detail">
                        <span class="ui-public-detail__index">01</span>
                        <div>
                            <h4 class="ui-public-detail__title">Bangla-first অভিজ্ঞতা</h4>
                            <p class="ui-public-detail__body">প্রথম দর্শন থেকেই মাতৃভাষাকেন্দ্রিক স্বাচ্ছন্দ্য ও পাঠযোগ্যতা নিশ্চিত করা হয়েছে।</p>
                        </div>
                    </div>

                    <div class="ui-public-detail">
                        <span class="ui-public-detail__index">02</span>
                        <div>
                            <h4 class="ui-public-detail__title">দায়িত্বশীল তথ্য বিন্যাস</h4>
                            <p class="ui-public-detail__body">অনুদান, ভর্তি, যোগাযোগ ও সাইন-ইনকে মিশিয়ে না ফেলে নিজ নিজ অবস্থানে রাখা হয়েছে।</p>
                        </div>
                    </div>

                    <div class="ui-public-detail">
                        <span class="ui-public-detail__index">03</span>
                        <div>
                            <h4 class="ui-public-detail__title">পরিবর্তনযোগ্য কনটেন্ট ব্লক</h4>
                            <p class="ui-public-detail__body">ভবিষ্যতে প্রকৃত খবর, গ্যালারি, প্রকাশনা ও বার্তা যুক্ত করার জন্য প্রতিটি সেকশন প্রস্তুত।</p>
                        </div>
                    </div>

                    <div class="ui-public-detail">
                        <span class="ui-public-detail__index">04</span>
                        <div>
                            <h4 class="ui-public-detail__title">হালকা ও দ্রুত ইন্টারফেস</h4>
                            <p class="ui-public-detail__body">ভারী স্ক্রিপ্ট, স্লাইডার বা অপ্রয়োজনীয় নির্ভরতা ছাড়াই পলিশড ভিজ্যুয়াল অভিজ্ঞতা রাখা হয়েছে।</p>
                        </div>
                    </div>
                </div>
            </article>

            <article class="ui-public-card ui-public-card--soft">
                <p class="ui-public-card__eyebrow">প্রতিষ্ঠান প্রধানের সংক্ষিপ্ত বক্তব্য</p>
                <h3 class="ui-public-card__title">“একটি ভালো প্রতিষ্ঠান শুধু ভবন দিয়ে নয়, বরং আস্থা, আদব ও আমানতের সংস্কৃতি দিয়ে গড়ে ওঠে।”</h3>
                <p class="ui-public-card__body">
                    আমরা চাই এখানে আগত প্রতিটি মানুষ অনুভব করুন যে এটি একটি সুশৃঙ্খল, নির্ভরযোগ্য এবং দ্বীনি চেতনায়
                    পরিচালিত প্রতিষ্ঠান। তাই আমাদের জনসেবা, ভর্তি সহায়তা ও অনুদান আহ্বানকে একই সাথে আন্তরিক এবং
                    দায়িত্বশীল ভঙ্গিতে উপস্থাপন করা হয়েছে।
                </p>

                <div class="mt-6 space-y-3">
                    <div class="ui-public-list-item">
                        <span class="ui-public-list-item__dot"></span>
                        <p>শিক্ষা ও তারবিয়াহকে প্রতিষ্ঠানিক মর্যাদার সাথে উপস্থাপন</p>
                    </div>
                    <div class="ui-public-list-item">
                        <span class="ui-public-list-item__dot"></span>
                        <p>অনুদান ও সহায়তাকে সহজ কিন্তু বিশ্বাসযোগ্য পথে আহ্বান</p>
                    </div>
                    <div class="ui-public-list-item">
                        <span class="ui-public-list-item__dot"></span>
                        <p>ভর্তি নির্দেশনাকে নিরাপদ বাহ্যিক প্রক্রিয়ার সাথে যুক্ত রাখা</p>
                    </div>
                    <div class="ui-public-list-item">
                        <span class="ui-public-list-item__dot"></span>
                        <p>ভবিষ্যৎ কনটেন্ট, সংবাদ ও প্রকাশনার জন্য সুসংহত ভিজ্যুয়াল কাঠামো প্রস্তুত রাখা</p>
                    </div>
                </div>
            </article>
        </div>
    </section>

    <section class="ui-public-section">
        <div class="ui-public-section__header">
            <div class="max-w-3xl">
                <span class="ui-public-kicker">মূল কার্যক্রম</span>
                <h2 class="ui-public-title">শিক্ষা, দাওয়াহ ও সেবাকে সমন্বিতভাবে দেখানোর জন্য স্পষ্ট কার্যক্রমভিত্তিক উপস্থাপন</h2>
                <p class="ui-public-subtitle">
                    প্রতিটি কার্যক্রমকে সংক্ষিপ্ত, বিশ্বাসযোগ্য এবং দর্শনার্থী-বান্ধব ভাষায় সাজানো হয়েছে যাতে হোমপেজটি
                    একটি বাস্তব প্রতিষ্ঠানিক কেন্দ্র হিসেবে অনুভূত হয়।
                </p>
            </div>
        </div>

        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($activities as $index => $activity)
                <article class="ui-public-feature">
                    <div class="ui-public-feature__top">
                        <span class="ui-public-feature__index">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="ui-public-feature__tag">{{ $activity['tag'] }}</span>
                    </div>
                    <h3 class="ui-public-feature__title">{{ $activity['title'] }}</h3>
                    <p class="ui-public-feature__body">{{ $activity['description'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="ui-public-section">
        <div class="grid gap-6 xl:grid-cols-[0.92fr,1.08fr]">
            <article class="ui-public-card ui-public-card--accent">
                <p class="ui-public-card__eyebrow text-white/75">অবদান রাখুন</p>
                <h2 class="ui-public-card__title text-white">আপনার সহায়তা একটি জীবন্ত ইসলামি প্রতিষ্ঠানকে শক্তিশালী করতে পারে</h2>
                <p class="ui-public-card__body text-white/85">
                    সাধারণ তহবিল, শিক্ষার্থী সহায়তা, দাওয়াহ কার্যক্রম, প্রকাশনা কিংবা অবকাঠামোগত উন্নয়ন - যে
                    ক্ষেত্রেই হোক, আপনার অংশগ্রহণকে সম্মানজনক ও সহজভাবে গ্রহণ করার জন্য অনুদান প্রবেশ পথ রাখা হয়েছে।
                </p>

                <div class="mt-6 space-y-3">
                    <div class="ui-public-list-item ui-public-list-item--light">
                        <span class="ui-public-list-item__dot bg-white/35"></span>
                        <p>অল্প ধাপের public donation entry</p>
                    </div>
                    <div class="ui-public-list-item ui-public-list-item--light">
                        <span class="ui-public-list-item__dot bg-white/35"></span>
                        <p>প্রতিষ্ঠানিক মর্যাদার সাথে দাতা আহ্বান</p>
                    </div>
                    <div class="ui-public-list-item ui-public-list-item--light">
                        <span class="ui-public-list-item__dot bg-white/35"></span>
                        <p>হোমপেজ থেকেই পরিষ্কার contribution sectors</p>
                    </div>
                </div>

                <div class="mt-8">
                    <a href="{{ route('donations.guest.entry') }}" class="ui-public-action ui-public-action--secondary">
                        নিরাপদ অনুদান শুরু করুন
                    </a>
                </div>
            </article>

            <div class="grid gap-5 md:grid-cols-2">
                @foreach ($causes as $cause)
                    <article class="ui-public-cause">
                        <p class="ui-public-card__eyebrow">{{ $cause['title'] }}</p>
                        <p class="ui-public-cause__body">{{ $cause['description'] }}</p>
                        <a href="{{ route('donations.guest.entry') }}" class="ui-public-inline-link">এই খাতে অংশ নিন</a>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="ui-public-section">
        <div class="grid gap-6 xl:grid-cols-[1.08fr,0.92fr]">
            <article class="ui-public-card">
                <span class="ui-public-kicker">ভর্তি সহায়তা</span>
                <h2 class="ui-public-title mt-3 text-3xl sm:text-[2.2rem]">ভর্তি তথ্য এখানে, কিন্তু আবেদনপ্রক্রিয়া অনুমোদিত বাহ্যিক হ্যান্ডঅফে</h2>
                <p class="ui-public-subtitle mt-4">
                    এই হোমপেজ থেকে আগ্রহীরা প্রথমে ভর্তি-সংক্রান্ত ধারণা, প্রস্তুতি ও নির্দেশনা পাবেন। এরপর প্রকৃত
                    আবেদন-প্রক্রিয়া যেখানে সংরক্ষিত আছে, সেই অনুমোদিত বাহ্যিক ধাপে নিরাপদভাবে অগ্রসর হবেন।
                </p>

                <div class="mt-6 grid gap-4 sm:grid-cols-3">
                    <div class="ui-public-detail">
                        <span class="ui-public-detail__index">A</span>
                        <div>
                            <h3 class="ui-public-detail__title">প্রস্তুতি</h3>
                            <p class="ui-public-detail__body">ভর্তি-পূর্ব প্রয়োজনীয় তথ্য ও দিকনির্দেশনা আগে দেখুন।</p>
                        </div>
                    </div>
                    <div class="ui-public-detail">
                        <span class="ui-public-detail__index">B</span>
                        <div>
                            <h3 class="ui-public-detail__title">সীমারেখা</h3>
                            <p class="ui-public-detail__body">এই সাইটে আবেদন ড্রাফট, আপলোড বা জমা দেওয়ার ব্যবস্থা রাখা হয়নি।</p>
                        </div>
                    </div>
                    <div class="ui-public-detail">
                        <span class="ui-public-detail__index">C</span>
                        <div>
                            <h3 class="ui-public-detail__title">হ্যান্ডঅফ</h3>
                            <p class="ui-public-detail__body">নির্দেশনা শেষে অনুমোদিত external application ধাপে এগিয়ে যান।</p>
                        </div>
                    </div>
                </div>
            </article>

            <article class="ui-public-card ui-public-card--soft">
                <p class="ui-public-card__eyebrow">Admission handoff ready</p>
                <h3 class="ui-public-card__title">ভর্তি আগ্রহীদের জন্য সংক্ষিপ্ত ও নিরাপদ CTA ব্লক</h3>
                <p class="ui-public-card__body">
                    দর্শনার্থীরা যেন বিভ্রান্ত না হন, সে জন্য ভর্তি অংশটিকে পরিষ্কার ও স্বতন্ত্র রাখা হয়েছে। নির্দেশনা
                    পড়ার পর তারা অনুমোদিত ভর্তি ধাপে যেতে পারবেন।
                </p>

                <div class="mt-6 space-y-3">
                    <a href="{{ route('admission') }}" class="ui-public-action ui-public-action--primary w-full justify-center">
                        ভর্তি নির্দেশনা খুলুন
                    </a>
                    <a href="https://attawheedic.com/contact/" class="ui-public-action ui-public-action--ghost w-full justify-center" target="_blank" rel="noreferrer">
                        যোগাযোগ সহায়তা
                    </a>
                </div>

                <div class="mt-6 rounded-[1.4rem] border border-emerald-100 bg-white/80 px-4 py-4 text-sm leading-7 text-slate-700">
                    এই ব্লকটি শুধুমাত্র নির্দেশনা ও নিরাপদ handoff-কে সামনে আনে; ভর্তি আবেদন, সুরক্ষিত ডেটা এবং
                    অন্যান্য প্রোটেক্টেড ফ্লো এখানে খোলা হয়নি।
                </div>
            </article>
        </div>
    </section>

    <section class="ui-public-section">
        <div class="ui-public-section__header">
            <div class="max-w-3xl">
                <span class="ui-public-kicker">প্রকাশনা ও রিসোর্স</span>
                <h2 class="ui-public-title">বই, ভিডিও, নির্দেশনা ও জ্ঞানভিত্তিক উপকরণের জন্য একটি পরিচ্ছন্ন উপস্থাপন স্তর</h2>
                <p class="ui-public-subtitle">
                    প্রকৃত কনটেন্ট ধীরে ধীরে যুক্ত করা গেলেও কাঠামোটি এখনই এমনভাবে তৈরি করা হয়েছে যাতে এটি
                    প্রতিষ্ঠানের একটি মর্যাদাপূর্ণ রিসোর্স-হাবে রূপ নিতে পারে।
                </p>
            </div>
        </div>

        <div class="grid gap-5 lg:grid-cols-3">
            @foreach ($publications as $resource)
                <article class="ui-public-card">
                    <p class="ui-public-card__eyebrow">{{ $resource['type'] }}</p>
                    <h3 class="ui-public-card__title">{{ $resource['title'] }}</h3>
                    <p class="ui-public-card__body">{{ $resource['description'] }}</p>
                    <a href="{{ $resource['href'] }}" class="ui-public-inline-link" @if (str_starts_with($resource['href'], 'http')) target="_blank" rel="noreferrer" @endif>
                        {{ $resource['action'] }}
                    </a>
                </article>
            @endforeach
        </div>
    </section>

    <section class="ui-public-section">
        <div class="ui-public-section__header">
            <div class="max-w-3xl">
                <span class="ui-public-kicker">ফটো, ভিডিও ও গ্যালারি</span>
                <h2 class="ui-public-title">দৃশ্যমান মর্যাদা, প্রতিষ্ঠানিক নান্দনিকতা ও মিডিয়া উপস্থিতির জন্য স্তরভিত্তিক গ্যালারি</h2>
                <p class="ui-public-subtitle">
                    বাস্তব গ্যালারি কনটেন্ট পরবর্তীতে সংযোজিত হলেও এখানে ইতোমধ্যে এমন একটি ভিজ্যুয়াল রিদম রাখা হয়েছে
                    যা হোমপেজকে প্রাণবন্ত করে, কিন্তু ভারী বা অগোছালো বানায় না।
                </p>
            </div>
        </div>

        <div class="grid gap-5 lg:grid-cols-[1.1fr,0.9fr]">
            <div class="grid gap-5 sm:grid-cols-2">
                @foreach ($galleryItems as $item)
                    <figure class="ui-public-gallery-card">
                        <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}" class="h-full min-h-[240px] w-full object-cover">
                        <figcaption class="ui-public-gallery-card__caption">
                            <span class="ui-public-gallery-card__tag">{{ $item['label'] }}</span>
                            <h3 class="ui-public-gallery-card__title">{{ $item['title'] }}</h3>
                            <p class="ui-public-gallery-card__text">{{ $item['action'] }}</p>
                        </figcaption>
                    </figure>
                @endforeach
            </div>

            <article class="ui-public-card ui-public-card--soft">
                <p class="ui-public-card__eyebrow">মিডিয়া কর্নার</p>
                <h3 class="ui-public-card__title">ছবি, ভিডিও, ইভেন্ট কভারেজ ও কার্যক্রম আর্কাইভের জন্য প্রস্তুত সেকশন</h3>
                <p class="ui-public-card__body">
                    ভবিষ্যতে কার্যক্রমের ছবি, বয়ান, সেমিনার, বিশেষ আয়োজন ও প্রকাশনা-সংশ্লিষ্ট কনটেন্ট যুক্ত করার জন্য
                    এই অংশটি deliberate spacing, layered card treatment এবং readable overlay-সহ প্রস্তুত রাখা হয়েছে।
                </p>

                <div class="mt-6 space-y-3">
                    <a href="https://www.facebook.com/AtTawheedIslamiComplex/" class="ui-public-action ui-public-action--secondary w-full justify-center" target="_blank" rel="noreferrer">
                        Facebook presence
                    </a>
                    <a href="https://www.youtube.com/@AtTawheedIslamiComplex" class="ui-public-action ui-public-action--ghost w-full justify-center" target="_blank" rel="noreferrer">
                        ভিডিও চ্যানেল
                    </a>
                </div>
            </article>
        </div>
    </section>

    <section class="ui-public-section">
        <div class="ui-public-section__header">
            <div class="max-w-3xl">
                <span class="ui-public-kicker">সর্বশেষ আপডেট</span>
                <h2 class="ui-public-title">খবর, ঘোষণা ও গুরুত্বপূর্ণ জনতথ্য দেখানোর জন্য স্পষ্ট update rhythm</h2>
                <p class="ui-public-subtitle">
                    এই ব্লকটি ভবিষ্যতের বাস্তব কনটেন্টের জন্য প্রস্তুত, তবে বর্তমানে দর্শনার্থীদের প্রয়োজনীয় দিকনির্দেশ
                    দিতে সংক্ষিপ্ত demo-ready institutional copy রাখা হয়েছে।
                </p>
            </div>
        </div>

        <div class="grid gap-5 lg:grid-cols-3">
            @foreach ($updates as $update)
                <article class="ui-public-news-card">
                    <span class="ui-public-news-card__tag">{{ $update['tag'] }}</span>
                    <h3 class="ui-public-news-card__title">{{ $update['title'] }}</h3>
                    <p class="ui-public-news-card__body">{{ $update['description'] }}</p>
                    <a href="{{ $update['href'] }}" class="ui-public-inline-link" @if (str_starts_with($update['href'], 'http')) target="_blank" rel="noreferrer" @endif>
                        {{ $update['action'] }}
                    </a>
                </article>
            @endforeach
        </div>
    </section>

    <section class="ui-public-section">
        <div class="ui-public-trust-strip">
            @foreach ($trustLinks as $link)
                <a href="{{ $link['href'] }}" class="ui-public-trust-strip__item" @if (str_starts_with($link['href'], 'http')) target="_blank" rel="noreferrer" @endif>
                    <span class="ui-public-trust-strip__label">{{ $link['label'] }}</span>
                    <span class="ui-public-trust-strip__title">{{ $link['title'] }}</span>
                </a>
            @endforeach
        </div>
    </section>
</x-public-shell>
