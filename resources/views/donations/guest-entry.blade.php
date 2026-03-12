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
    $categoryLabels = collect($categories)->mapWithKeys(fn (array $category) => [$category['key'] => $category['label']])->all();
    $nameValue = old('name', $draft['name'] ?? '');
    $emailValue = old('email', $draft['email'] ?? '');
    $phoneValue = old('phone', $draft['phone'] ?? '');
    $anonymousDisplay = (bool) old('anonymous_display', $draft['anonymous_display'] ?? false);
    $hasRegistrationAction = Route::has('register.donor') && ! auth()->check();

    foreach ($presetAmounts as $presetAmount) {
        if ($draftAmount !== '' && (float) $draftAmount === (float) $presetAmount) {
            $selectedPreset = $presetAmount;
            break;
        }
    }

    $customAmount = old('custom_amount', $selectedPreset === null ? $draftAmount : '');
@endphp

<x-public-shell
    title="সাধারণ অনুদান"
    description="খাত নির্বাচন, পরিমাণ নির্ধারণ এবং নিরাপদ checkout একসাথেই সম্পন্ন করুন।"
    :nav-links="$navLinks"
>
    <section
        class="ui-public-hero ui-donation-refined"
        x-data="{
            category: @js($selectedCategory),
            categoryLabels: @js($categoryLabels),
            presetAmount: @js($selectedPreset !== null ? (string) $selectedPreset : ''),
            customAmount: @js((string) $customAmount),
            choosePreset(amount) { this.presetAmount = String(amount); this.customAmount = ''; },
            amountValue() { return this.customAmount !== '' ? this.customAmount : this.presetAmount; },
            hasAmount() { const amount = Number(this.amountValue()); return Number.isFinite(amount) && amount > 0; },
            summaryAmount() { return this.hasAmount() ? new Intl.NumberFormat('bn-BD').format(Number(this.amountValue())) + ' টাকা' : 'পরিমাণ নির্বাচন করুন'; },
            categoryLabel() { return this.categoryLabels[this.category] ?? 'খাত নির্বাচন করুন'; },
            canContinue() { return this.category !== '' && this.hasAmount(); },
        }"
    >
        <div class="ui-donation-refined__layout">
            <article class="ui-public-card ui-public-card--accent ui-donation-refined__intro">
                <div>
                    <span class="ui-public-card__eyebrow text-white/75">সাধারণ অনুদান</span>
                    <h1 class="ui-donation-refined__title text-white">খাত ও পরিমাণ নিশ্চিত করে তারপর নিরাপদ checkout-এ যান</h1>
                    <p class="ui-donation-refined__lead text-white/85">
                        এই hero section-এর মধ্যেই অনুদানের পুরো সিদ্ধান্তের ধাপ রাখা হয়েছে, যাতে visitor সঙ্গে সঙ্গে বুঝতে পারেন কোথায় খাত বাছাই করবেন, কোথায় amount ঠিক করবেন, এবং কোন বোতামে চাপলে payment gateway-এ যাবেন।
                    </p>
                </div>

                <div class="mt-6 grid gap-3 sm:grid-cols-3">
                    <div class="rounded-[1.3rem] border border-white/12 bg-white/10 px-4 py-4">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.28em] text-white/70">এক পেজে সিদ্ধান্ত</p>
                        <p class="mt-2 text-sm leading-6 text-white/80">খাত ও amount আগে ঠিক না করে checkout-এ চলে যাওয়ার chance কমানো হয়েছে।</p>
                    </div>
                    <div class="rounded-[1.3rem] border border-white/12 bg-white/10 px-4 py-4">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.28em] text-white/70">গেস্ট ফ্রেন্ডলি</p>
                        <p class="mt-2 text-sm leading-6 text-white/80">অ্যাকাউন্ট ছাড়াও অনুদান করা যাবে, registration সম্পূর্ণ আলাদা ও ঐচ্ছিক।</p>
                    </div>
                    <div class="rounded-[1.3rem] border border-white/12 bg-white/10 px-4 py-4">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.28em] text-white/70">নিরাপদ অগ্রগতি</p>
                        <p class="mt-2 text-sm leading-6 text-white/80">শেষের বড় CTA-তে ক্লিক করলে তবেই secure payment gateway-এ অগ্রসর হবেন।</p>
                    </div>
                </div>

                <div class="ui-donation-refined__journey">
                    <div class="ui-donation-refined__journey-item">
                        <span class="ui-donation-refined__journey-index">১</span>
                        <div>
                            <p class="ui-donation-refined__journey-title">খাত নির্বাচন</p>
                            <p class="ui-donation-refined__journey-copy">উপযুক্ত খাত বেছে নিন, যাতে অনুদানের উদ্দেশ্য একদম শুরুতেই পরিষ্কার থাকে।</p>
                        </div>
                    </div>
                    <div class="ui-donation-refined__journey-item">
                        <span class="ui-donation-refined__journey-index">২</span>
                        <div>
                            <p class="ui-donation-refined__journey-title">পরিমাণ নির্ধারণ</p>
                            <p class="ui-donation-refined__journey-copy">Preset amount দিয়ে দ্রুত এগোতে পারেন, অথবা custom amount লিখতে পারেন।</p>
                        </div>
                    </div>
                    <div class="ui-donation-refined__journey-item">
                        <span class="ui-donation-refined__journey-index">৩</span>
                        <div>
                            <p class="ui-donation-refined__journey-title">একটি পরিষ্কার CTA</p>
                            <p class="ui-donation-refined__journey-copy">শুধু “এখনই অনুদান দিন” বোতাম চাপলেই checkout শুরু হবে, তার আগে কিছু submit হবে না।</p>
                        </div>
                    </div>
                </div>
            </article>

            <article class="ui-public-card ui-donation-refined__panel">
                <div class="rounded-[1.55rem] border border-emerald-900/8 bg-[linear-gradient(135deg,rgba(247,250,248,0.96),rgba(252,247,238,0.96))] p-5 sm:p-6">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <span class="ui-public-kicker">এখান থেকেই donation শুরু</span>
                            <h2 class="ui-donation-refined__panel-title">অনুদান flow এখন স্পষ্ট, ধাপে ধাপে এবং করণীয়-ভিত্তিক</h2>
                            <p class="ui-donation-refined__panel-copy">খাত, amount, optional donor info এবং final checkout CTA একই panel-এ রাখা হয়েছে।</p>
                        </div>

                        <div class="rounded-[1.35rem] border border-emerald-900/10 bg-emerald-950 px-4 py-4 text-white shadow-[0_20px_40px_-30px_rgba(15,23,42,0.7)] lg:min-w-[15rem]">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.28em] text-white/70">লাইভ summary</p>
                            <p class="mt-3 text-2xl font-semibold leading-tight" x-text="summaryAmount()" aria-live="polite"></p>
                            <p class="mt-2 text-sm leading-6 text-white/75">খাত: <span class="font-semibold text-white" x-text="categoryLabel()"></span></p>
                        </div>
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
                        <input type="hidden" name="amount" :value="presetAmount">

                        <div class="ui-donation-refined__section">
                            <div class="ui-donation-refined__section-head">
                                <span class="ui-donation-refined__step">১</span>
                                <div>
                                    <h3 class="ui-donation-refined__section-title">খাত নির্বাচন করুন</h3>
                                    <p class="ui-donation-refined__section-copy">প্রথমেই আপনার অনুদানের উদ্দেশ্য বেছে নিন। প্রতিটি card শুধু select করবে, submit করবে না।</p>
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
                                        <div class="flex items-start justify-between gap-3">
                                            <span class="ui-donation-refined__category-badge">{{ $category['badge'] }}</span>
                                            @if ($category['featured'] ?? false)
                                                <span class="rounded-full bg-amber-50 px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.18em] text-amber-800">প্রধান</span>
                                            @endif
                                        </div>
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
                                    <h3 class="ui-donation-refined__section-title">পরিমাণ নির্ধারণ করুন</h3>
                                    <p class="ui-donation-refined__section-copy">Preset amount বেছে নিন অথবা custom amount লিখুন। category না বাছলে এই ধাপ স্বাভাবিকভাবেই কম গুরুত্বে দেখানো হবে।</p>
                                </div>
                            </div>

                            <div class="mt-4 rounded-[1.25rem] border border-emerald-900/8 bg-emerald-50/60 px-4 py-3 text-sm text-slate-700">
                                নির্বাচিত খাত: <span class="font-semibold text-slate-900" x-text="categoryLabel()"></span>
                            </div>

                            <div class="ui-donation-refined__preset-grid">
                                @foreach ($presetAmounts as $presetAmount)
                                    <button
                                        type="button"
                                        class="ui-donation-refined__preset"
                                        :class="presetAmount === '{{ $presetAmount }}' && customAmount === '' ? 'ui-donation-refined__preset--active' : ''"
                                        @click="choosePreset({{ $presetAmount }})"
                                        :disabled="! category"
                                    >
                                        <span class="ui-donation-refined__preset-amount">{{ number_format($presetAmount) }} টাকা</span>
                                        <span class="ui-donation-refined__preset-note">দ্রুত নির্বাচন</span>
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
                                    x-model="customAmount"
                                    @input="if ($event.target.value !== '') { presetAmount = ''; }"
                                    class="ui-donation-refined__amount-input"
                                    placeholder="যেমন 1200"
                                    autocomplete="off"
                                    :disabled="! category"
                                >
                            </div>

                            <x-input-error :messages="$errors->get('amount')" class="mt-3" />
                        </div>

                        <div class="ui-donation-refined__section">
                            <div class="ui-donation-refined__section-head">
                                <span class="ui-donation-refined__step">৩</span>
                                <div>
                                    <h3 class="ui-donation-refined__section-title">ইচ্ছা করলে donor তথ্য দিন</h3>
                                    <p class="ui-donation-refined__section-copy">এই অংশ সম্পূর্ণ ঐচ্ছিক। যোগাযোগ বা reference-এর সুবিধার জন্য তথ্য রাখতে পারেন।</p>
                                </div>
                            </div>

                            <div class="mt-5 grid gap-4 md:grid-cols-2">
                                <div>
                                    <label for="name" class="ui-donation-refined__field-label">নাম (ঐচ্ছিক)</label>
                                    <input id="name" name="name" type="text" value="{{ $nameValue }}" class="ui-donation-refined__amount-input" placeholder="আপনার নাম" autocomplete="name">
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>
                                <div>
                                    <label for="phone" class="ui-donation-refined__field-label">মোবাইল নম্বর (ঐচ্ছিক)</label>
                                    <input id="phone" name="phone" type="tel" value="{{ $phoneValue }}" class="ui-donation-refined__amount-input" placeholder="01XXXXXXXXX" autocomplete="tel">
                                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                                </div>
                                <div class="md:col-span-2">
                                    <label for="email" class="ui-donation-refined__field-label">ইমেইল (ঐচ্ছিক)</label>
                                    <input id="email" name="email" type="email" value="{{ $emailValue }}" class="ui-donation-refined__amount-input" placeholder="name@example.com" autocomplete="email">
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>
                            </div>

                            <label class="mt-5 flex items-start gap-3 rounded-[1.2rem] border border-emerald-900/8 bg-emerald-50/55 px-4 py-4">
                                <input type="checkbox" name="anonymous_display" value="1" class="mt-1 h-4 w-4 rounded border-emerald-900/25 text-emerald-900 focus:ring-emerald-900/25" @checked($anonymousDisplay)>
                                <span class="text-sm leading-6 text-slate-700">প্রয়োজনে আমার পরিচয় public display-এ গোপন রাখুন।</span>
                            </label>
                        </div>

                        <div class="rounded-[1.6rem] border border-emerald-900/8 bg-[linear-gradient(145deg,rgba(9,53,43,0.98),rgba(15,86,68,0.96))] p-5 text-white shadow-[0_30px_60px_-36px_rgba(15,23,42,0.62)]">
                            <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                                <div aria-live="polite">
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.3em] text-white/65">চূড়ান্ত নিশ্চিতকরণ</p>
                                    <p class="mt-3 text-3xl font-semibold leading-tight" x-text="summaryAmount()"></p>
                                    <p class="mt-2 text-sm leading-6 text-white/75">খাত: <span class="font-semibold text-white" x-text="categoryLabel()"></span></p>
                                </div>
                                <button
                                    type="submit"
                                    class="inline-flex w-full items-center justify-center rounded-full bg-white px-6 py-3.5 text-sm font-semibold text-emerald-950 shadow-[0_18px_36px_-24px_rgba(255,255,255,0.42)] transition hover:bg-emerald-50 disabled:cursor-not-allowed disabled:opacity-60 sm:w-auto"
                                    :disabled="! canContinue()"
                                >
                                    এখনই অনুদান দিন
                                </button>
                            </div>
                        </div>

                        <p class="ui-donation-refined__subnote">
                            payment flow, guest donation আচরণ এবং account boundary অপরিবর্তিত আছে।
                            @if ($hasRegistrationAction)
                                নিয়মিত donor access চাইলে <a href="{{ route('register.donor') }}" class="text-emerald-900 underline decoration-emerald-900/25 underline-offset-4">রেজিস্ট্রেশন</a> আলাদা ভাবে করতে পারবেন।
                            @endif
                        </p>
                    </form>
                </div>
            </article>
        </div>
    </section>
</x-public-shell>
