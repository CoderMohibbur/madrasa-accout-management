@php
    $status = match ($access->reason) {
        'profile_pending' => [
            'title' => 'Guardian informational access is live, while protected linkage stays separate',
            'description' => 'This guardian profile can use the informational portal now, but protected student, invoice, receipt, and payment access still depends on later enablement and linkage work.',
            'items' => [
                'Guardian login and guardian informational access stay separate from universal email or phone verification requirements.',
                'Protected guardian routes remain distinct from this light informational surface.',
                'Keep using profile and contact tools while any later linkage or eligibility review is still pending.',
            ],
        ],
        'protected_eligible' => [
            'title' => 'Guardian informational guidance stays available alongside protected access',
            'description' => 'This account can use the informational portal for institution and admission guidance without mixing in protected student, invoice, receipt, or payment data.',
            'items' => [
                'Protected student and invoice routes remain separate from this informational portal.',
                'Admission guidance here stays informational only and never becomes an internal application workflow.',
                'Use the protected guardian routes separately when linked student or invoice actions are needed.',
            ],
        ],
        'email_unverified' => [
            'title' => 'Guardian informational access stays available while protected routes wait on email verification',
            'description' => 'This shared account already has guardian linkage, but the protected guardian portal remains locked until the email-verification boundary is satisfied.',
            'items' => [
                'Protected guardian routing now checks verified contact trust separately from the informational portal.',
                'Student, invoice, receipt, and payment controls stay fail-closed until email verification is complete.',
                'Institution guidance and admission help remain available here without exposing protected data.',
            ],
        ],
        'unlinked' => [
            'title' => 'Guardian informational access stays available while protected linkage is still missing',
            'description' => 'This guardian profile can sign in and use informational guidance, but the protected guardian portal remains locked until at least one authorized student linkage exists.',
            'items' => [
                'Portal enablement alone does not unlock protected guardian routes.',
                'Protected student, invoice, receipt, and payment access still requires explicit guardian linkage.',
                'Use institution help channels if this guardian linkage should already be present.',
            ],
        ],
        'profile_inactive', 'profile_deleted' => [
            'title' => 'Guardian informational access is currently limited',
            'description' => 'A guardian profile exists on this shared account, but its current state does not allow protected guardian use. The informational portal stays limited to safe guidance only.',
            'items' => [
                'Student, invoice, receipt, and payment-sensitive areas remain blocked.',
                'Profile and verification tools stay separate from protected guardian eligibility.',
                'Use institution help channels if this guardian profile needs review.',
            ],
        ],
        'role_only' => [
            'title' => 'Guardian role detected without a linked informational profile',
            'description' => 'This shared account has guardian-domain potential, but no linked guardian profile is available yet. The informational portal stays limited to non-sensitive guidance.',
            'items' => [
                'Guardian role membership alone does not grant protected student or invoice access.',
                'Admission guidance remains external-only from this page.',
                'Protected guardian routes stay fail-closed until the profile and linkage conditions are satisfied.',
            ],
        ],
        default => [
            'title' => 'Guardian informational access is ready on this shared account',
            'description' => 'This portal gives guardian-intent and unlinked guardian accounts a safe place for institution guidance, admission information, and next steps without exposing protected data.',
            'items' => [
                'Protected student, invoice, receipt, and payment details stay on separate guardian routes.',
                'Admission information here is curated guidance only and remains an external handoff.',
                'Verification and linkage stay separate from informational access and protected access.',
            ],
        ],
    };
@endphp

<x-guardian-informational-layout
    title="Guardian informational access"
    description="Use this non-sensitive guardian space for institution guidance, admission information, and safe next steps."
>
    <div class="grid gap-6 xl:grid-cols-[1.1fr,0.9fr]">
        <x-ui.card :title="$status['title']" :description="$status['description']">
            <x-slot name="headerActions">
                <x-ui.badge variant="info">Guardian informational portal</x-ui.badge>
            </x-slot>

            <div class="space-y-3">
                @foreach ($status['items'] as $item)
                    <div class="rounded-[1.5rem] border border-slate-200 bg-stone-50 px-4 py-3 text-sm leading-6 text-slate-700">
                        {{ $item }}
                    </div>
                @endforeach
            </div>

            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('guardian.info.institution') }}" class="ui-button ui-button--primary">Institution information</a>
                <a href="{{ route('guardian.info.admission') }}" class="ui-button ui-button--secondary">Admission guidance</a>
                <a href="{{ route('profile.edit') }}" class="ui-button ui-button--ghost">Profile settings</a>
            </div>
        </x-ui.card>

        <x-ui.card
            title="Current guardian state"
            description="This summary stays informational-only and never reveals protected student, invoice, receipt, or payment records."
        >
            <dl class="grid gap-4 sm:grid-cols-2">
                <div class="rounded-[1.5rem] border border-slate-200 bg-stone-50 px-4 py-3">
                    <dt class="ui-stat-label">Guardian profile</dt>
                    <dd class="mt-2 text-sm font-semibold text-slate-900">{{ $guardian?->name ?: Auth::user()->name }}</dd>
                </div>
                <div class="rounded-[1.5rem] border border-slate-200 bg-stone-50 px-4 py-3">
                    <dt class="ui-stat-label">Email verification</dt>
                    <dd class="mt-2 text-sm font-semibold text-slate-900">{{ Auth::user()->hasVerifiedEmail() ? 'Verified' : 'Pending' }}</dd>
                </div>
                <div class="rounded-[1.5rem] border border-slate-200 bg-stone-50 px-4 py-3">
                    <dt class="ui-stat-label">Protected guardian access</dt>
                    <dd class="mt-2 text-sm font-semibold text-slate-900">{{ $access->protectedEligible ? 'Eligible on separate protected routes' : 'Not enabled here' }}</dd>
                </div>
                <div class="rounded-[1.5rem] border border-slate-200 bg-stone-50 px-4 py-3">
                    <dt class="ui-stat-label">Linkage boundary</dt>
                    <dd class="mt-2 text-sm leading-6 text-slate-700">This informational portal never shows linked students, invoices, receipts, or payment-entry controls.</dd>
                </div>
            </dl>
        </x-ui.card>
    </div>

    <div class="mt-8 grid gap-5 lg:grid-cols-3">
        @foreach ($institutionHighlights as $highlight)
            <x-ui.card :title="$highlight['title']" soft>
                <p class="text-sm leading-7 text-slate-600">{{ $highlight['description'] }}</p>
            </x-ui.card>
        @endforeach
    </div>

    <div class="mt-8">
        <x-ui.alert variant="info" title="Protected boundary preserved">
            Protected student, invoice, receipt, and payment details stay on separate guardian routes.
        </x-ui.alert>
    </div>

    <div class="mt-8 grid gap-5 lg:grid-cols-3">
        @foreach ($supportChannels as $channel)
            <x-ui.card :title="$channel['label']" :description="$channel['value']">
                <p class="text-sm leading-7 text-slate-600">{{ $channel['description'] }}</p>
            </x-ui.card>
        @endforeach
    </div>
</x-guardian-informational-layout>
