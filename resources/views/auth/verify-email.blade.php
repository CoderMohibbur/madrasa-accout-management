<x-guest-layout>
    <div class="ui-auth-section">
        <article class="ui-public-card ui-public-card--soft">
            <p class="ui-public-card__eyebrow">ইমেইল নিশ্চিতকরণ</p>
            <h3 class="ui-public-card__title">আপনার ইমেইল ঠিকানাটি যাচাই করুন</h3>
            <p class="ui-public-card__body">
                বর্তমান verified boundary বজায় রাখতে <span class="font-semibold text-slate-950">{{ $user->email }}</span>
                ঠিকানায় পাঠানো লিংকটি ব্যবহার করুন। এতে approval state, donor access বা guardian linkage পরিবর্তন হয় না।
            </p>
        </article>

        @if (session('email_verification_message'))
            <x-ui.alert variant="success" title="Email verification">
                {{ session('email_verification_message') }}
            </x-ui.alert>
        @endif

        @if (session('email_verification_warning'))
            <x-ui.alert variant="warning" title="Email delivery pending">
                {{ session('email_verification_warning') }}
            </x-ui.alert>
        @endif

        @if ($errors->has('email_verification'))
            <x-ui.alert variant="warning" title="Resend unavailable">
                {{ $errors->first('email_verification') }}
            </x-ui.alert>
        @endif

        <article class="ui-public-card">
            <p class="ui-public-card__eyebrow">পরবর্তী ধাপ</p>
            <h3 class="ui-public-card__title">ইমেইল লিংক না পেলে আবার পাঠান</h3>
            <div class="mt-4 space-y-4 text-sm leading-7 text-slate-600">
                <p>ইনবক্স বা স্প্যাম ফোল্ডার দেখুন। প্রয়োজনে নিচের বোতাম থেকে নতুন verification email অনুরোধ করুন।</p>

                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="ui-public-action ui-public-action--primary">আবার verification email পাঠান</button>
                </form>

                <div class="flex flex-wrap gap-3">
                    @if ($user->hasRole(\App\Models\User::ROLE_REGISTERED_USER))
                        <a href="{{ route('registration.onboarding') }}" class="ui-public-action ui-public-action--secondary">অনবোর্ডিং-এ ফিরুন</a>
                    @else
                        <a href="{{ route('profile.edit') }}" class="ui-public-action ui-public-action--secondary">প্রোফাইল সেটিংস</a>
                    @endif
                </div>
            </div>
        </article>

        <form method="POST" action="{{ route('logout') }}" class="flex justify-end">
            @csrf
            <button type="submit" class="ui-public-action ui-public-action--ghost">সাইন আউট</button>
        </form>
    </div>
</x-guest-layout>
