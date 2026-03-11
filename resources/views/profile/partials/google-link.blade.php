@php
    $googleIdentity = $user->googleIdentity()->first();
@endphp

<section>
    <header>
        <h2 class="text-lg font-semibold text-slate-950">
            Google Sign-In
        </h2>

        <p class="mt-1 text-sm leading-6 text-slate-600">
            Link Google only to this existing shared account. Prompt-38 keeps broad merge logic, donor portal expansion,
            and guardian protected auto-linking outside scope.
        </p>
    </header>

    @if (session('google_link_message'))
        <div class="mt-4">
            <x-ui.alert variant="success" title="Google link updated">
                {{ session('google_link_message') }}
            </x-ui.alert>
        </div>
    @endif

    @if (session('google_link_warning'))
        <div class="mt-4">
            <x-ui.alert variant="warning" title="Google link notice">
                {{ session('google_link_warning') }}
            </x-ui.alert>
        </div>
    @endif

    <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50/80 p-5">
        @if ($googleIdentity)
            <dl class="space-y-3 text-sm text-slate-700">
                <div>
                    <dt class="font-medium text-slate-900">Linked Google email snapshot</dt>
                    <dd class="mt-1">{{ $googleIdentity->provider_email ?: 'No provider email snapshot was returned.' }}</dd>
                </div>

                <div>
                    <dt class="font-medium text-slate-900">Email verification from Google</dt>
                    <dd class="mt-1">{{ $googleIdentity->provider_email_verified ? 'Verified at provider' : 'Not verified at provider' }}</dd>
                </div>

                <div>
                    <dt class="font-medium text-slate-900">Last Google sign-in use</dt>
                    <dd class="mt-1">{{ $googleIdentity->last_used_at?->diffForHumans() ?: 'Linked but not used yet' }}</dd>
                </div>
            </dl>

            <p class="mt-4 text-sm leading-6 text-slate-600">
                This rollout keeps Google unlink and identity reassignment off. If this link must change, use a later
                recovery or support path instead of widening scope here.
            </p>
        @else
            <p class="text-sm leading-6 text-slate-600">
                No Google identity is linked yet. Linking here keeps the account the same and does not create donor
                portal access, guardian linkage, or protected guardian access by itself.
            </p>

            <form method="post" action="{{ route('google.link') }}" class="mt-4">
                @csrf

                <button type="submit" class="ui-button ui-button--secondary">
                    Link Google to this account
                </button>
            </form>
        @endif
    </div>
</section>
