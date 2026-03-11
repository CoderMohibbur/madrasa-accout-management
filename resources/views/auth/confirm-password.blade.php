<x-guest-layout>
    <div class="ui-auth-section">
        <article class="ui-public-card ui-public-card--soft">
            <p class="ui-public-card__eyebrow">নিরাপত্তা যাচাই</p>
            <h3 class="ui-public-card__title">এটি একটি নিরাপদ ধাপ</h3>
            <p class="ui-public-card__body">
                পরবর্তী protected action-এ যাওয়ার আগে আপনার পাসওয়ার্ড আবার নিশ্চিত করতে হবে। এই behavior অপরিবর্তিত, কেবল উপস্থাপনাটি এখন homepage family-এর সাথে সামঞ্জস্যপূর্ণ।
            </p>
        </article>

        <article class="ui-public-card">
            <p class="ui-public-card__eyebrow">Password confirmation</p>
            <h3 class="ui-public-card__title">পাসওয়ার্ড লিখে নিশ্চিত করুন</h3>

            <form method="POST" action="{{ route('password.confirm') }}" class="mt-6 space-y-5">
                @csrf

                <div>
                    <x-input-label for="password" :value="__('পাসওয়ার্ড / Password')" />
                    <x-text-input id="password" class="mt-1 block w-full" type="password" name="password" required autocomplete="current-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="ui-public-action ui-public-action--primary">
                        {{ __('নিশ্চিত করুন') }}
                    </button>
                </div>
            </form>
        </article>
    </div>
</x-guest-layout>
