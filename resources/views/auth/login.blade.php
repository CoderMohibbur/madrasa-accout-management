<x-guest-layout>
    <div class="ui-auth-section">
        <x-auth-session-status :status="session('status')" />

        @if (session('google_auth_message'))
            <x-ui.alert variant="success" title="Google sign-in ready">
                {{ session('google_auth_message') }}
            </x-ui.alert>
        @endif

        @if (session('google_auth_warning'))
            <x-ui.alert variant="warning" title="Google sign-in notice">
                {{ session('google_auth_warning') }}
            </x-ui.alert>
        @endif

        <div class="grid gap-5 lg:grid-cols-[0.8fr,1.2fr]">
            <article class="ui-public-card ui-public-card--soft">
                <p class="ui-public-card__eyebrow">বিকল্প প্রবেশ</p>
                <h3 class="ui-public-card__title">Google দিয়ে দ্রুত প্রবেশ</h3>
                <p class="ui-public-card__body">
                    একই shared account model বজায় রেখেই Google sign-in ব্যবহার করা যাবে। এতে donor, guardian বা
                    management boundary বদলায় না; কেবল বিদ্যমান বা ন্যূনতম প্রয়োজনীয় account foundation-এই কাজ হয়।
                </p>

                <div class="mt-6 space-y-3">
                    <div class="ui-auth-note ui-auth-note--soft">
                        নতুন ব্যবহারকারীর ক্ষেত্রেও public-safe account foundation-এর বাইরে কিছুই খোলা হয় না।
                    </div>

                    <a href="{{ route('google.redirect', ['intent' => 'public']) }}" class="ui-public-action ui-public-action--secondary w-full justify-center">
                        Google দিয়ে চালিয়ে যান
                    </a>
                </div>
            </article>

            <article class="ui-public-card">
                <p class="ui-public-card__eyebrow">ইমেইল প্রবেশ</p>
                <h3 class="ui-public-card__title">ইমেইল ও পাসওয়ার্ড দিয়ে লগইন করুন</h3>
                <p class="ui-public-card__body">
                    নিচের তথ্যগুলো আপনার বিদ্যমান অ্যাকাউন্টের সাথে যাচাই হবে। login flow, session behavior এবং
                    access rules অপরিবর্তিত রাখা হয়েছে।
                </p>

                <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-5">
                    @csrf

                    <div>
                        <x-input-label for="email" :value="__('ইমেইল / Email')" />
                        <x-text-input
                            id="email"
                            class="mt-1 block w-full"
                            type="email"
                            name="email"
                            :value="old('email')"
                            required
                            autofocus
                            autocomplete="username"
                        />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password" :value="__('পাসওয়ার্ড / Password')" />
                        <x-text-input
                            id="password"
                            class="mt-1 block w-full"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                        />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="ui-auth-note">
                        <label for="remember_me" class="flex items-start gap-3">
                            <input id="remember_me" type="checkbox" name="remember" class="ui-checkbox mt-1">

                            <span>
                                <span class="block text-sm font-semibold text-slate-900">{{ __('মনে রাখুন / Remember me') }}</span>
                                <span class="mt-1 block text-sm leading-6 text-slate-600">
                                    একই বিশ্বস্ত ব্রাউজার থেকে পরে দ্রুত প্রবেশের জন্য এই সেশন মনে রাখা যেতে পারে।
                                </span>
                            </span>
                        </label>
                    </div>

                    <div class="ui-auth-actions">
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="ui-public-inline-link">
                                {{ __('পাসওয়ার্ড ভুলে গেছেন?') }}
                            </a>
                        @endif

                        <button type="submit" class="ui-public-action ui-public-action--primary">
                            {{ __('লগইন করুন') }}
                        </button>
                    </div>
                </form>
            </article>
        </div>
    </div>
</x-guest-layout>
