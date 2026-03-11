<x-guest-layout>
    <div class="ui-auth-section">
        <article class="ui-public-card ui-public-card--soft">
            <p class="ui-public-card__eyebrow">পাসওয়ার্ড সহায়তা</p>
            <h3 class="ui-public-card__title">ইমেইল ঠিকানা দিন, আমরা রিসেট লিংক পাঠাব</h3>
            <p class="ui-public-card__body">
                আপনার অ্যাকাউন্টের সাথে যুক্ত ইমেইল ঠিকানাটি ব্যবহার করুন। reset link পাঠানোর flow অপরিবর্তিত; এখানে শুধু নির্দেশনাকে আরও পরিষ্কার করা হয়েছে।
            </p>
        </article>

        <x-auth-session-status :status="session('status')" />

        <article class="ui-public-card">
            <p class="ui-public-card__eyebrow">Reset link request</p>
            <h3 class="ui-public-card__title">পাসওয়ার্ড রিসেট লিংক পাঠান</h3>

            <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-5">
                @csrf

                <div>
                    <x-input-label for="email" :value="__('ইমেইল / Email')" />
                    <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required autofocus />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="ui-public-action ui-public-action--primary">
                        {{ __('রিসেট লিংক পাঠান') }}
                    </button>
                </div>
            </form>
        </article>
    </div>
</x-guest-layout>
