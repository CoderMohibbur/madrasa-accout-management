@php
    $navLinks = [
        ['label' => 'হোম', 'url' => url('/')],
        ['label' => 'ভর্তি', 'url' => route('admission')],
        ['label' => 'আমাদের সম্পর্কে', 'url' => 'https://attawheedic.com/about/'],
        ['label' => 'যোগাযোগ', 'url' => 'https://attawheedic.com/contact/'],
        ['label' => 'অনুদান', 'url' => route('donations.guest.entry')],
    ];

    $presetAmounts = [500, 1000, 2500, 5000];
    $selectedCategory = old('category', $draft['category_key'] ?? '');
    $draftAmount = old('amount', old('custom_amount', $draft['amount'] ?? ''));
    $selectedPreset = null;
    $selectedCategoryLabel = $draft['category_label'] ?? null;

    if ($selectedCategoryLabel === null) {
        foreach ($categories as $category) {
            if ($category['key'] === $selectedCategory) {
                $selectedCategoryLabel = $category['label'];
                break;
            }
        }
    }

    foreach ($presetAmounts as $presetAmount) {
        if ($draftAmount !== '' && (float) $draftAmount === (float) $presetAmount) {
            $selectedPreset = $presetAmount;
            break;
        }
    }

    $customAmount = old('custom_amount', $selectedPreset === null ? $draftAmount : '');
    $hasRegistrationAction = Route::has('register.donor') && ! auth()->check();
@endphp

<x-public-shell
    title="সাধারণ অনুদান"
    description="খাত নির্বাচন করুন, পরিমাণ ঠিক করুন এবং নিরাপদে অনুদানের পেমেন্ট গেটওয়েতে এগিয়ে যান।"
    :nav-links="$navLinks"
