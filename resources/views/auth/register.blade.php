<x-guest-layout>
    @php
        $registrationIntent = $registrationIntent ?? old('intent', 'public');

        $intentCards = [
            [
                'intent' => 'public',
                'title' => 'সাধারণ অ্যাকাউন্ট / General',
                'description' => 'কোনো donor বা guardian portal অনুমতি না খুলেই একটি shared account শুরু করুন।',
                'route' => route('register'),
            ],
            [
                'intent' => 'donor',
                'title' => 'দাতা ভিত্তি / Donor',
                'description' => 'একই অ্যাকাউন্টে donor intent রেকর্ড করুন, কিন্তু donor portal access বন্ধই থাকবে।',
                'route' => route('register.donor'),
            ],
            [
                'intent' => 'guardian',
                'title' => 'অভিভাবক ভিত্তি / Guardian',
                'description' => 'unlinked guardian foundation তৈরি করুন, কিন্তু কোনো protected guardian access খোলা হবে না।',
                'route' => route('register.guardian'),
            ],
        ];
    @endphp

    <div class="ui-auth-section">
        <div class="grid gap-3 sm:grid-cols-3">
            @foreach ($intentCards as $card)
                @php
                    $active = $registrationIntent === $card['intent'];
                @endphp

                <a href="{{ $card['route'] }}" class="ui-auth-choice {{ $active ? 'ui-auth-choice--active' : '' }}">
                    <div class="flex items-center justify-between gap-3">
                        <h3 class="ui-auth-choice__title">{{ $card['title'] }}</h3>
                        @if ($active)
                            <span class="ui-badge ui-badge--success">নির্বাচিত</span>
                        @endif
                    </div>
                    <p class="ui-auth-choice__body">{{ $card['description'] }}</p>
                </a>
            @endforeach
        </div>

        <x-ui.alert variant="info" title="Registration boundary">
            এই ধাপে shared identity এবং প্রয়োজন হলে donor বা guardian intent মাত্র সেট হয়। donor portal access, guardian linkage, invoice/payment visibility বা অন্য protected flow খোলা হয় না।
        </x-ui.alert>

        @if ($registrationIntent === 'guardian')
            <x-ui.alert variant="warning" title="Guardian Google note">
                প্রথমবার guardian foundation তৈরি করতে ইমেইল রেজিস্ট্রেশনই ব্যবহার করুন। Google linking পরে profile থেকে করা যাবে।
            </x-ui.alert>
        @else
            <article class="ui-public-card ui-public-card--soft">
                <p class="ui-public-card__eyebrow">Google ভিত্তিক রেজিস্ট্রেশন</p>
                <h3 class="ui-public-card__title">একই shared account foundation Google দিয়েও শুরু করা যাবে</h3>
                <p class="ui-public-card__body">
                    donor intent capture করা যেতে পারে, কিন্তু donor portal eligibility বা guardian protected access এখনও বন্ধই থাকবে।
                </p>
                <a href="{{ route('google.redirect', ['intent' => $registrationIntent]) }}" class="ui-public-action ui-public-action--secondary mt-6">
                    Google দিয়ে চালিয়ে যান
                </a>
            </article>
        @endif

        <article class="ui-public-card">
            <p class="ui-public-card__eyebrow">নিবন্ধন ফর্ম</p>
            <h3 class="ui-public-card__title">প্রয়োজনীয় তথ্য দিয়ে অ্যাকাউন্ট তৈরি করুন</h3>
            <p class="ui-public-card__body">
                সব field, validation rule এবং registration behavior অপরিবর্তিত রাখা হয়েছে। কেবল hierarchy, readability এবং institutional presentation উন্নত করা হয়েছে।
            </p>

            <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-5">
                @csrf

                <input type="hidden" name="intent" value="{{ old('intent', $registrationIntent) }}">

                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <x-input-label for="name" :value="__('নাম / Name')" />
                        <x-text-input id="name" class="mt-1 block w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="email" :value="__('ইমেইল / Email')" />
                        <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="phone" :value="__('ফোন (ঐচ্ছিক) / Phone')" />
                        <x-text-input id="phone" class="mt-1 block w-full" type="tel" name="phone" :value="old('phone')" autocomplete="tel" />
                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                        <p class="ui-field-help">এটি account-level optional phone. পরে আলাদা করে verify করা যাবে।</p>
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
                </div>

                <div class="ui-auth-actions">
                    <a href="{{ route('login') }}" class="ui-public-inline-link">
                        {{ __('আগেই অ্যাকাউন্ট আছে?') }}
                    </a>

                    <button type="submit" class="ui-public-action ui-public-action--primary">
                        {{ __('অ্যাকাউন্ট তৈরি করুন') }}
                    </button>
                </div>
            </form>
        </article>
    </div>
</x-guest-layout>
