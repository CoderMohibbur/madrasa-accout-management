@props([
    'navLinks' => [],
    'showCta' => true,
])

@php
    $mainSiteUrl = 'https://attawheedic.com/';
    $aboutUrl = 'https://attawheedic.com/about/';
    $contactUrl = 'https://attawheedic.com/contact/';
    $facebookUrl = 'https://www.facebook.com/AtTawheedIslamiComplex/';
    $youtubeUrl = 'https://www.youtube.com/@AtTawheedIslamiComplex';
@endphp

<footer class="ui-public-footer">
    <div class="ui-container ui-container--public">
        @if ($showCta)
            <div class="ui-public-footer__cta">
                <div>
                    <div class="ui-public-kicker">সম্মিলিত সেবার আহ্বান</div>
                    <h2 class="ui-public-footer__cta-title">দাওয়াহ, শিক্ষা ও জনকল্যাণের কাজে আপনার অংশগ্রহণকে স্বাগতম</h2>
                    <p class="ui-public-footer__cta-text">
                        প্রতিষ্ঠানের প্রয়োজন, ভর্তি সহায়তা, সাধারণ অনুদান ও যোগাযোগের পথগুলোকে এখানে সহজ,
                        পরিষ্কার এবং সম্মানজনকভাবে সাজানো হয়েছে।
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('donations.guest.entry') }}" class="ui-public-action ui-public-action--primary">
                        অনুদান করুন
                    </a>
                    <a href="{{ route('admission') }}" class="ui-public-action ui-public-action--secondary">
                        ভর্তি তথ্য
                    </a>
                </div>
            </div>
        @endif

        <div class="ui-public-footer__grid">
            <div>
                <h3 class="ui-public-footer__heading">প্রতিষ্ঠান পরিচিতি</h3>
                <p class="ui-public-footer__text">
                    আত-তাওহীদ ইসলামী কমপ্লেক্স একটি শিক্ষামুখী, মূল্যবোধনির্ভর ও জনসেবামুখী ইসলামি
                    প্রতিষ্ঠানিক উদ্যোগ, যেখানে পাবলিক তথ্য ও নিরাপদ ডিজিটাল সহায়তা একসাথে রাখা হয়েছে।
                </p>
                <div class="mt-4 flex flex-wrap gap-2">
                    <span class="ui-public-mini-pill">যশোর, বাংলাদেশ</span>
                    <span class="ui-public-mini-pill">Bangla-first</span>
                    <span class="ui-public-mini-pill">English-ready</span>
                </div>
            </div>

            <div>
                <h3 class="ui-public-footer__heading">দ্রুত লিংক</h3>
                <div class="ui-public-footer__links">
                    @foreach ($navLinks as $link)
                        @php
                            $isExternal = str_starts_with($link['url'], 'http://') || str_starts_with($link['url'], 'https://');
                        @endphp

                        <a
                            href="{{ $link['url'] }}"
                            class="ui-public-footer__link"
                            @if ($isExternal) target="_blank" rel="noreferrer" @endif
                        >
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div>
                <h3 class="ui-public-footer__heading">পাবলিক সাপোর্ট</h3>
                <div class="ui-public-footer__links">
                    <a href="{{ route('donations.guest.entry') }}" class="ui-public-footer__link">সাধারণ অনুদান শুরু করুন</a>
                    <a href="{{ route('admission') }}" class="ui-public-footer__link">ভর্তি নির্দেশনা দেখুন</a>
                    @if (Route::has('login'))
                        <a href="{{ route('login') }}" class="ui-public-footer__link">নিরাপদ লগইন</a>
                    @endif
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="ui-public-footer__link">নতুন অ্যাকাউন্ট তৈরি</a>
                    @endif
                </div>
            </div>

            <div>
                <h3 class="ui-public-footer__heading">সংযোগ ও উপস্থিতি</h3>
                <div class="ui-public-footer__links">
                    <a href="{{ $mainSiteUrl }}" class="ui-public-footer__link" target="_blank" rel="noreferrer">প্রধান ওয়েবসাইট</a>
                    <a href="{{ $aboutUrl }}" class="ui-public-footer__link" target="_blank" rel="noreferrer">আমাদের সম্পর্কে</a>
                    <a href="{{ $contactUrl }}" class="ui-public-footer__link" target="_blank" rel="noreferrer">যোগাযোগ পাতা</a>
                    <a href="{{ $facebookUrl }}" class="ui-public-footer__link" target="_blank" rel="noreferrer">Facebook</a>
                    <a href="{{ $youtubeUrl }}" class="ui-public-footer__link" target="_blank" rel="noreferrer">YouTube</a>
                </div>
            </div>
        </div>

        <div class="ui-public-footer__bar">
            <p>&copy; {{ now()->year }} At-Tawheed Islami Complex. সর্বস্বত্ব সংরক্ষিত।</p>
            <p>এই পাবলিক স্তরটি তথ্য, ভর্তি সহায়তা, অনুদান প্রবেশ এবং নিরাপদ সাইন-ইনকে আলাদা ও পরিচ্ছন্ন রাখে।</p>
        </div>
    </div>
</footer>