>
    <section class="ui-public-hero ui-donation-refined" x-data="{ category: @js($selectedCategory) }">
        <div class="ui-donation-refined__layout">
            <article class="ui-public-card ui-public-card--accent ui-donation-refined__intro">
                <div>
                    <span class="ui-public-card__eyebrow text-white/75">সাধারণ অনুদান</span>
                    <h1 class="ui-donation-refined__title text-white">খাত নির্বাচন করুন, তারপর দ্রুত ও নিরাপদে অনুদান দিন</h1>
                    <p class="ui-donation-refined__lead text-white/85">
                        প্রথমে অনুদানের খাত বেছে নিন। তারপর একটি দ্রুত পরিমাণ নির্বাচন করুন বা নিজের পরিমাণ লিখে সরাসরি
                        পেমেন্ট গেটওয়েতে এগিয়ে যান।
                    </p>
                </div>

                <div class="ui-donation-refined__journey">
                    <div class="ui-donation-refined__journey-item">
                        <span class="ui-donation-refined__journey-index">01</span>
                        <div>
                            <p class="ui-donation-refined__journey-title">খাত বেছে নিন</p>
                            <p class="ui-donation-refined__journey-copy">মাদ্রাসা, মসজিদ বা চলমান অন্য কোনো খাতে অনুদান দিন।</p>
                        </div>
                    </div>

                    <div class="ui-donation-refined__journey-item">
                        <span class="ui-donation-refined__journey-index">02</span>
                        <div>
                            <p class="ui-donation-refined__journey-title">পরিমাণ ঠিক করুন</p>
                            <p class="ui-donation-refined__journey-copy">দ্রুত পরিমাণ বেছে নিন বা নিজের পরিমাণ লিখুন।</p>
                        </div>
                    </div>

                    <div class="ui-donation-refined__journey-item">
                        <span class="ui-donation-refined__journey-index">03</span>
                        <div>
                            <p class="ui-donation-refined__journey-title">নিরাপদে এগিয়ে যান</p>
                            <p class="ui-donation-refined__journey-copy">বিদ্যমান নিরাপদ checkout flow-এই পেমেন্ট গেটওয়েতে পৌঁছে যাবেন।</p>
                        </div>
                    </div>
                </div>
            </article>

            <article class="ui-public-card ui-donation-refined__panel">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <span class="ui-public-kicker">খাত নির্বাচন বাধ্যতামূলক</span>
                        <h2 class="ui-donation-refined__panel-title">খাত ও পরিমাণ নির্ধারণ করুন</h2>
                        <p class="ui-donation-refined__panel-copy">প্রথমে খাত বেছে নিন। এরপর quick amount বা custom amount দিয়ে অনুদান শুরু করুন।</p>
                    </div>

                    @if ($selectedCategory !== '')
                        <span class="ui-public-chip">সর্বশেষ খাত: {{ $selectedCategoryLabel ?? $selectedCategory }}</span>
                    @endif
                </div>

                @if (session('guest_donation_message'))
                    <div class="ui-donation-refined__status ui-donation-refined__status--success">
                        {{ session('guest_donation_message') }}
                    </div>
                @endif

                @if ($errors->has('payment'))
                    <div class="ui-donation-refined__status ui-donation-refined__status--warning">
                        {{ $errors->first('payment') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('donations.guest.start') }}" class="ui-donation-refined__form">
                    @csrf
                    <input type="hidden" name="checkout_now" value="1">

                    <div class="ui-donation-refined__section">
                        <div class="ui-donation-refined__section-head">
                            <span class="ui-donation-refined__step">১</span>
                            <div>
                                <h3 class="ui-donation-refined__section-title">অনুদানের খাত</h3>
                                <p class="ui-donation-refined__section-copy">মাদ্রাসা কমপ্লেক্স প্রথমে, মসজিদ কমপ্লেক্স দ্বিতীয়তে, তারপর অন্যান্য বিদ্যমান খাত।</p>
                            </div>
                        </div>

                        <div class="ui-donation-refined__category-grid">
                            @foreach ($categories as $category)
                                <label
                                    class="ui-donation-refined__category-card"
                                    :class="category === '{{ $category['key'] }}' ? 'ui-donation-refined__category-card--active' : ''"
                                >
                                    <input
                                        type="radio"
                                        name="category"
                                        value="{{ $category['key'] }}"
                                        class="sr-only"
                                        x-model="category"
                                        required
                                    >

                                    <span class="ui-donation-refined__category-badge">{{ $category['badge'] }}</span>
                                    <span class="ui-donation-refined__category-title">{{ $category['label'] }}</span>
                                    <span class="ui-donation-refined__category-copy">{{ $category['description'] }}</span>
                                </label>
                            @endforeach
                        </div>

                        <x-input-error :messages="$errors->get('category')" class="mt-3" />
                    </div>

                    <div class="ui-donation-refined__amount-shell" :class="{ 'ui-donation-refined__amount-shell--locked': ! category }">
                        <div class="ui-donation-refined__section-head">
                            <span class="ui-donation-refined__step">২</span>
                            <div>
                                <h3 class="ui-donation-refined__section-title">অনুদানের পরিমাণ</h3>
                                <p class="ui-donation-refined__section-copy">দ্রুত অনুদানের জন্য quick amount ব্যবহার করুন, অথবা নিজের পরিমাণ লিখে দিন।</p>
                            </div>
                        </div>

                        <div class="ui-donation-refined__preset-grid">
                            @foreach ($presetAmounts as $presetAmount)
                                <button
                                    type="submit"
                                    name="amount"
                                    value="{{ $presetAmount }}"
                                    class="{{ $selectedPreset === $presetAmount ? 'ui-donation-refined__preset ui-donation-refined__preset--active' : 'ui-donation-refined__preset' }}"
                                    :disabled="! category"
                                >
                                    <span class="ui-donation-refined__preset-amount">{{ number_format($presetAmount) }} টাকা</span>
                                    <span class="ui-donation-refined__preset-note">দ্রুত অনুদান</span>
                                </button>
                            @endforeach
                        </div>

                        <div class="ui-donation-refined__custom-field">
                            <label for="custom_amount" class="ui-donation-refined__field-label">কাস্টম পরিমাণ / Custom amount (BDT)</label>
                            <input
                                id="custom_amount"
                                name="custom_amount"
                                type="number"
                                min="1"
                                step="0.01"
                                value="{{ $customAmount }}"
                                class="ui-donation-refined__amount-input"
                                placeholder="যেমন 1200"
                                autocomplete="off"
                            >
                        </div>

                        <x-input-error :messages="$errors->get('amount')" class="mt-3" />
                    </div>

                    <div class="ui-donation-refined__actions">
                        <button
                            type="submit"
                            class="ui-public-action ui-public-action--primary w-full justify-center sm:w-auto"
                            :disabled="! category"
                        >
                            এখনই অনুদান করুন
                        </button>

                        @if ($hasRegistrationAction)
                            <a href="{{ route('register.donor') }}" class="ui-public-action ui-public-action--secondary w-full justify-center sm:w-auto">
                                রেজিস্ট্রেশন
                            </a>
                        @endif
                    </div>

                    <p class="ui-donation-refined__subnote">রেজিস্ট্রেশন আলাদা ও ঐচ্ছিক। payment flow, guest behavior, এবং account boundary অপরিবর্তিত রাখা হয়েছে।</p>
                </form>
            </article>
        </div>
    </section>
</x-public-shell>
