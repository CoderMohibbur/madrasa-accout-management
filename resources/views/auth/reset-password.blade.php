<x-guest-layout>
    <article class="ui-public-card">
        <p class="ui-public-card__eyebrow">নতুন পাসওয়ার্ড</p>
        <h3 class="ui-public-card__title">নিরাপদভাবে নতুন পাসওয়ার্ড সেট করুন</h3>
        <p class="ui-public-card__body">
            নিচের তথ্যগুলো পূরণ করলে আপনার shared account-এর জন্য নতুন পাসওয়ার্ড সংরক্ষিত হবে। route, token usage এবং reset behavior অপরিবর্তিত রাখা হয়েছে।
        </p>

        <form method="POST" action="{{ route('password.store') }}" class="mt-6 space-y-5">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div>
                <x-input-label for="email" :value="__('ইমেইল / Email')" />
                <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password" :value="__('পাসওয়ার্ড / Password')" />
                <x-text-input id="password" class="mt-1 block w-full" type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password_confirmation" :value="__('পাসওয়ার্ড নিশ্চিতকরণ / Confirm Password')" />
                <x-text-input id="password_confirmation" class="mt-1 block w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex justify-end">
                <button type="submit" class="ui-public-action ui-public-action--primary">
                    {{ __('পাসওয়ার্ড রিসেট করুন') }}
                </button>
            </div>
        </form>
    </article>
</x-guest-layout>
