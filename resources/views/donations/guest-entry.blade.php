@php
    $navLinks = [
        ['label' => 'হোম', 'url' => url('/')],
        ['label' => 'ভর্তি', 'url' => route('admission')],
        ['label' => 'আমাদের সম্পর্কে', 'url' => 'https://attawheedic.com/about/'],
        ['label' => 'যোগাযোগ', 'url' => 'https://attawheedic.com/contact/'],
        ['label' => 'অনুদান', 'url' => route('donations.guest.entry')],
    ];

    $presetAmounts = [100, 500, 1000, 2000, 5000, 10000];
    $selectedFund = old('fund', $draft['fund_key'] ?? $draft['category_key'] ?? '');
    $amountValue = old('amount', $draft['amount'] ?? '');
    $phoneValue = old('phone', $draft['phone'] ?? (auth()->user()?->phone ?? ''));
    $fundLabels = collect($categories)->mapWithKeys(fn (array $category) => [$category['key'] => $category['label']])->all();
    $featuredFunds = collect($categories)->filter(fn (array $category) => (bool) ($category['featured'] ?? false))->take(2)->pluck('label')->all();
    $hasRegistrationAction = Route::has('register.donor') && ! auth()->check();
    $selectedPreset = null;

    foreach ($presetAmounts as $presetAmount) {
        if ($amountValue !== '' && (float) $amountValue === (float) $presetAmount) {
            $selectedPreset = $presetAmount;
            break;
        }
    }

    $fundPriority = [
        'madrasa_complex',
        'mosque_complex',
        'zakat',
    ];

    $fundDescriptionMap = [
        'madrasa_complex' => 'দ্বীনি শিক্ষা, আবাসন, অবকাঠামো ও শিক্ষাবান্ধব পরিবেশ গঠনে আপনার অনুদান সরাসরি ভূমিকা রাখবে।',
        'mosque_complex' => 'ইবাদত, ইলম ও জনকল্যাণমূলক কার্যক্রমের কেন্দ্র হিসেবে মসজিদ কমপ্লেক্স উন্নয়নে সহযোগিতা করুন।',
        'zakat' => 'আপনার যাকাত সঠিক খাতে পৌঁছে দিয়ে অসহায় মানুষের জীবনমান উন্নয়নে অংশ নিন।',
        'general' => 'সাধারণ কল্যাণমূলক কার্যক্রমে সহায়তার জন্য এই তহবিল সর্বাধিক নমনীয় অবদান রাখে।',
        'regular' => 'নিয়মিত অনুদান প্রতিষ্ঠানের ধারাবাহিক কার্যক্রম সচল রাখতে দীর্ঘমেয়াদে সবচেয়ে কার্যকর সহায়তা দেয়।',
        'sadaqah' => 'সাদাকাহর মাধ্যমে মানবিক ও কল্যাণমুখী নানা খাতে অংশগ্রহণের সুযোগ তৈরি হয়।',
        'self_reliant' => 'স্বাবলম্বীকরণমূলক উদ্যোগে সহায়তা করে একটি পরিবারকে নতুনভাবে দাঁড়াতে সাহায্য করুন।',
        'medhabi' => 'মেধাবী শিক্ষার্থীদের শিক্ষাবান্ধব সহায়তায় আপনার অবদান ভবিষ্যৎ গঠনে ভূমিকা রাখবে।',
        'flood' => 'জরুরি দুর্যোগ, বন্যা ও সংকটে ক্ষতিগ্রস্ত মানুষের পাশে দ্রুত সহায়তা পৌঁছে দিতে অনুদান দিন।',
        'winter' => 'শীতার্ত মানুষের কাছে উষ্ণতা ও প্রয়োজনীয় সহায়তা পৌঁছে দিতে এই তহবিলে অংশ নিন।',
        'iftar' => 'রোজাদারদের জন্য ইফতার ও খাদ্য সহায়তা কার্যক্রমে আপনার অনুদান সরাসরি কাজে লাগবে।',
        'qurbani' => 'কুরবানির মাংস প্রান্তিক মানুষের কাছে পৌঁছে দিতে অংশ নিন সুশৃঙ্খল ব্যবস্থাপনায়।',
        'tree_plantation' => 'পরিবেশ, উপকার ও সদকায়ে জারিয়ার ভাবনায় বৃক্ষরোপণ কর্মসূচিতে সহায়তা করুন।',
        'tree-plantation' => 'পরিবেশ, উপকার ও সদকায়ে জারিয়ার ভাবনায় বৃক্ষরোপণ কর্মসূচিতে সহায়তা করুন।',
    ];

    $fundBadgeMap = [
        'madrasa_complex' => 'অগ্রাধিকার',
        'mosque_complex' => 'অগ্রাধিকার',
        'zakat' => 'শরয়ি খাত',
    ];

    $fundAccentMap = [
        'madrasa_complex' => 'from-emerald-500/20 via-teal-500/10 to-cyan-500/10',
        'mosque_complex' => 'from-amber-500/20 via-orange-500/10 to-yellow-500/10',
        'zakat' => 'from-violet-500/20 via-fuchsia-500/10 to-rose-500/10',
        'default' => 'from-slate-500/10 via-white to-emerald-500/10',
    ];

    $normalizedCategories = collect($categories)
        ->map(function (array $category) use ($fundDescriptionMap, $fundBadgeMap, $fundAccentMap) {
            $key = $category['key'] ?? '';
            $label = $category['label'] ?? $key;

            return [
                'key' => $key,
                'label' => $label,
                'description' => $fundDescriptionMap[$key] ?? 'এই তহবিলে অনুদানের মাধ্যমে গুরুত্বপূর্ণ কল্যাণমূলক কাজে অংশগ্রহণ করা যাবে।',
                'badge' => $fundBadgeMap[$key] ?? 'তহবিল',
                'accent' => $fundAccentMap[$key] ?? $fundAccentMap['default'],
            ];
        })
        ->filter(fn (array $item) => filled($item['key']) && filled($item['label']));

    $orderedPriorityFunds = collect($fundPriority)
        ->map(fn (string $priorityKey) => $normalizedCategories->firstWhere('key', $priorityKey))
        ->filter();

    $remainingFunds = $normalizedCategories
        ->reject(fn (array $item) => in_array($item['key'], $fundPriority, true))
        ->unique('key')
        ->values();

    $showcaseFunds = $orderedPriorityFunds
        ->concat($remainingFunds)
        ->unique('key')
        ->values();
