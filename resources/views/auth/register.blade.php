<x-guest-layout>
    @php
        $registrationIntent = $registrationIntent ?? old('intent', 'public');

        $intentCards = [
            [
                'intent' => 'public',
                'title' => 'সাধারণ অ্যাকাউন্ট',
                'subtitle' => 'General foundation',
                'description' => 'কোনো donor বা guardian portal access না খুলেই একটি shared account শুরু করুন।',
                'route' => route('register'),
            ],
            [
                'intent' => 'donor',
                'title' => 'দাতা ভিত্তি',
                'subtitle' => 'Donor foundation',
                'description' => 'একই অ্যাকাউন্টে donor intent রেকর্ড করুন, তবে protected donor access এখনও সক্রিয় হবে না।',
                'route' => route('register.donor'),
            ],
            [
                'intent' => 'guardian',
                'title' => 'অভিভাবক ভিত্তি',
                'subtitle' => 'Guardian foundation',
                'description' => 'unlinked guardian foundation তৈরি করুন, কিন্তু কোনো protected guardian access খোলা হবে না।',
                'route' => route('register.guardian'),
            ],
        ];
    @endphp

    <style>
        .ma-register-page {
            min-height: 100%;
        }

        .ma-register-shell {
            background: rgba(255, 255, 255, 0.72);
            border: 1px solid rgba(255, 255, 255, 0.72);
            backdrop-filter: blur(14px);
            box-shadow:
                0 30px 80px -42px rgba(15, 23, 42, 0.22),
                0 18px 30px -24px rgba(15, 23, 42, 0.12);
        }

        .ma-register-hero {
            position: relative;
            overflow: hidden;
            background:
                radial-gradient(circle at top left, rgba(255,255,255,0.12), transparent 26%),
                radial-gradient(circle at bottom right, rgba(251,191,36,0.10), transparent 22%),
                linear-gradient(145deg, rgba(2,6,23,0.98) 0%, rgba(6,78,59,0.95) 55%, rgba(15,23,42,0.96) 100%);
        }

        .ma-register-hero::before {
            content: "";
            position: absolute;
            left: -50px;
            top: 40px;
            width: 180px;
            height: 180px;
            border-radius: 9999px;
            background: rgba(52, 211, 153, 0.10);
            filter: blur(40px);
        }

        .ma-register-hero::after {
            content: "";
            position: absolute;
            right: -50px;
            bottom: 10px;
            width: 220px;
            height: 220px;
            border-radius: 9999px;
            background: rgba(251, 191, 36, 0.08);
            filter: blur(48px);
        }

        .ma-register-glass {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.10);
            backdrop-filter: blur(10px);
        }

        .ma-register-card {
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid rgba(226, 232, 240, 0.9);
            box-shadow:
                0 24px 54px -34px rgba(15, 23, 42, 0.28),
                0 12px 22px -18px rgba(15, 23, 42, 0.14);
        }

        .ma-register-choice {
            display: block;
            border-radius: 1rem;
            border: 1px solid rgb(226 232 240);
            background: rgb(248 250 252);
            padding: 1rem;
            transition: all 160ms ease;
        }

        .ma-register-choice:hover {
            border-color: rgb(16 185 129);
            background: rgb(240 253 250);
        }

        .ma-register-choice--active {
            border-color: rgb(16 185 129);
            background: linear-gradient(180deg, rgba(236,253,245,1) 0%, rgba(255,255,255,1) 100%);
            box-shadow: 0 16px 30px -24px rgba(5, 150, 105, 0.40);
        }

        .ma-register-soft {
            background: #f8fafc;
            border: 1px solid rgba(226, 232, 240, 0.9);
            box-shadow:
                0 12px 30px -24px rgba(15, 23, 42, 0.12),
                inset 0 1px 0 rgba(255,255,255,0.75);
        }

        .ma-register-input {
            min-height: 52px;
            background: rgba(255,255,255,0.96);
            box-shadow: 0 10px 22px -18px rgba(15, 23, 42, 0.22);
        }

        .ma-register-google-btn {
            min-height: 52px;
        }

        .ma-register-submit-btn {
            min-height: 56px;
            box-shadow: 0 24px 44px -24px rgba(5, 150, 105, 0.50);
        }

        .ma-register-submit-btn:hover {
            box-shadow: 0 28px 50px -26px rgba(6, 95, 70, 0.58);
        }

        .ma-register-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            border-radius: 9999px;
            border: 1px solid rgba(255,255,255,0.14);
            background: rgba(255,255,255,0.10);
            color: rgba(255,255,255,0.92);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.14em;
        }

        .ma-register-badge-dot {
            width: 8px;
            height: 8px;
            border-radius: 9999px;
            background: #6ee7b7;
        }

        .ma-register-divider {
            display: flex;
            align-items: center;
            gap: 16px;
            color: #64748b;
            font-size: 14px;
            line-height: 20px;
        }

        .ma-register-divider::before,
        .ma-register-divider::after {
            content: "";
            flex: 1 1 0%;
            height: 1px;
            background: linear-gradient(90deg, rgba(148,163,184,0), rgba(148,163,184,0.5), rgba(148,163,184,0));
        }
    </style>

    <div class="ma-register-page">
        <div class="mx-auto max-w-7xl">
            <div class="ma-register-shell overflow-hidden rounded-3xl">
                <div class="grid lg:grid-cols-2">
                    <section class="ma-register-hero">
                        <div class="relative z-10 h-full p-6 sm:p-8 lg:p-10">
                            <div class="grid h-full gap-8 lg:grid-rows-[1fr_auto]">
                                <div>
                                    <span class="ma-register-badge">
                                        <span class="ma-register-badge-dot"></span>
                                        ACCOUNT REGISTRATION
                                    </span>

                                    <h1 class="mt-5 max-w-2xl text-3xl font-semibold leading-tight text-white sm:text-4xl lg:text-5xl">
                                        একটি নিরাপদ, পরিপাটি ও ভবিষ্যৎ-প্রস্তুত অ্যাকাউন্ট শুরু করুন
                                    </h1>

                                    <p class="mt-4 max-w-2xl text-sm leading-7 text-white/80 sm:text-base">
                                        সাধারণ ব্যবহার, donor intent অথবা guardian foundation—যে উদ্দেশ্যেই শুরু করুন, registration journey একই design language-এর মধ্যে, কিন্তু access boundary নিজ নিজ নিয়মে সুরক্ষিত থাকবে।
                                    </p>

                                    <div class="mt-8 grid gap-3 sm:grid-cols-3">
                                        <div class="ma-register-glass rounded-2xl px-4 py-4 text-white/90">
                                            <p class="text-xs font-semibold tracking-widest text-white/60">STRUCTURED</p>
                                            <p class="mt-2 text-sm font-medium leading-6">পরিষ্কার ধাপ</p>
                                        </div>

                                        <div class="ma-register-glass rounded-2xl px-4 py-4 text-white/90">
                                            <p class="text-xs font-semibold tracking-widest text-white/60">TRUSTED</p>
                                            <p class="mt-2 text-sm font-medium leading-6">নিরাপদ ভিত্তি</p>
                                        </div>

                                        <div class="ma-register-glass rounded-2xl px-4 py-4 text-white/90">
                                            <p class="text-xs font-semibold tracking-widest text-white/60">BANGLA-FIRST</p>
                                            <p class="mt-2 text-sm font-medium leading-6">সহজ বোঝাপড়া</p>
                                        </div>
                                    </div>

                                    <div class="mt-8 grid gap-3">
                                        @foreach ($intentCards as $card)
                                            @php
                                                $active = $registrationIntent === $card['intent'];
                                            @endphp

                                            <a href="{{ $card['route'] }}" class="ma-register-glass rounded-2xl px-4 py-4 text-white/90 {{ $active ? 'ring-1 ring-emerald-300/60' : '' }}">
                                                <div class="flex items-start justify-between gap-3">
                                                    <div>
                                                        <p class="text-xs font-semibold tracking-widest text-white/60">{{ strtoupper($card['subtitle']) }}</p>
                                                        <h3 class="mt-2 text-base font-semibold text-white">{{ $card['title'] }}</h3>
                                                        <p class="mt-1 text-sm leading-6 text-white/75">{{ $card['description'] }}</p>
                                                    </div>

                                                    @if ($active)
                                                        <span class="rounded-full bg-emerald-300 px-3 py-1 text-xs font-semibold text-emerald-950">
                                                            নির্বাচিত
                                                        </span>
                                                    @endif
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="ma-register-glass rounded-3xl p-5 text-white/90">
                                    <div class="flex items-start gap-3">
                                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-white/10">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3 4 7v6c0 5 3.4 7.9 8 9 4.6-1.1 8-4 8-9V7l-8-4Z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m9.5 12 1.7 1.7L15 9.9"/>
                                            </svg>
                                        </div>

                                        <div>
                                            <p class="text-sm font-semibold text-white">Registration boundary নিরাপদভাবেই বজায় থাকবে</p>
                                            <p class="mt-1 text-sm leading-6 text-white/75">
                                                এই ধাপে shared identity এবং প্রয়োজন হলে intent মাত্র রেকর্ড হয়। donor portal access, guardian linkage, invoice/payment visibility বা protected flow এখান থেকে সরাসরি খোলা হয় না।
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="bg-gradient-to-b from-amber-50/40 via-white to-slate-50 p-4 sm:p-6 lg:p-8">
                        <div class="mx-auto flex h-full max-w-xl items-center">
                            <div class="ma-register-card w-full overflow-hidden rounded-3xl">
                                <div class="border-b border-slate-100 bg-gradient-to-r from-amber-50 via-white to-emerald-50 px-5 py-5 sm:px-7 sm:py-6">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <span class="inline-flex items-center rounded-full border border-emerald-300 bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-900">
                                                REGISTRATION FORM
                                            </span>

                                            <h2 class="mt-3 text-2xl font-semibold leading-tight text-slate-950 sm:text-3xl">
                                                প্রয়োজনীয় তথ্য দিয়ে অ্যাকাউন্ট তৈরি করুন
                                            </h2>

                                            <p class="mt-2 text-sm leading-6 text-slate-600">
                                                সব field, validation rule এবং registration behavior অপরিবর্তিত রেখে hierarchy, readability এবং premium presentation উন্নত করা হয়েছে।
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="px-5 py-5 sm:px-7 sm:py-7">
                                    <x-ui.alert variant="info" title="Registration boundary">
                                        এই ধাপে shared identity এবং প্রয়োজন হলে donor বা guardian intent মাত্র সেট হয়। donor portal access, guardian linkage, invoice/payment visibility বা অন্য protected flow খোলা হয় না।
                                    </x-ui.alert>

                                    @if ($registrationIntent === 'guardian')
                                        <div class="mt-4">
                                            <x-ui.alert variant="warning" title="Guardian Google note">
                                                প্রথমবার guardian foundation তৈরি করতে ইমেইল রেজিস্ট্রেশনই ব্যবহার করুন। Google linking পরে profile থেকে করা যাবে।
                                            </x-ui.alert>
                                        </div>
                                    @else
                                        <div class="mt-4 rounded-3xl border border-slate-200 bg-slate-50 px-4 py-4 sm:px-5">
                                            <p class="text-xs font-semibold tracking-widest text-slate-500">
                                                QUICK REGISTRATION
                                            </p>

                                            <h3 class="mt-2 text-lg font-semibold text-slate-950">
                                                Google দিয়ে দ্রুত অ্যাকাউন্ট শুরু করুন
                                            </h3>

                                            <p class="mt-1 text-sm leading-6 text-slate-600">
                                                shared account foundation শুরু করা যাবে, তবে protected donor বা guardian access এখনও সক্রিয় হবে না।
                                            </p>

                                            <a
                                                href="{{ route('google.redirect', ['intent' => $registrationIntent]) }}"
                                                class="ma-register-google-btn mt-4 inline-flex w-full items-center justify-center gap-3 rounded-2xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-800 shadow-sm transition hover:border-emerald-500 hover:bg-emerald-50 hover:text-emerald-900 focus:outline-none focus:ring-4 focus:ring-emerald-100"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" class="h-5 w-5" aria-hidden="true">
                                                    <path fill="#FFC107" d="M43.611 20.083H42V20H24v8h11.303C33.659 32.657 29.244 36 24 36c-6.627 0-12-5.373-12-12S17.373 12 24 12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.27 4 24 4 12.955 4 4 12.955 4 24s8.955 20 20 20 20-8.955 20-20c0-1.341-.138-2.65-.389-3.917z"/>
                                                    <path fill="#FF3D00" d="M6.306 14.691l6.571 4.819C14.655 16.108 19.005 12 24 12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.27 4 24 4 16.318 4 9.656 8.337 6.306 14.691z"/>
                                                    <path fill="#4CAF50" d="M24 44c5.145 0 9.84-1.977 13.409-5.192l-6.19-5.238C29.165 35.091 26.715 36 24 36c-5.223 0-9.625-3.316-11.283-7.946l-6.522 5.025C9.505 39.556 16.227 44 24 44z"/>
                                                    <path fill="#1976D2" d="M43.611 20.083H42V20H24v8h11.303a12.05 12.05 0 0 1-4.084 5.571l.003-.002 6.19 5.238C36.971 39.205 44 34 44 24c0-1.341-.138-2.65-.389-3.917z"/>
                                                </svg>
                                                Google দিয়ে চালিয়ে যান
                                            </a>
                                        </div>
                                    @endif

                                    <div class="mt-6 ma-register-divider">
                                        অথবা তথ্য দিয়ে নিবন্ধন সম্পন্ন করুন
                                    </div>

                                    <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-5">
                                        @csrf

                                        <input type="hidden" name="intent" value="{{ old('intent', $registrationIntent) }}">

                                        <div class="grid gap-5 sm:grid-cols-2">
                                            <div class="sm:col-span-2">
                                                <x-input-label for="name" :value="__('নাম / Name')" />
                                                <x-text-input
                                                    id="name"
                                                    class="ma-register-input mt-1 block w-full rounded-2xl border-slate-300 px-4 py-3 text-slate-900 focus:border-emerald-600 focus:ring-emerald-100"
                                                    type="text"
                                                    name="name"
                                                    :value="old('name')"
                                                    required
                                                    autofocus
                                                    autocomplete="name"
                                                    placeholder="আপনার পূর্ণ নাম লিখুন"
                                                />
                                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                            </div>

                                            <div>
                                                <x-input-label for="email" :value="__('ইমেইল / Email')" />
                                                <x-text-input
                                                    id="email"
                                                    class="ma-register-input mt-1 block w-full rounded-2xl border-slate-300 px-4 py-3 text-slate-900 focus:border-emerald-600 focus:ring-emerald-100"
                                                    type="email"
                                                    name="email"
                                                    :value="old('email')"
                                                    required
                                                    autocomplete="username"
                                                    placeholder="আপনার ইমেইল ঠিকানা লিখুন"
                                                />
                                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                            </div>

                                            <div>
                                                <x-input-label for="phone" :value="__('ফোন (ঐচ্ছিক) / Phone')" />
                                                <x-text-input
                                                    id="phone"
                                                    class="ma-register-input mt-1 block w-full rounded-2xl border-slate-300 px-4 py-3 text-slate-900 focus:border-emerald-600 focus:ring-emerald-100"
                                                    type="tel"
                                                    name="phone"
                                                    :value="old('phone')"
                                                    autocomplete="tel"
                                                    placeholder="ইচ্ছা হলে ফোন নম্বর দিন"
                                                />
                                                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                                                <p class="mt-2 text-sm leading-6 text-slate-500">
                                                    এটি account-level optional phone. পরে আলাদা করে verify করা যাবে।
                                                </p>
                                            </div>

                                            <div>
                                                <x-input-label for="password" :value="__('পাসওয়ার্ড / Password')" />
                                                <x-text-input
                                                    id="password"
                                                    class="ma-register-input mt-1 block w-full rounded-2xl border-slate-300 px-4 py-3 text-slate-900 focus:border-emerald-600 focus:ring-emerald-100"
                                                    type="password"
                                                    name="password"
                                                    required
                                                    autocomplete="new-password"
                                                    placeholder="একটি শক্তিশালী পাসওয়ার্ড দিন"
                                                />
                                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                            </div>

                                            <div>
                                                <x-input-label for="password_confirmation" :value="__('পাসওয়ার্ড নিশ্চিতকরণ / Confirm Password')" />
                                                <x-text-input
                                                    id="password_confirmation"
                                                    class="ma-register-input mt-1 block w-full rounded-2xl border-slate-300 px-4 py-3 text-slate-900 focus:border-emerald-600 focus:ring-emerald-100"
                                                    type="password"
                                                    name="password_confirmation"
                                                    required
                                                    autocomplete="new-password"
                                                    placeholder="পাসওয়ার্ড আবার লিখুন"
                                                />
                                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                                            </div>
                                        </div>

                                        <div class="flex flex-col-reverse gap-3 pt-2 sm:flex-row sm:items-center sm:justify-between">
                                            <a href="{{ route('login') }}" class="text-sm font-semibold text-emerald-800 underline decoration-emerald-400 underline-offset-4 hover:text-emerald-900">
                                                আগেই অ্যাকাউন্ট আছে?
                                            </a>

                                            <button
                                                type="submit"
                                                class="ma-register-submit-btn inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-emerald-700 px-6 py-4 text-base font-semibold text-white transition hover:bg-emerald-800 focus:outline-none focus:ring-4 focus:ring-emerald-200 sm:w-auto"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                                </svg>
                                                অ্যাকাউন্ট তৈরি করুন
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>