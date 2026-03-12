<x-guest-layout>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap');

        .ma-login-page,
        .ma-login-page * {
            font-family: "Hind Siliguri", ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .ma-login-page {
            min-height: 100vh;
            background:
                radial-gradient(circle at top left, rgba(16, 185, 129, 0.08), transparent 26%),
                radial-gradient(circle at bottom right, rgba(245, 158, 11, 0.08), transparent 22%),
                linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
        }

        .ma-shell {
            background: rgba(255, 255, 255, 0.72);
            border: 1px solid rgba(255, 255, 255, 0.72);
            backdrop-filter: blur(14px);
            box-shadow:
                0 30px 80px -42px rgba(15, 23, 42, 0.22),
                0 18px 30px -24px rgba(15, 23, 42, 0.12);
        }

        .ma-hero-panel {
            position: relative;
            overflow: hidden;
            background:
                radial-gradient(circle at top left, rgba(255,255,255,0.12), transparent 26%),
                radial-gradient(circle at bottom right, rgba(251,191,36,0.10), transparent 22%),
                linear-gradient(145deg, rgba(2,6,23,0.98) 0%, rgba(6,78,59,0.95) 55%, rgba(15,23,42,0.96) 100%);
        }

        .ma-hero-panel::before {
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

        .ma-hero-panel::after {
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

        .ma-login-card {
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid rgba(226, 232, 240, 0.9);
            box-shadow:
                0 24px 54px -34px rgba(15, 23, 42, 0.28),
                0 12px 22px -18px rgba(15, 23, 42, 0.14);
        }

        .ma-soft-card {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.10);
            backdrop-filter: blur(10px);
        }

        .ma-soft-box {
            background: #f8fafc;
            border: 1px solid rgba(226, 232, 240, 0.9);
            box-shadow:
                0 12px 30px -24px rgba(15, 23, 42, 0.12),
                inset 0 1px 0 rgba(255,255,255,0.75);
        }

        .ma-badge {
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

        .ma-badge-dot {
            width: 8px;
            height: 8px;
            border-radius: 9999px;
            background: #6ee7b7;
        }

        .ma-divider {
            display: flex;
            align-items: center;
            gap: 16px;
            color: #64748b;
            font-size: 14px;
            line-height: 20px;
        }

        .ma-divider::before,
        .ma-divider::after {
            content: "";
            flex: 1 1 0%;
            height: 1px;
            background: linear-gradient(90deg, rgba(148,163,184,0), rgba(148,163,184,0.5), rgba(148,163,184,0));
        }

        .ma-google-btn {
            min-height: 52px;
        }

        .ma-submit-btn {
            min-height: 56px;
            box-shadow: 0 24px 44px -24px rgba(5, 150, 105, 0.50);
        }

        .ma-submit-btn:hover {
            box-shadow: 0 28px 50px -26px rgba(6, 95, 70, 0.58);
        }

        .ma-input {
            min-height: 52px;
            background: rgba(255,255,255,0.96);
            box-shadow: 0 10px 22px -18px rgba(15, 23, 42, 0.22);
        }

        @media (max-width: 1023px) {
            .ma-hero-content {
                padding-bottom: 1.5rem;
            }
        }
    </style>

    <div class="ma-login-page px-4 py-6 sm:px-6 sm:py-8 lg:px-8 lg:py-10">
        <div class="mx-auto max-w-7xl">
            <div class="ma-shell overflow-hidden rounded-3xl">
                <div class="grid lg:grid-cols-2">
                    <section class="ma-hero-panel">
                        <div class="relative z-10 h-full p-6 sm:p-8 lg:p-10">
                            <div class="grid h-full gap-8 lg:grid-rows-[1fr_auto]">
                                <div class="ma-hero-content">
                                    <span class="ma-badge">
                                        <span class="ma-badge-dot"></span>
                                        SECURE ACCOUNT ACCESS
                                    </span>

                                    <h1 class="mt-5 max-w-2xl text-3xl font-semibold leading-tight text-white sm:text-4xl lg:text-5xl">
                                        নিরাপদ, পরিষ্কার ও দ্রুতভাবে আপনার অ্যাকাউন্টে প্রবেশ করুন
                                    </h1>

                                    <p class="mt-4 max-w-2xl text-sm leading-7 text-white/80 sm:text-base">
                                        পরিচিত sign-in flow, উন্নত presentation, কম বিভ্রান্তি এবং production-grade user experience—সবকিছু এমনভাবে সাজানো হয়েছে যেন প্রথম দেখাতেই ব্যবহারকারী বুঝতে পারে কোথায় কী করতে হবে।
                                    </p>

                                    <div class="mt-8 grid gap-3 sm:grid-cols-3">
                                        <div class="ma-soft-card rounded-2xl px-4 py-4 text-white/90">
                                            <p class="text-xs font-semibold tracking-widest text-white/60">FAST</p>
                                            <p class="mt-2 text-sm font-medium leading-6">দ্রুত প্রবেশ</p>
                                        </div>

                                        <div class="ma-soft-card rounded-2xl px-4 py-4 text-white/90">
                                            <p class="text-xs font-semibold tracking-widest text-white/60">CLEAR</p>
                                            <p class="mt-2 text-sm font-medium leading-6">পরিষ্কার নির্দেশনা</p>
                                        </div>

                                        <div class="ma-soft-card rounded-2xl px-4 py-4 text-white/90">
                                            <p class="text-xs font-semibold tracking-widest text-white/60">TRUSTED</p>
                                            <p class="mt-2 text-sm font-medium leading-6">বিদ্যমান auth rules</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="ma-soft-card rounded-3xl p-5 text-white/90">
                                    <div class="flex items-start gap-3">
                                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-white/10">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-emerald-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3 4 7v6c0 5 3.4 7.9 8 9 4.6-1.1 8-4 8-9V7l-8-4Z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m9.5 12 1.7 1.7L15 9.9"/>
                                            </svg>
                                        </div>

                                        <div>
                                            <p class="text-sm font-semibold text-white">আপনার প্রবেশাধিকার নিরাপদভাবেই পরিচালিত হবে</p>
                                            <p class="mt-1 text-sm leading-6 text-white/75">
                                                UI ও layout উন্নত করা হয়েছে, কিন্তু backend authentication, route behavior, session handling এবং security logic অপরিবর্তিত রাখা হয়েছে।
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="bg-gradient-to-b from-amber-50/40 via-white to-slate-50 p-4 sm:p-6 lg:p-8">
                        <div class="mx-auto flex h-full max-w-xl items-center">
                            <div class="ma-login-card w-full rounded-3xl overflow-hidden">
                                <div class="border-b border-slate-100 bg-gradient-to-r from-amber-50 via-white to-emerald-50 px-5 py-5 sm:px-7 sm:py-6">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <span class="inline-flex items-center rounded-full border border-emerald-300 bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-900">
                                                ACCOUNT SIGN-IN
                                            </span>

                                            <h2 class="mt-3 text-2xl font-semibold leading-tight text-slate-950 sm:text-3xl">
                                                স্বাগতম, আবার ফিরে আসুন
                                            </h2>

                                            <p class="mt-2 text-sm leading-6 text-slate-600">
                                                আপনার পছন্দের sign-in পদ্ধতি বেছে নিয়ে নিরাপদভাবে প্রবেশ করুন।
                                            </p>
                                        </div>

                                        <div class="hidden rounded-2xl border border-white bg-white p-3 shadow-sm sm:flex sm:items-center sm:justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-emerald-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3 4 7v6c0 5 3.4 7.9 8 9 4.6-1.1 8-4 8-9V7l-8-4Z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m9.5 12 1.7 1.7L15 9.9"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <div class="px-5 py-5 sm:px-7 sm:py-7">
                                    <x-auth-session-status :status="session('status')" />

                                    @if (session('google_auth_message'))
                                        <div class="mt-4">
                                            <x-ui.alert variant="success" title="Google sign-in ready">
                                                {{ session('google_auth_message') }}
                                            </x-ui.alert>
                                        </div>
                                    @endif

                                    @if (session('google_auth_warning'))
                                        <div class="mt-4">
                                            <x-ui.alert variant="warning" title="Google sign-in notice">
                                                {{ session('google_auth_warning') }}
                                            </x-ui.alert>
                                        </div>
                                    @endif

                                    <div class="mt-4 rounded-3xl border border-slate-200 bg-slate-50 px-4 py-4 sm:px-5">
                                        <p class="text-xs font-semibold tracking-widest text-slate-500">
                                            QUICK ACCESS
                                        </p>

                                        <h3 class="mt-2 text-lg font-semibold text-slate-950">
                                            Google দিয়ে দ্রুত সাইন-ইন
                                        </h3>

                                        <p class="mt-1 text-sm leading-6 text-slate-600">
                                            পরিচিত ও দ্রুত একটি পদ্ধতিতে আপনার অ্যাকাউন্টে প্রবেশ করুন।
                                        </p>

                                        <a
                                            href="{{ route('google.redirect', ['intent' => 'public']) }}"
                                            class="ma-google-btn mt-4 inline-flex w-full items-center justify-center gap-3 rounded-2xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-800 shadow-sm transition hover:border-emerald-500 hover:bg-emerald-50 hover:text-emerald-900 focus:outline-none focus:ring-4 focus:ring-emerald-100"
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

                                    <div class="mt-6 ma-divider">
                                        অথবা ইমেইল ও পাসওয়ার্ড ব্যবহার করুন
                                    </div>

                                    <div class="mt-6">
                                        <div class="mb-5">
                                            <p class="text-xs font-semibold tracking-widest text-slate-500">
                                                STANDARD SIGN-IN
                                            </p>

                                            <h3 class="mt-2 text-xl font-semibold text-slate-950">
                                                ইমেইল ও পাসওয়ার্ড দিয়ে লগইন করুন
                                            </h3>

                                            <p class="mt-1 text-sm leading-6 text-slate-600">
                                                আপনার বিদ্যমান অ্যাকাউন্ট যাচাই করে নিরাপদভাবে session শুরু হবে।
                                            </p>
                                        </div>

                                        <form method="POST" action="{{ route('login') }}" class="space-y-5">
                                            @csrf

                                            <div>
                                                <x-input-label for="email" :value="__('ইমেইল ঠিকানা')" />
                                                <x-text-input
                                                    id="email"
                                                    class="ma-input mt-1 block w-full rounded-2xl border-slate-300 px-4 py-3 text-slate-900 focus:border-emerald-600 focus:ring-emerald-100"
                                                    type="email"
                                                    name="email"
                                                    :value="old('email')"
                                                    required
                                                    autofocus
                                                    autocomplete="username"
                                                    placeholder="আপনার ইমেইল ঠিকানা লিখুন"
                                                />
                                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                            </div>

                                            <div>
                                                <div class="flex items-center justify-between gap-3">
                                                    <x-input-label for="password" :value="__('পাসওয়ার্ড')" />

                                                    @if (Route::has('password.request'))
                                                        <a href="{{ route('password.request') }}" class="text-sm font-semibold text-emerald-800 underline decoration-emerald-400 underline-offset-4 hover:text-emerald-900">
                                                            pাসওয়ার্ড ভুলে গেছেন?
                                                        </a>
                                                    @endif
                                                </div>

                                                <x-text-input
                                                    id="password"
                                                    class="ma-input mt-1 block w-full rounded-2xl border-slate-300 px-4 py-3 text-slate-900 focus:border-emerald-600 focus:ring-emerald-100"
                                                    type="password"
                                                    name="password"
                                                    required
                                                    autocomplete="current-password"
                                                    placeholder="আপনার পাসওয়ার্ড লিখুন"
                                                />
                                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                            </div>

                                            <label for="remember_me" class="ma-soft-box flex items-start gap-3 rounded-2xl px-4 py-4">
                                                <input id="remember_me" type="checkbox" name="remember" class="ui-checkbox mt-1">

                                                <span>
                                                    <span class="block text-sm font-semibold text-slate-900">এই ডিভাইসে মনে রাখুন</span>
                                                    <span class="mt-1 block text-sm leading-6 text-slate-600">
                                                        এটি যদি আপনার ব্যক্তিগত ও বিশ্বস্ত ডিভাইস হয়, তাহলে পরবর্তীবার আরও দ্রুত প্রবেশের জন্য session মনে রাখা যেতে পারে।
                                                    </span>
                                                </span>
                                            </label>

                                            <button
                                                type="submit"
                                                class="ma-submit-btn inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-emerald-700 px-6 py-4 text-base font-semibold text-white transition hover:bg-emerald-800 focus:outline-none focus:ring-4 focus:ring-emerald-200"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 5l7 7-7 7"/>
                                                </svg>
                                                লগইন করুন
                                            </button>
                                        </form>
                                    </div>

                                    <div class="mt-6 grid gap-3 sm:grid-cols-3">
                                        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-center">
                                            <p class="text-xs font-semibold tracking-widest text-slate-500">STREAMLINED</p>
                                            <p class="mt-1 text-sm font-medium text-slate-800">কম ধাপে প্রবেশ</p>
                                        </div>

                                        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-center">
                                            <p class="text-xs font-semibold tracking-widest text-slate-500">CLARITY</p>
                                            <p class="mt-1 text-sm font-medium text-slate-800">পরিষ্কার নির্দেশনা</p>
                                        </div>

                                        <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-center">
                                            <p class="text-xs font-semibold tracking-widest text-slate-500">RELIABLE</p>
                                            <p class="mt-1 text-sm font-medium text-slate-800">বিশ্বস্ত sign-in UI</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>