@endphp

<x-public-shell
    title="সাধারণ অনুদান"
    description="তহবিল, মোবাইল নম্বর এবং পরিমাণ নিশ্চিত করেই একবারে নিরাপদ অনুদান চেকআউট শুরু করুন।"
    :nav-links="$navLinks"
>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap');

        .donation-page,
        .donation-page * {
            font-family: "Hind Siliguri", ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .donation-page {
            background:
                radial-gradient(circle at top left, rgba(16, 185, 129, 0.10), transparent 30%),
                radial-gradient(circle at top right, rgba(245, 158, 11, 0.12), transparent 28%),
                linear-gradient(180deg, #f8fafc 0%, #f6f7f9 100%);
        }

        .donation-mesh {
            background-image:
                linear-gradient(rgba(255,255,255,0.08) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.08) 1px, transparent 1px);
            background-size: 26px 26px;
        }

        .donation-glow {
            box-shadow:
                0 20px 60px -35px rgba(15, 23, 42, 0.45),
                0 16px 34px -24px rgba(5, 150, 105, 0.22);
        }

        .donation-card-shadow {
            box-shadow:
                0 24px 60px -32px rgba(15, 23, 42, 0.25),
                0 12px 24px -18px rgba(15, 23, 42, 0.14);
        }

        .donation-soft-ring {
            box-shadow:
                0 0 0 1px rgba(255,255,255,0.18) inset,
                0 18px 45px -30px rgba(15, 23, 42, 0.35);
        }

        .donation-field {
            background:
                linear-gradient(180deg, rgba(255,255,255,0.96), rgba(255,255,255,0.92));
        }

        .donation-showcase-card {
            box-shadow:
                0 24px 60px -35px rgba(15, 23, 42, 0.18),
                0 14px 28px -22px rgba(15, 23, 42, 0.10);
        }

        .donation-showcase-card:hover {
            box-shadow:
                0 30px 70px -34px rgba(5, 150, 105, 0.20),
                0 18px 30px -22px rgba(15, 23, 42, 0.12);
        }
    </style>

    <section
        class="donation-page relative overflow-hidden py-6 sm:py-8 lg:py-10"
        x-data="{
            fund: @js($selectedFund),
            fundLabels: @js($fundLabels),
            amount: @js((string) $amountValue),
            phone: @js((string) $phoneValue),
            selectedPreset: @js($selectedPreset !== null ? (string) $selectedPreset : ''),
            choosePreset(amount) {
                this.selectedPreset = String(amount);
                this.amount = String(amount);
            },
            hasAmount() {
                const value = Number(this.amount);
                return Number.isFinite(value) && value > 0;
            },
            fundLabel() {
                return this.fundLabels[this.fund] ?? 'তহবিল নির্বাচন করুন';
            },
            summaryAmount() {
                if (! this.hasAmount()) {
                    return 'পরিমাণ নির্ধারণ করুন';
                }

                return new Intl.NumberFormat('bn-BD').format(Number(this.amount)) + ' টাকা';
            },
            canSubmit() {
                return this.fund !== '' && this.hasAmount() && this.phone.trim() !== '';
            },
            chooseFundAndScroll(fundKey, focusAmount = false) {
                this.fund = fundKey;
                this.$nextTick(() => {
                    document.getElementById('quick-donation-form')?.scrollIntoView({ behavior: 'smooth', block: 'center' });

                    if (focusAmount) {
                        document.getElementById('amount')?.focus();
                    }
                });
            },
        }"
    >
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute left-[-80px] top-10 h-44 w-44 rounded-full bg-emerald-300/20 blur-3xl"></div>
            <div class="absolute right-[-70px] top-24 h-52 w-52 rounded-full bg-amber-300/20 blur-3xl"></div>
            <div class="absolute bottom-0 left-1/2 h-40 w-40 -translate-x-1/2 rounded-full bg-sky-200/20 blur-3xl"></div>
        </div>

        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-[2rem] border border-white/70 bg-white/70 donation-card-shadow backdrop-blur-xl sm:rounded-[2.25rem]">
                <div class="grid lg:grid-cols-[1.08fr_minmax(360px,0.92fr)]">
                    <div class="relative min-h-[320px] overflow-hidden bg-slate-950 sm:min-h-[380px] lg:min-h-[640px]">
                        <img
                            src="{{ asset('img/background image.png') }}"
                            alt="মাদ্রাসা ও মসজিদ কমপ্লেক্সের আলোকিত প্রাঙ্গণ"
                            class="absolute inset-0 h-full w-full object-cover"
                        >

                        <div class="donation-mesh absolute inset-0"></div>
                        <div class="absolute inset-0 bg-[linear-gradient(135deg,rgba(2,6,23,0.88),rgba(6,78,59,0.52)_45%,rgba(2,6,23,0.42)_75%,rgba(2,6,23,0.18))]"></div>
                        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(255,255,255,0.16),transparent_26%),radial-gradient(circle_at_bottom_right,rgba(245,158,11,0.16),transparent_24%)]"></div>

                        <div class="relative flex h-full flex-col justify-between p-6 sm:p-8 lg:p-10">
                            <div class="max-w-xl">
                                <span class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-4 py-2 text-[12px] font-semibold tracking-[0.18em] text-white/90 backdrop-blur">
                                    <span class="h-2 w-2 rounded-full bg-emerald-300"></span>
                                    নিরাপদ অনুদান
                                </span>

                                <h1 class="mt-5 text-3xl font-semibold leading-[1.22] text-white sm:text-4xl lg:text-[2.8rem]">
                                    আপনার অনুদান পৌঁছে যাক উপযুক্ত তহবিলে, সহজ ও নিরাপদভাবে
                                </h1>

                                <p class="mt-4 max-w-2xl text-sm leading-7 text-white/80 sm:text-base">
                                    পরিমাণ, মোবাইল নম্বর ও তহবিল নির্বাচন করলেই এক ধাপে নিরাপদ চেকআউট শুরু হবে।
                                </p>
                            </div>

                            <div class="space-y-4">
                                @if (! empty($featuredFunds))
                                    <div class="flex flex-wrap gap-2.5">
                                        @foreach ($featuredFunds as $featuredFund)
                                            <span class="inline-flex items-center rounded-full border border-white/12 bg-white/10 px-4 py-2 text-sm font-medium text-white/90 backdrop-blur-md">
                                                {{ $featuredFund }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="grid gap-3 sm:grid-cols-3">
                                    <div class="rounded-2xl border border-white/10 bg-white/10 px-4 py-3 text-white/90 backdrop-blur-md donation-soft-ring">
                                        <p class="text-[11px] font-semibold tracking-[0.2em] text-white/65">ধাপ ১</p>
                                        <p class="mt-1 text-sm font-medium leading-6">তহবিল নির্বাচন করুন</p>
                                    </div>
                                    <div class="rounded-2xl border border-white/10 bg-white/10 px-4 py-3 text-white/90 backdrop-blur-md donation-soft-ring">
                                        <p class="text-[11px] font-semibold tracking-[0.2em] text-white/65">ধাপ ২</p>
                                        <p class="mt-1 text-sm font-medium leading-6">পরিমাণ ও মোবাইল নম্বর দিন</p>
                                    </div>
                                    <div class="rounded-2xl border border-white/10 bg-white/10 px-4 py-3 text-white/90 backdrop-blur-md donation-soft-ring">
                                        <p class="text-[11px] font-semibold tracking-[0.2em] text-white/65">ধাপ ৩</p>
                                        <p class="mt-1 text-sm font-medium leading-6">নিরাপদ চেকআউটে এগিয়ে যান</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="relative bg-[linear-gradient(180deg,#fffdf7_0%,#ffffff_46%,#f8fafc_100%)] p-4 sm:p-6 lg:p-8">
                        <div class="absolute inset-x-0 top-0 h-24 bg-[radial-gradient(circle_at_top,rgba(245,158,11,0.12),transparent_70%)]"></div>

                        <div class="relative mx-auto max-w-xl">
                            <div class="donation-glow overflow-hidden rounded-[1.75rem] border border-amber-100 bg-white/90 backdrop-blur-xl">
                                <div class="border-b border-slate-100 bg-[linear-gradient(135deg,rgba(255,251,235,0.95),rgba(236,253,245,0.85))] px-5 py-5 sm:px-6">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <span class="inline-flex items-center rounded-full border border-emerald-300 bg-emerald-100 px-3 py-1 text-[12px] font-semibold text-emerald-900">
                                                দ্রুত অনুদান
                                            </span>
                                            <h2 class="mt-3 text-2xl font-semibold leading-tight text-slate-900 sm:text-[2rem]">
                                                অনুদান সম্পন্ন করুন
                                            </h2>
                                            <p class="mt-2 text-sm leading-6 text-slate-600">
                                                প্রয়োজনীয় তথ্য দিন, তারপর নিরাপদ চেকআউটে চলে যান।
                                            </p>
                                        </div>

                                        <div class="hidden rounded-2xl border border-white/70 bg-white/80 p-3 shadow-sm sm:block">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3 4 7v6c0 5 3.4 7.9 8 9 4.6-1.1 8-4 8-9V7l-8-4Z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m9.5 12 1.7 1.7L15 9.9"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <div class="px-5 py-5 sm:px-6 sm:py-6">
                                    @if (session('guest_donation_message'))
                                        <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50/95 px-4 py-3 text-sm leading-6 text-emerald-900">
                                            {{ session('guest_donation_message') }}
                                        </div>
                                    @endif

                                    @if ($errors->has('payment'))
                                        <div class="mb-4 rounded-2xl border border-amber-200 bg-amber-50/95 px-4 py-3 text-sm leading-6 text-amber-900">
                                            {{ $errors->first('payment') }}
                                        </div>
                                    @endif

                                    <form id="quick-donation-form" method="POST" action="{{ route('donations.quick.checkout') }}" class="space-y-5">
                                        @csrf

                                        <div class="space-y-4">
                                            <div>
                                                <label for="fund" class="mb-2 block text-sm font-semibold text-slate-800">
                                                    তহবিল <span class="text-rose-600">*</span>
                                                </label>

                                                <div class="relative">
                                                    <select
                                                        id="fund"
                                                        name="fund"
                                                        x-model="fund"
                                                        class="donation-field block w-full appearance-none rounded-2xl border border-slate-200 px-4 py-3.5 pr-12 text-[15px] text-slate-900 shadow-[0_12px_26px_-22px_rgba(15,23,42,0.35)] transition focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/10"
                                                        required
                                                    >
                                                        <option value="">তহবিল নির্বাচন করুন</option>
                                                        @foreach ($categories as $category)
                                                            <option value="{{ $category['key'] }}">{{ $category['label'] }}</option>
                                                        @endforeach
                                                    </select>

                                                    <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-slate-500">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
                                                        </svg>
                                                    </span>
                                                </div>

                                                <x-input-error :messages="$errors->get('fund')" class="mt-2" />
                                            </div>

                                            <div>
                                                <label for="phone" class="mb-2 block text-sm font-semibold text-slate-800">
                                                    মোবাইল নম্বর <span class="text-rose-600">*</span>
                                                </label>

                                                <input
                                                    id="phone"
                                                    name="phone"
                                                    type="tel"
                                                    value="{{ $phoneValue }}"
                                                    x-model="phone"
                                                    class="donation-field block w-full rounded-2xl border border-slate-200 px-4 py-3.5 text-[15px] text-slate-900 shadow-[0_12px_26px_-22px_rgba(15,23,42,0.35)] placeholder:text-slate-400 transition focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/10"
                                                    placeholder="01XXXXXXXXX"
                                                    autocomplete="tel"
                                                    required
                                                >

                                                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                                            </div>

                                            <div>
                                                <label for="amount" class="mb-2 block text-sm font-semibold text-slate-800">
                                                    অনুদানের পরিমাণ <span class="text-rose-600">*</span>
                                                </label>

                                                <div class="relative">
                                                    <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-base font-semibold text-emerald-700">৳</span>
                                                    <input
                                                        id="amount"
                                                        name="amount"
                                                        type="number"
                                                        min="1"
                                                        step="0.01"
                                                        value="{{ $amountValue }}"
                                                        x-model="amount"
                                                        @input="selectedPreset = ''"
                                                        class="donation-field block w-full rounded-2xl border border-slate-200 py-3.5 pl-9 pr-4 text-[15px] text-slate-900 shadow-[0_12px_26px_-22px_rgba(15,23,42,0.35)] placeholder:text-slate-400 transition focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/10"
                                                        placeholder="পরিমাণ লিখুন"
                                                        inputmode="decimal"
                                                        autocomplete="off"
                                                        required
                                                    >
                                                </div>

                                                <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                                            </div>
                                        </div>

                                        <div>
                                            <p class="mb-3 text-sm font-semibold text-slate-700">দ্রুত নির্বাচন</p>

                                            <div class="flex flex-wrap gap-2.5">
                                                @foreach ($presetAmounts as $presetAmount)
                                                    <button
                                                        type="button"
                                                        class="rounded-full border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm font-semibold text-slate-800 shadow-sm transition hover:border-emerald-500 hover:bg-emerald-100 hover:text-emerald-900"
                                                        :class="selectedPreset === '{{ $presetAmount }}' && Number(amount) === {{ $presetAmount }}
                                                            ? 'border-emerald-700 bg-emerald-700 text-white shadow-[0_18px_36px_-28px_rgba(6,78,59,0.7)]'
                                                            : 'text-slate-800'"
                                                        @click="choosePreset({{ $presetAmount }})"
                                                    >
                                                        {{ number_format($presetAmount) }} টাকা
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="rounded-[1.5rem] border border-slate-200 bg-[linear-gradient(135deg,#f8fafc,#fefce8)] p-4 sm:p-5">
                                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                                <div>
                                                    <p class="text-[12px] font-semibold tracking-[0.16em] text-slate-500">
                                                        নিশ্চিতকরণ
                                                    </p>
                                                    <p class="mt-1.5 text-2xl font-semibold text-slate-950" x-text="summaryAmount()" aria-live="polite"></p>
                                                    <p class="mt-1 text-sm leading-6 text-slate-700">
                                                        তহবিল:
                                                        <span class="font-semibold text-slate-950" x-text="fundLabel()"></span>
                                                    </p>
                                                </div>

                                                <div class="max-w-xs text-sm leading-6 text-slate-600 sm:text-right">
                                                    নিরাপদ যাচাই শেষে আপনাকে চেকআউটে নিয়ে যাওয়া হবে।
                                                </div>
                                            </div>
                                        </div>

                                        <button
                                            type="submit"
                                            class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-emerald-700 px-6 py-4 text-base font-semibold text-white shadow-[0_28px_48px_-24px_rgba(5,150,105,0.55)] transition hover:bg-emerald-800 hover:translate-y-[-1px] hover:shadow-[0_32px_54px_-26px_rgba(6,95,70,0.62)] focus:outline-none focus:ring-4 focus:ring-emerald-200 disabled:cursor-not-allowed disabled:bg-slate-300 disabled:text-slate-500 disabled:shadow-none"
                                            :disabled="! canSubmit()"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7"/>
                                            </svg>
                                            এখনই অনুদান করুন
                                        </button>

                                        @if ($hasRegistrationAction)
                                            <p class="text-center text-sm leading-6 text-slate-600">
                                                দাতা অ্যাক্সেস প্রয়োজন হলে
                                                <a
                                                    href="{{ route('register.donor') }}"
                                                    class="font-semibold text-emerald-800 underline decoration-emerald-500 underline-offset-4 hover:text-emerald-900"
                                                >
                                                    আলাদা নিবন্ধন
                                                </a>
                                                করা যাবে।
                                            </p>
                                        @endif
                                    </form>
                                </div>
                            </div>

                            <div class="mt-4 grid gap-3 sm:grid-cols-3">
                                <div class="rounded-2xl border border-white/80 bg-white/70 px-4 py-3 text-center shadow-sm backdrop-blur">
                                    <p class="text-[11px] font-semibold tracking-[0.18em] text-slate-500">দ্রুত</p>
                                    <p class="mt-1 text-sm font-medium text-slate-800">কম ধাপে সম্পন্ন</p>
                                </div>
                                <div class="rounded-2xl border border-white/80 bg-white/70 px-4 py-3 text-center shadow-sm backdrop-blur">
                                    <p class="text-[11px] font-semibold tracking-[0.18em] text-slate-500">নিরাপদ</p>
                                    <p class="mt-1 text-sm font-medium text-slate-800">যাচাইকৃত চেকআউট</p>
                                </div>
                                <div class="rounded-2xl border border-white/80 bg-white/70 px-4 py-3 text-center shadow-sm backdrop-blur">
                                    <p class="text-[11px] font-semibold tracking-[0.18em] text-slate-500">সহজ</p>
                                    <p class="mt-1 text-sm font-medium text-slate-800">মোবাইলবান্ধব অভিজ্ঞতা</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 sm:mt-10 lg:mt-12">
                <div class="overflow-hidden rounded-[2rem] border border-white/70 bg-white/75 px-5 py-6 shadow-[0_28px_70px_-38px_rgba(15,23,42,0.24)] backdrop-blur-xl sm:px-7 sm:py-8 lg:px-10 lg:py-10">
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                        <div class="max-w-3xl">
                            <span class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-[12px] font-semibold text-emerald-800">
                                অনুদানের খাতসমূহ
                            </span>

                            <h3 class="mt-3 text-2xl font-semibold tracking-tight text-slate-900 sm:text-3xl lg:text-[2.2rem]">
                                আপনার অনুদানের জন্য উপযুক্ত তহবিল বেছে নিন
                            </h3>

                            <p class="mt-3 max-w-2xl text-sm leading-7 text-slate-600 sm:text-base">
                                নিচের যেকোনো তহবিল নির্বাচন করলে উপরের Quick Donation ফর্মে সেটি স্বয়ংক্রিয়ভাবে নির্বাচন হবে। এতে আপনার মূল donation flow একদম একই থাকবে, শুধু ব্যবহার করা হবে আরও সহজভাবে।
                            </p>
                        </div>

                        <a
                            href="#quick-donation-form"
                            class="inline-flex items-center gap-2 self-start rounded-full border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-emerald-500 hover:text-emerald-700"
                        >
                            উপরের ফর্মে যান
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6"/>
                            </svg>
                        </a>
                    </div>

                    <div class="mt-7 grid gap-5 sm:mt-8 md:grid-cols-2 xl:grid-cols-3">
                        @foreach ($showcaseFunds as $fundItem)
                            <article
                                class="donation-showcase-card group relative overflow-hidden rounded-[1.75rem] border border-slate-200/80 bg-white transition duration-300 hover:-translate-y-1"
                            >
                                <div class="absolute inset-x-0 top-0 h-32 bg-gradient-to-r {{ $fundItem['accent'] }}"></div>
                                <div class="absolute right-0 top-0 h-24 w-24 translate-x-6 -translate-y-6 rounded-full bg-white/50 blur-2xl"></div>
                                <div class="absolute inset-x-0 top-0 h-px bg-white/80"></div>

                                <div class="relative flex h-full flex-col p-5 sm:p-6">
                                    <div class="flex items-start justify-between gap-4">
                                        <span class="inline-flex items-center rounded-full border border-slate-200 bg-white/90 px-3 py-1 text-[11px] font-semibold tracking-[0.18em] text-slate-700 shadow-sm backdrop-blur">
                                            {{ $fundItem['badge'] }}
                                        </span>

                                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl border border-white/80 bg-white/90 text-emerald-700 shadow-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6"/>
                                            </svg>
                                        </div>
                                    </div>

                                    <div class="mt-6">
                                        <h4 class="text-xl font-semibold leading-8 text-slate-900">
                                            {{ $fundItem['label'] }}
                                        </h4>

                                        <p class="mt-3 text-sm leading-7 text-slate-600">
                                            {{ $fundItem['description'] }}
                                        </p>
                                    </div>

                                    <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center">
                                        <button
                                            type="button"
                                            @click="chooseFundAndScroll('{{ $fundItem['key'] }}', true)"
                                            class="inline-flex flex-1 items-center justify-center gap-2 rounded-2xl bg-emerald-700 px-5 py-3.5 text-sm font-semibold text-white shadow-[0_24px_48px_-28px_rgba(5,150,105,0.58)] transition hover:bg-emerald-800"
                                        >
                                            তহবিলে অনুদান দিন
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6"/>
                                            </svg>
                                        </button>

                                        <button
                                            type="button"
                                            @click="chooseFundAndScroll('{{ $fundItem['key'] }}')"
                                            class="inline-flex items-center justify-center rounded-2xl border border-slate-300 bg-white px-4 py-3.5 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50"
                                        >
                                            নির্বাচন করুন
                                        </button>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-public-shell>