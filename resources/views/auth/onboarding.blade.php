<x-guest-layout>
    @php
        $state = match ($intent) {
            'donor' => [
                'title' => 'দাতা ভিত্তি প্রস্তুত হয়েছে',
                'description' => 'আপনার shared account এখন সক্রিয়, এবং একটি non-portal donor foundation তৈরি হয়েছে যাতে ভবিষ্যৎ donor onboarding একই identity-তে এগোতে পারে।',
                'items' => [
                    'এই একই অ্যাকাউন্ট ভবিষ্যতে donor journey-তে ব্যবহার করা যাবে।',
                    'donor portal access, donation history এবং receipt history এখনও deferred।',
                    'শুধু registration দিয়ে donor role বা portal entitlement দেওয়া হয়নি।',
                ],
            ],
            'guardian' => [
                'title' => 'অভিভাবক ভিত্তি প্রস্তুত হয়েছে',
                'description' => 'আপনার shared account সক্রিয় হয়েছে এবং একটি unlinked guardian foundation তৈরি হয়েছে, কিন্তু কোনো protected student বা invoice/payment data খোলা হয়নি।',
                'items' => [
                    'guardian protected access-এর জন্য পরে linkage ও authorization প্রয়োজন হবে।',
                    'informational guardian journey একই identity-তে ভবিষ্যতে এগোতে পারবে।',
                    'registration একাই কোনো student link বা sensitive guardian data প্রকাশ করেনি।',
                ],
            ],
            default => [
                'title' => 'সাধারণ অ্যাকাউন্ট প্রস্তুত হয়েছে',
                'description' => 'আপনার shared account এখন প্রস্তুত। donor বা guardian eligibility স্বয়ংক্রিয়ভাবে ধরে নেওয়া হয়নি; এটি একটি safe neutral landing state।',
                'items' => [
                    'একই account ভবিষ্যৎ donor বা guardian expansion-এর জন্যও ব্যবহার করা যাবে।',
                    'কোনো donor profile, guardian profile বা protected portal access স্বয়ংক্রিয়ভাবে খোলা হয়নি।',
                    'open registration-এর জন্য এটি নিরপেক্ষ onboarding landing point হিসেবে কাজ করছে।',
                ],
            ],
        };
    @endphp

    <div class="ui-auth-section">
        <x-auth-session-status :status="session('status')" />

        <article class="ui-public-card ui-public-card--soft">
            <p class="ui-public-card__eyebrow">অনবোর্ডিং অবস্থা</p>
            <h3 class="ui-public-card__title">{{ $state['title'] }}</h3>
            <p class="ui-public-card__body">{{ $state['description'] }}</p>
        </article>

        <article class="ui-public-card">
            <p class="ui-public-card__eyebrow">এখন কী হবে</p>
            <h3 class="ui-public-card__title">পরবর্তী নিরাপদ ধাপগুলো</h3>
            <ul class="ui-auth-list mt-6">
                @foreach ($state['items'] as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ul>
        </article>

        @include('auth.partials.contact-verification-panel', ['user' => $user, 'showProfileLink' => true])

        <div class="grid gap-5 sm:grid-cols-2">
            <article class="ui-public-card">
                <p class="ui-public-card__eyebrow">অ্যাকাউন্ট টুলস</p>
                <h3 class="ui-public-card__title">প্রোফাইল ও যোগাযোগ সেটিংস</h3>
                <p class="ui-public-card__body">
                    প্রোফাইল তথ্য, optional phone channel এবং ভবিষ্যৎ donor/guardian expansion-এর ভিত্তি এখান থেকে হালনাগাদ করা যাবে।
                </p>
                <a href="{{ route('profile.edit') }}" class="ui-public-action ui-public-action--secondary mt-6">প্রোফাইল সেটিংস খুলুন</a>
            </article>

            <article class="ui-public-card ui-public-card--soft">
                <p class="ui-public-card__eyebrow">পাবলিক হোম</p>
                <h3 class="ui-public-card__title">জনসাধারণের সাইটে ফিরে যান</h3>
                <p class="ui-public-card__body">
                    আপনার account foundation প্রস্তুত আছে। এখন চাইলে homepage, donation entry বা admission guidance-এ ফিরে যেতে পারেন।
                </p>
                <a href="{{ url('/') }}" class="ui-public-action ui-public-action--primary mt-6">পাবলিক হোমে ফিরুন</a>
            </article>
        </div>

        <form method="POST" action="{{ route('logout') }}" class="flex justify-end">
            @csrf
            <button type="submit" class="ui-public-action ui-public-action--ghost">সাইন আউট</button>
        </form>
    </div>
</x-guest-layout>
