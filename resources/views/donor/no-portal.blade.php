@php
    if ($access->reason === 'profile_pending') {
        $state = [
            'title' => 'Donor access is ready, but portal history is still off',
            'description' => 'Your shared account can sign in and use donor checkout safely, but this donor profile has not been enabled for the full donor portal yet.',
            'items' => [
                'Registration, login, and identified donation stay separate from donor portal eligibility.',
                'Email and phone verification are still independent trust signals; completing them does not unlock portal history by itself.',
                'Use the secure checkout and transaction-specific status links for current donations until donor portal access is enabled.',
            ],
        ];
    } elseif (in_array($access->reason, ['profile_inactive', 'profile_deleted'], true)) {
        $state = [
            'title' => 'This donor profile is not portal-eligible right now',
            'description' => 'Your account remains separate from donor portal eligibility. A donor profile exists, but its current state does not allow the read-only donor portal.',
            'items' => [
                'Donation capability and donor portal history remain separate permissions.',
                'Transaction-specific status and receipt access stay narrower than full donor history.',
                'Profile and verification updates remain available without widening donor access automatically.',
            ],
        ];
    } elseif ($access->reason === 'identified_only') {
        $state = [
            'title' => 'Your account-linked donations stay narrow by design',
            'description' => 'This account has identified donor activity, but payment completion does not auto-grant donor portal access or donation-history browsing.',
            'items' => [
                'Continue to use the same account for identified checkout on /donate.',
                'Keep using transaction-specific checkout and status links for settled donations.',
                'A later explicit donor-profile enablement step is still required before full donor history appears here.',
            ],
        ];
    } else {
        $state = [
            'title' => 'Donor access remains limited to safe next steps',
            'description' => 'This account can sign in, but donor portal history still depends on an explicit donor-domain eligibility state.',
            'items' => [
                'Donor login does not depend on universal verification.',
                'Donor payment ability remains broader than donor portal access.',
                'The donor portal stays read-only and fail-closed until portal eligibility exists.',
            ],
        ];
    }

    $navItems = [
        ['label' => 'Overview', 'route' => 'donor.dashboard', 'pattern' => 'donor.dashboard'],
    ];
@endphp

<x-donor-layout
    title="Donor Access"
    description="Your account can stay account-linked for donor activity without assuming donor portal eligibility."
    :nav-items="$navItems"
>
    <div class="grid gap-6 xl:grid-cols-[1.15fr,0.85fr]">
        <x-ui.card :title="$state['title']" :description="$state['description']">
            <x-slot name="headerActions">
                <x-ui.badge variant="warning">Donor no-portal state</x-ui.badge>
            </x-slot>

            <div class="space-y-3">
                @foreach ($state['items'] as $item)
                    <div class="rounded-[1.5rem] border border-slate-200 bg-stone-50 px-4 py-3 text-sm leading-6 text-slate-700">
                        {{ $item }}
                    </div>
                @endforeach
            </div>
        </x-ui.card>

        <x-ui.card
            title="Current donor context"
            description="These account details stay separate from donor portal enablement until an explicit donor-domain decision is made."
            soft
        >
            <dl class="grid gap-4">
                <div class="rounded-[1.5rem] border border-slate-200 bg-white px-4 py-3">
                    <dt class="ui-stat-label">Portal status</dt>
                    <dd class="mt-2 text-sm font-semibold text-slate-900">Not enabled</dd>
                </div>
                <div class="rounded-[1.5rem] border border-slate-200 bg-white px-4 py-3">
                    <dt class="ui-stat-label">Donor profile</dt>
                    <dd class="mt-2 text-sm text-slate-700">{{ $donor?->name ?: 'No active donor portal profile is linked yet.' }}</dd>
                </div>
                <div class="rounded-[1.5rem] border border-slate-200 bg-white px-4 py-3">
                    <dt class="ui-stat-label">Contact email</dt>
                    <dd class="mt-2 text-sm text-slate-700">{{ $donor?->email ?: Auth::user()->email }}</dd>
                </div>
                <div class="rounded-[1.5rem] border border-slate-200 bg-white px-4 py-3">
                    <dt class="ui-stat-label">Next safe actions</dt>
                    <dd class="mt-2 text-sm leading-6 text-slate-700">Keep your base profile current, continue using secure identified checkout, and rely on transaction-specific status or receipt access until donor portal enablement is granted explicitly.</dd>
                </div>
            </dl>
        </x-ui.card>
    </div>

    <div class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <x-ui.card title="Donate safely">
            <div class="space-y-3 text-sm leading-6 text-slate-600">
                <p>Account-linked checkout still works without widening donor portal access.</p>
                <a href="{{ route('donations.guest.entry') }}" class="ui-button ui-button--primary">Open donor checkout</a>
            </div>
        </x-ui.card>

        <x-ui.card title="Profile settings">
            <div class="space-y-3 text-sm leading-6 text-slate-600">
                <p>Profile, password, and verification tools stay available on the shared account.</p>
                <a href="{{ route('profile.edit') }}" class="ui-button ui-button--secondary">Open profile</a>
            </div>
        </x-ui.card>

        <x-ui.card title="Verification boundary">
            <div class="space-y-3 text-sm leading-6 text-slate-600">
                <p>Email and phone verification can improve contact trust, but neither one silently grants donor portal eligibility.</p>
                <a href="{{ route('verification.notice') }}" class="ui-button ui-button--ghost">Review verification</a>
            </div>
        </x-ui.card>
    </div>
</x-donor-layout>
