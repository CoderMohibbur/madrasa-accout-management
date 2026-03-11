@php
    use App\Models\DonationIntent;
    use App\Models\Payment;

    $navLinks = [
        ['label' => 'হোম', 'url' => url('/')],
        ['label' => 'অনুদান', 'url' => route('donations.guest.entry')],
        ['label' => 'লগইন', 'url' => route('login')],
    ];

    $statusLabel = match ($payment?->status) {
        Payment::STATUS_PAID => 'Donation Verified',
        Payment::STATUS_FAILED => 'Donation Not Verified',
        Payment::STATUS_CANCELLED => 'Donation Cancelled',
        Payment::STATUS_MANUAL_REVIEW => 'Manual Review Needed',
        Payment::STATUS_PENDING_VERIFICATION => 'Pending Verification',
        Payment::STATUS_REDIRECT_PENDING => 'Redirect Pending',
        default => 'Donation Pending',
    };

    $donorModeLabel = $intent->donor_mode === DonationIntent::DONOR_MODE_GUEST ? 'Guest donation' : 'Identified donation';
    $displayModeLabel = $intent->display_mode === DonationIntent::DISPLAY_MODE_ANONYMOUS ? 'Anonymous display' : 'Named display';
    $receipt = $payment?->receipt;
@endphp

<x-public-shell
    title="অনুদান অবস্থা"
    description="এই পেজটি শুধু নির্দিষ্ট donation reference-টির সীমিত status দেখায়; donor portal history বা broader account access খোলে না।"
    :nav-links="$navLinks"
>
    <section class="space-y-6">
        <x-ui.page-header
            eyebrow="Donation status"
            title="{{ $statusLabel }}"
            description="Server-side verification-ই donation outcome নির্ধারণ করে। Browser return alone payment settle করে না।"
        >
            <x-slot:actions>
                <x-ui.badge variant="info">{{ $donorModeLabel }}</x-ui.badge>
            </x-slot:actions>
        </x-ui.page-header>

        <x-ui.alert :variant="$statusVariant" title="Current status">
            {{ $statusMessage }}
        </x-ui.alert>

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <x-ui.stat-card
                label="Amount"
                value="{{ number_format((float) $intent->amount, 2) }} {{ $intent->currency }}"
                meta="Settled donor amount remains separate from legacy accounting posting."
            />
            <x-ui.stat-card
                label="Public reference"
                value="{{ $intent->public_reference }}"
                meta="Support বা future lookup-এর জন্য এই reference সংরক্ষণ করুন।"
            />
            <x-ui.stat-card
                label="Display mode"
                value="{{ $displayModeLabel }}"
                meta="Anonymous display কেবল visibility preference."
            />
            <x-ui.stat-card
                label="Receipt"
                value="{{ $receipt?->receipt_number ?: 'Not issued yet' }}"
                meta="{{ $receipt ? 'Issued after verified settlement only.' : 'Receipts are created only after verified settlement.' }}"
            />
        </div>

        <div class="grid gap-6 lg:grid-cols-[1.1fr,0.9fr]">
            <x-ui.card
                title="Donation outcome"
                description="বর্তমান attempt, receipt state এবং donor-safe next steps এখানে দেখানো হচ্ছে।"
            >
                <dl class="grid gap-4 text-sm text-slate-600 sm:grid-cols-2">
                    <div>
                        <dt class="ui-stat-label">Payment status</dt>
                        <dd class="mt-2 font-semibold text-slate-950">{{ strtoupper($payment?->status ?: 'pending') }}</dd>
                    </div>
                    <div>
                        <dt class="ui-stat-label">Verification status</dt>
                        <dd class="mt-2 font-semibold text-slate-950">{{ strtoupper($payment?->verification_status ?: 'pending') }}</dd>
                    </div>
                    <div>
                        <dt class="ui-stat-label">Provider</dt>
                        <dd class="mt-2">{{ strtoupper($payment?->provider ?: 'shurjopay') }}</dd>
                    </div>
                    <div>
                        <dt class="ui-stat-label">Provider reference</dt>
                        <dd class="mt-2 break-all">{{ $payment?->provider_reference ?: $payment?->idempotency_key ?: 'Unavailable' }}</dd>
                    </div>
                    <div>
                        <dt class="ui-stat-label">Donated at</dt>
                        <dd class="mt-2">
                            {{ optional($donationRecord?->donated_at ?? $payment?->paid_at ?? $payment?->initiated_at)->format('Y-m-d H:i') ?: 'Not settled yet' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="ui-stat-label">Posting status</dt>
                        <dd class="mt-2">{{ strtoupper($donationRecord?->posting_status ?: 'not_started') }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="ui-stat-label">Status reason</dt>
                        <dd class="mt-2 leading-7">{{ $payment?->status_reason ?: 'No extra detail was recorded for this donation yet.' }}</dd>
                    </div>
                </dl>

                @if ($receipt)
                    <div class="ui-auth-note mt-6">
                        Receipt <strong>{{ $receipt->receipt_number }}</strong> was issued on
                        <strong>{{ optional($receipt->issued_at)->format('Y-m-d H:i') }}</strong>.
                    </div>
                @endif

                @if ($intent->donor_mode === DonationIntent::DONOR_MODE_GUEST)
                    <div class="ui-auth-note mt-6">
                        Guest donation স্বয়ংক্রিয়ভাবে account বা donor portal access তৈরি করে না।
                    </div>
                @else
                    <div class="ui-auth-note mt-6">
                        এই donation transaction-specific ownership-এর জন্য বর্তমান account-এর সাথে সীমিতভাবে যুক্ত। broader donor portal history পরে আসে।
                    </div>
                @endif
            </x-ui.card>

            <div class="space-y-6">
                <x-ui.card
                    title="Access key"
                    description="Guest এবং narrow transaction-specific access এই public reference ও opaque key-এর ওপর নির্ভর করে।"
                    soft
                >
                    @if ($accessKeyForDisplay)
                        <p class="text-sm leading-7 text-slate-600">
                            এই key-টি public reference-এর সাথে সংরক্ষণ করুন। এটি শুধুমাত্র এই donation-এর সীমিত access material।
                        </p>
                        <div class="mt-4 rounded-[1.5rem] border border-slate-200 bg-white px-4 py-3 font-mono text-sm text-slate-900">
                            {{ $accessKeyForDisplay }}
                        </div>
                        <p class="mt-4 text-xs uppercase tracking-[0.22em] text-slate-500">
                            Direct status link
                        </p>
                        <div class="mt-2 break-all text-sm text-slate-700">
                            {{ $statusLink }}
                        </div>
                    @else
                        <p class="text-sm leading-7 text-slate-600">
                            এই session-এ key সংরক্ষিত নেই। সংরক্ষিত access key ব্যবহার করুন, অথবা প্রয়োজন হলে sign in করুন।
                        </p>
                    @endif
                </x-ui.card>

                <x-ui.card
                    title="Next safe actions"
                    description="এই donation reference-টির সীমার মধ্যেই পরবর্তী পদক্ষেপগুলো রাখুন।"
                    soft
                >
                    <ul class="space-y-3 text-sm leading-7 text-slate-600">
                        <li>public reference ও access key একসাথে সংরক্ষণ করুন।</li>
                        <li><a href="{{ route('donations.guest.entry') }}" class="font-semibold text-emerald-700 hover:text-emerald-600">Donation entry page</a> থেকে নতুন donation শুরু করুন।</li>
                        <li>broader donor portal history, account linking ও claim flow এখনও deferred।</li>
                    </ul>
                </x-ui.card>
            </div>
        </div>
    </section>
</x-public-shell>
