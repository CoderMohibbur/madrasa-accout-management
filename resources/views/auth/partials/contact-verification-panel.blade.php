@php
    $user = $user ?? auth()->user();
    $showProfileLink = $showProfileLink ?? ! request()->routeIs('profile.edit');
    $emailNeedsVerification = $user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail();
@endphp

<div class="space-y-4">
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

    @if (session('phone_verification_message'))
        <x-ui.alert variant="success" title="Phone verification">
            <div class="space-y-2">
                <p>{{ session('phone_verification_message') }}</p>
                @if (app()->environment(['local', 'testing']) && session('phone_verification_debug_code'))
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">
                        Development placeholder code: {{ session('phone_verification_debug_code') }}
                    </p>
                @endif
            </div>
        </x-ui.alert>
    @endif

    @if (session('contact_verification_message'))
        <x-ui.alert variant="info" title="Contact state updated">
            {{ session('contact_verification_message') }}
        </x-ui.alert>
    @endif

    <div class="grid gap-4 lg:grid-cols-2">
        <x-ui.card title="ইমেইল চ্যানেল / Email channel">
            <div class="space-y-4 text-sm leading-6 text-slate-600">
                <p>
                    <span class="ui-badge {{ $emailNeedsVerification ? 'ui-badge--warning' : 'ui-badge--success' }}">
                        {{ $emailNeedsVerification ? 'যাচাই প্রয়োজন' : 'যাচাইকৃত' }}
                    </span>
                </p>

                <p>
                    {{ $emailNeedsVerification
                        ? 'বর্তমান verified boundary এখনও আপনার email trust state ব্যবহার করে। প্রয়োজন হলে এখান থেকেই নতুন verification email পাঠানো যাবে।'
                        : 'আপনার email channel যাচাইকৃত। তবে এটি donor portal access, guardian linkage বা অন্য কোনো protected role স্বয়ংক্রিয়ভাবে দেয় না।' }}
                </p>

                @if ($emailNeedsVerification)
                    <form method="POST" action="{{ route('verification.send') }}" class="space-y-3">
                        @csrf

                        @if ($errors->has('email_verification'))
                            <x-ui.alert variant="warning" title="Email resend unavailable">
                                {{ $errors->first('email_verification') }}
                            </x-ui.alert>
                        @endif

                        <button type="submit" class="ui-button ui-button--secondary">Verification email আবার পাঠান</button>
                    </form>
                @endif
            </div>
        </x-ui.card>

        <x-ui.card title="ফোন চ্যানেল / Phone channel">
            <div class="space-y-4 text-sm leading-6 text-slate-600">
                @if ($user->normalizedPhone())
                    <p>
                        <span class="ui-badge {{ $user->hasVerifiedPhone() ? 'ui-badge--success' : 'ui-badge--warning' }}">
                            {{ $user->hasVerifiedPhone() ? 'যাচাইকৃত' : 'ঐচ্ছিক ও অপেক্ষমাণ' }}
                        </span>
                    </p>

                    <p>
                        অ্যাকাউন্ট ফোন: <span class="font-semibold text-slate-950">{{ $user->maskedPhone() }}</span>
                    </p>

                    <p>
                        Phone verification এই পর্যায়ে ঐচ্ছিক। এটি শুধু যোগাযোগের বিশ্বাসযোগ্যতা বাড়ায়; donor বা guardian protected access খোলে না।
                    </p>

                    @if (! $user->hasVerifiedPhone())
                        @if ($errors->has('phone_verification'))
                            <x-ui.alert variant="warning" title="Phone verification unavailable">
                                {{ $errors->first('phone_verification') }}
                            </x-ui.alert>
                        @endif

                        <form method="POST" action="{{ route('verification.phone.send') }}" class="space-y-3">
                            @csrf
                            <button type="submit" class="ui-button ui-button--secondary">ফোন কোড পাঠান</button>
                            <p class="text-xs uppercase tracking-[0.24em] text-slate-500">60 second cooldown, 5 sends per hour, 10 minute code expiry</p>
                        </form>

                        <form method="POST" action="{{ route('verification.phone.verify') }}" class="space-y-3">
                            @csrf

                            <div>
                                <x-input-label for="phone_verification_code" :value="__('যাচাইকরণ কোড / Verification code')" />
                                <x-text-input
                                    id="phone_verification_code"
                                    name="code"
                                    type="text"
                                    inputmode="numeric"
                                    maxlength="6"
                                    class="mt-1 block w-full"
                                    :value="old('code')"
                                />
                                <x-input-error class="mt-2" :messages="$errors->get('phone_verification_code')" />
                            </div>

                            <button type="submit" class="ui-button ui-button--primary">ফোন যাচাই করুন</button>
                        </form>
                    @endif
                @else
                    <p>এখনও কোনো account phone number সংরক্ষিত নেই। Phone verification এই পর্যায়ে ঐচ্ছিক।</p>

                    @if ($showProfileLink)
                        <a href="{{ route('profile.edit') }}" class="ui-button ui-button--secondary">প্রোফাইল থেকে ফোন যোগ করুন</a>
                    @endif
                @endif
            </div>
        </x-ui.card>
    </div>
</div>
