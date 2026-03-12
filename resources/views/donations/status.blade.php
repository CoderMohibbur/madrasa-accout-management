@php
    use App\Models\DonationIntent;
    use App\Models\Payment;

    $navLinks = [
        ['label' => 'হোম', 'url' => url('/')],
        ['label' => 'অনুদান', 'url' => route('donations.guest.entry')],
        ['label' => 'লগইন', 'url' => route('login')],
    ];

    $bn = static fn ($value) => strtr((string) $value, [
        '0' => '০',
        '1' => '১',
        '2' => '২',
        '3' => '৩',
        '4' => '৪',
        '5' => '৫',
        '6' => '৬',
        '7' => '৭',
        '8' => '৮',
        '9' => '৯',
    ]);

    $status = $payment?->status ?? 'pending';
    $receipt = $payment?->receipt;
    $categoryLabel = data_get($intent->metadata, 'category.label') ?: 'সাধারণ অনুদান';

    $statusLabel = match ($status) {
        Payment::STATUS_PAID => 'অনুদান ভেরিফাইড',
        Payment::STATUS_FAILED => 'অনুদান নিশ্চিত হয়নি',
        Payment::STATUS_CANCELLED => 'অনুদান বাতিল হয়েছে',
        Payment::STATUS_MANUAL_REVIEW => 'ম্যানুয়াল রিভিউ প্রয়োজন',
        Payment::STATUS_PENDING_VERIFICATION => 'ভেরিফিকেশন অপেক্ষমাণ',
        Payment::STATUS_REDIRECT_PENDING => 'চেকআউট অগ্রগতিতে আছে',
        default => 'অনুদান প্রক্রিয়াধীন',
    };

    $statusKicker = match ($status) {
        Payment::STATUS_PAID => 'নিরাপদ যাচাই সম্পন্ন',
        Payment::STATUS_FAILED => 'পেমেন্ট নিশ্চিত হয়নি',
        Payment::STATUS_CANCELLED => 'ব্যবহারকারী অগ্রগতি থামিয়েছেন',
        Payment::STATUS_MANUAL_REVIEW => 'অতিরিক্ত যাচাই প্রয়োজন',
        Payment::STATUS_PENDING_VERIFICATION, Payment::STATUS_REDIRECT_PENDING => 'সার্ভার-সাইড যাচাই চলমান',
        default => 'বর্তমান donation অবস্থা',
    };

    $statusDescription = match ($status) {
        Payment::STATUS_PAID => 'আপনার অনুদান সার্ভার-সাইড যাচাই হয়ে সফলভাবে নিশ্চিত হয়েছে। এই পেজে সেই নির্দিষ্ট donation reference-এর নিরাপদ অবস্থা দেখানো হচ্ছে।',
        Payment::STATUS_FAILED => 'এই attempt সফলভাবে ভেরিফাই হয়নি। প্রয়োজনে নতুন করে অনুদান শুরু করতে পারেন বা reference ধরে support-এর সাথে যোগাযোগ করতে পারেন।',
        Payment::STATUS_CANCELLED => 'চেকআউট প্রক্রিয়া মাঝপথে থেমে গেছে। পুনরায় অনুদান শুরু করতে চাইলে নিচের নিরাপদ action থেকে এগোতে পারেন।',
        Payment::STATUS_MANUAL_REVIEW => 'স্বয়ংক্রিয়ভাবে চূড়ান্ত সিদ্ধান্ত নেওয়া যায়নি, তাই donation-টি ম্যানুয়াল রিভিউ পর্যায়ে আছে।',
        Payment::STATUS_PENDING_VERIFICATION, Payment::STATUS_REDIRECT_PENDING => 'চেকআউট শুরু হয়েছে, তবে সার্ভার-সাইড ভেরিফিকেশন এখনো সম্পূর্ণ হয়নি। কিছু সময় পর আবার status দেখুন।',
        default => 'এই reference-এর donation অবস্থা এখনো চূড়ান্ত হয়নি।',
    };

    $statusPillClasses = match ($statusVariant) {
        'success' => 'border-emerald-200/80 bg-emerald-50 text-emerald-950',
        'warning' => 'border-amber-200/80 bg-amber-50 text-amber-950',
        default => 'border-sky-200/80 bg-sky-50 text-sky-950',
    };

    $heroSurfaceClasses = match ($statusVariant) {
        'success' => 'bg-[linear-gradient(135deg,rgba(8,70,56,0.98),rgba(15,86,68,0.96)_48%,rgba(28,110,89,0.88))] text-white',
        'warning' => 'bg-[linear-gradient(135deg,rgba(84,54,12,0.98),rgba(134,86,18,0.94)_48%,rgba(15,86,68,0.72))] text-white',
        default => 'bg-[linear-gradient(135deg,rgba(8,47,73,0.98),rgba(5,93,124,0.94)_50%,rgba(15,86,68,0.75))] text-white',
    };

    $amountLabel = $intent->currency === 'BDT'
        ? '৳ '.$bn(number_format((float) $intent->amount, 2))
        : $bn(number_format((float) $intent->amount, 2)).' '.$intent->currency;

    $donorModeLabel = $intent->donor_mode === DonationIntent::DONOR_MODE_GUEST ? 'গেস্ট অনুদান' : 'অ্যাকাউন্ট-সংযুক্ত অনুদান';
    $displayModeLabel = $intent->display_mode === DonationIntent::DISPLAY_MODE_ANONYMOUS ? 'গোপন পরিচয় প্রদর্শন' : 'নাম প্রদর্শন';

    $paymentStatusLabel = match ($payment?->status) {
        Payment::STATUS_PAID => 'পরিশোধিত',
        Payment::STATUS_FAILED => 'ব্যর্থ',
        Payment::STATUS_CANCELLED => 'বাতিল',
        Payment::STATUS_MANUAL_REVIEW => 'ম্যানুয়াল রিভিউ',
        Payment::STATUS_PENDING_VERIFICATION => 'ভেরিফিকেশন অপেক্ষমাণ',
        Payment::STATUS_REDIRECT_PENDING => 'রিডাইরেক্ট অপেক্ষমাণ',
        default => 'অপেক্ষমাণ',
    };

    $verificationStatusLabel = match ($payment?->verification_status) {
        Payment::VERIFICATION_VERIFIED => 'ভেরিফাইড',
        Payment::VERIFICATION_FAILED => 'ভেরিফিকেশন ব্যর্থ',
        Payment::VERIFICATION_MANUAL_REVIEW => 'ম্যানুয়াল রিভিউ',
        default => 'অপেক্ষমাণ',
    };

    $postingStatusLabel = match ($donationRecord?->posting_status) {
        'posted' => 'পোস্টেড',
        'pending' => 'অপেক্ষমাণ',
        'failed' => 'ব্যর্থ',
        'not_started', null => 'শুরু হয়নি',
        default => $donationRecord?->posting_status,
    };

    $settledAt = optional($donationRecord?->donated_at ?? $payment?->paid_at ?? $payment?->initiated_at)->format('d M Y, h:i A');
    $settledAtLabel = $settledAt ? $bn($settledAt) : 'এখনও নিশ্চিত হয়নি';

    $receiptIssuedAt = optional($receipt?->issued_at)->format('d M Y, h:i A');
    $receiptIssuedAtLabel = $receiptIssuedAt ? $bn($receiptIssuedAt) : null;

    $providerReference = $payment?->provider_reference ?: $payment?->idempotency_key ?: 'সংরক্ষিত হয়নি';
    $statusReason = $payment?->status_reason ?: 'এই donation-এর জন্য অতিরিক্ত কোনো কারণ এখনো সংরক্ষিত হয়নি।';
    $systemNoteTitle = $statusVariant === 'success' ? 'সিস্টেম কনফার্মেশন' : 'সিস্টেম নোট';
@endphp

<x-public-shell
    title="অনুদান অবস্থা"
    description="এই পেজে শুধু নির্দিষ্ট donation reference-এর নিরাপদ status দেখানো হয়; broader donor portal access খোলে না।"
    :nav-links="$navLinks"
>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap');

        .donation-status-page,
        .donation-status-page * {
            font-family: "Hind Siliguri", ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .donation-status-page {
            background:
                radial-gradient(circle at top left, rgba(16, 185, 129, 0.08), transparent 24%),
                radial-gradient(circle at top right, rgba(245, 158, 11, 0.08), transparent 22%),
                linear-gradient(180deg, #f8fafc 0%, #f5f7fb 100%);
        }

        .status-card-shadow {
            box-shadow:
                0 24px 60px -34px rgba(15, 23, 42, 0.16),
                0 12px 28px -20px rgba(15, 23, 42, 0.08);
        }

        .status-soft-card {
            box-shadow:
                0 18px 40px -30px rgba(15, 23, 42, 0.14),
                inset 0 1px 0 rgba(255,255,255,0.7);
        }

        .status-glass {
            backdrop-filter: blur(16px);
        }
    </style>

    <section class="donation-status-page space-y-6 sm:space-y-7 lg:space-y-8">
        <div class="relative overflow-hidden rounded-[2rem] border border-white/75 p-5 shadow-[0_32px_72px_-48px_rgba(15,23,42,0.42)] sm:p-6 lg:p-8 {{ $heroSurfaceClasses }}">
            <div class="pointer-events-none absolute -left-10 top-0 h-40 w-40 rounded-full bg-white/10 blur-3xl"></div>
            <div class="pointer-events-none absolute bottom-0 right-0 h-56 w-56 rounded-full bg-amber-200/15 blur-3xl"></div>

            <div class="relative grid gap-6 xl:grid-cols-[1.06fr,0.94fr] xl:items-start">
                <div>
                    <span class="inline-flex items-center rounded-full border border-white/15 bg-white/10 px-4 py-1.5 text-[11px] font-semibold tracking-[0.28em] text-white/75">
                        {{ $statusKicker }}
                    </span>

                    <div class="mt-5 inline-flex items-center gap-3 rounded-full border border-white/15 bg-white/10 px-4 py-2 text-sm font-semibold text-white">
                        @if ($statusVariant === 'success')
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-white/14">
                                <svg class="h-[1.125rem] w-[1.125rem]" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.42l-7.29 7.29a1 1 0 01-1.414 0l-3-3a1 1 0 111.414-1.414l2.293 2.293 6.583-6.59a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        @elseif ($statusVariant === 'warning')
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-white/14">
                                <svg class="h-[1.125rem] w-[1.125rem]" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.72-1.36 3.486 0l6.518 11.596c.75 1.334-.213 2.996-1.742 2.996H3.48c-1.53 0-2.492-1.662-1.742-2.996L8.257 3.1zM11 13a1 1 0 10-2 0 1 1 0 002 0zm-1-6a1 1 0 00-1 1v3a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        @else
                            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-white/14">
                                <svg class="h-[1.125rem] w-[1.125rem]" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M18 10A8 8 0 112 10a8 8 0 0116 0zm-7-3a1 1 0 10-2 0 1 1 0 002 0zm-1 3a1 1 0 00-.993.883L9 11v3a1 1 0 001.993.117L11 14v-3a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        @endif

                        <span>{{ $statusLabel }}</span>
                    </div>

                    <h1 class="mt-5 max-w-3xl text-3xl font-semibold leading-tight sm:text-4xl xl:text-[3.05rem]">
                        {{ $statusLabel }}
                    </h1>

                    <p class="mt-4 max-w-3xl text-base leading-8 text-white/82 sm:text-lg">
                        {{ $statusDescription }}
                    </p>

                    <div class="mt-6 grid gap-3 sm:grid-cols-2">
                        <div class="rounded-[1.4rem] border border-white/12 bg-white/10 px-4 py-4 backdrop-blur-sm">
                            <p class="text-[11px] font-semibold tracking-[0.24em] text-white/65">অনুদানের পরিমাণ</p>
                            <p class="mt-2 text-2xl font-semibold text-white">{{ $amountLabel }}</p>
                            <p class="mt-1 text-sm leading-6 text-white/70">এই outcome কেবল এই reference-এর সাথেই সীমাবদ্ধ।</p>
                        </div>

                        <div class="rounded-[1.4rem] border border-white/12 bg-white/10 px-4 py-4 backdrop-blur-sm">
                            <p class="text-[11px] font-semibold tracking-[0.24em] text-white/65">অনুদানের খাত</p>
                            <p class="mt-2 text-xl font-semibold text-white">{{ $categoryLabel }}</p>
                            <p class="mt-1 text-sm leading-6 text-white/70">{{ $donorModeLabel }} · {{ $displayModeLabel }}</p>
                        </div>
                    </div>
                </div>

                <aside class="status-glass rounded-[1.8rem] border border-white/14 bg-white/10 p-5 shadow-[0_24px_50px_-38px_rgba(15,23,42,0.55)] sm:p-6">
                    <p class="text-[11px] font-semibold tracking-[0.28em] text-white/65">দ্রুত সারাংশ</p>

                    <div class="mt-5 space-y-4">
                        <div class="rounded-[1.3rem] border border-white/12 bg-white/10 px-4 py-4">
                            <p class="text-sm text-white/65">Public reference</p>
                            <p class="mt-2 break-all text-lg font-semibold text-white">{{ $intent->public_reference }}</p>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="rounded-[1.3rem] border border-white/12 bg-white/10 px-4 py-4">
                                <p class="text-sm text-white/65">রসিদ</p>
                                <p class="mt-2 text-base font-semibold text-white">{{ $receipt?->receipt_number ?: 'এখনও ইস্যু হয়নি' }}</p>
                            </div>

                            <div class="rounded-[1.3rem] border border-white/12 bg-white/10 px-4 py-4">
                                <p class="text-sm text-white/65">সময়</p>
                                <p class="mt-2 text-base font-semibold text-white">{{ $settledAtLabel }}</p>
                            </div>
                        </div>

                        @if ($receiptIssuedAtLabel)
                            <div class="rounded-[1.3rem] border border-emerald-200/10 bg-emerald-50/10 px-4 py-4 text-sm leading-7 text-white/82">
                                রসিদ <strong>{{ $receipt->receipt_number }}</strong> ইস্যু হয়েছে <strong>{{ $receiptIssuedAtLabel }}</strong> সময়ে।
                            </div>
                        @endif
                    </div>

                    <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                        <a href="{{ route('donations.guest.entry') }}" class="ui-public-action ui-public-action--primary w-full justify-center sm:w-auto">
                            নতুন অনুদান দিন
                        </a>

                        @if (Route::has('login'))
                            <a href="{{ route('login') }}" class="ui-public-action ui-public-action--ghost w-full justify-center sm:w-auto">
                                লগইন
                            </a>
                        @endif
                    </div>
                </aside>
            </div>
        </div>

        <x-ui.alert :variant="$statusVariant" :title="$systemNoteTitle">
            {{ $statusMessage }}
        </x-ui.alert>

        <div class="grid gap-6 xl:grid-cols-[1.14fr,0.86fr]">
            <div class="space-y-6">
                <div class="status-card-shadow overflow-hidden rounded-[1.85rem] border border-slate-200/80 bg-white">
                    <div class="border-b border-slate-100 bg-[linear-gradient(135deg,rgba(255,251,235,0.85),rgba(236,253,245,0.72))] px-5 py-5 sm:px-6">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                            <div>
                                <p class="text-[11px] font-semibold tracking-[0.28em] text-emerald-800">ভেরিফিকেশন বিশদ</p>
                                <h2 class="mt-3 text-2xl font-semibold leading-tight text-slate-950 sm:text-[2rem]">
                                    এই অনুদানের বর্তমান outcome
                                </h2>
                                <p class="mt-2 max-w-2xl text-sm leading-7 text-slate-600 sm:text-base">
                                    payment, verification, provider reference এবং accounting posting-এর নিরাপদ সারাংশ এখানে দেখানো হচ্ছে।
                                </p>
                            </div>

                            <span class="inline-flex w-fit items-center rounded-full border px-4 py-2 text-sm font-semibold {{ $statusPillClasses }}">
                                {{ $statusLabel }}
                            </span>
                        </div>
                    </div>

                    <div class="px-5 py-5 sm:px-6 sm:py-6">
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="status-soft-card rounded-[1.35rem] border border-slate-200/80 bg-slate-50/70 px-4 py-4">
                                <dt class="text-[11px] font-semibold tracking-[0.22em] text-slate-500">Payment status</dt>
                                <dd class="mt-2 text-lg font-semibold text-slate-950">{{ $paymentStatusLabel }}</dd>
                            </div>

                            <div class="status-soft-card rounded-[1.35rem] border border-slate-200/80 bg-slate-50/70 px-4 py-4">
                                <dt class="text-[11px] font-semibold tracking-[0.22em] text-slate-500">Verification</dt>
                                <dd class="mt-2 text-lg font-semibold text-slate-950">{{ $verificationStatusLabel }}</dd>
                            </div>

                            <div class="status-soft-card rounded-[1.35rem] border border-slate-200/80 bg-slate-50/70 px-4 py-4">
                                <dt class="text-[11px] font-semibold tracking-[0.22em] text-slate-500">Provider</dt>
                                <dd class="mt-2 text-lg font-semibold text-slate-950">{{ strtoupper($payment?->provider ?: 'shurjopay') }}</dd>
                            </div>

                            <div class="status-soft-card rounded-[1.35rem] border border-slate-200/80 bg-slate-50/70 px-4 py-4">
                                <dt class="text-[11px] font-semibold tracking-[0.22em] text-slate-500">Posting status</dt>
                                <dd class="mt-2 text-lg font-semibold text-slate-950">{{ $postingStatusLabel }}</dd>
                            </div>

                            <div class="status-soft-card rounded-[1.35rem] border border-slate-200/80 bg-white px-4 py-4 md:col-span-2">
                                <dt class="text-[11px] font-semibold tracking-[0.22em] text-slate-500">Provider reference</dt>
                                <dd class="mt-2 break-all text-sm leading-7 text-slate-700">{{ $providerReference }}</dd>
                            </div>

                            <div class="status-soft-card rounded-[1.35rem] border border-slate-200/80 bg-white px-4 py-4 md:col-span-2">
                                <dt class="text-[11px] font-semibold tracking-[0.22em] text-slate-500">Status reason</dt>
                                <dd class="mt-2 text-sm leading-7 text-slate-700">{{ $statusReason }}</dd>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="status-card-shadow overflow-hidden rounded-[1.85rem] border border-slate-200/80 bg-white">
                    <div class="border-b border-slate-100 bg-[linear-gradient(135deg,rgba(248,250,252,0.92),rgba(240,253,250,0.82))] px-5 py-5 sm:px-6">
                        <p class="text-[11px] font-semibold tracking-[0.28em] text-emerald-800">নিরাপদ সীমা</p>
                        <h3 class="mt-3 text-2xl font-semibold leading-tight text-slate-950">
                            এই পেজের সীমাবদ্ধতা ও নিরাপদ ব্যাখ্যা
                        </h3>
                    </div>

                    <div class="grid gap-4 px-5 py-5 sm:px-6 sm:py-6 lg:grid-cols-2">
                        <div class="rounded-[1.35rem] border border-emerald-200/70 bg-emerald-50/65 px-4 py-4">
                            <p class="text-base font-semibold text-slate-950">এই পেজের সীমা</p>
                            <p class="mt-2 text-sm leading-7 text-slate-600">
                                এই status page শুধু নির্দিষ্ট donation reference-এর সীমিত অবস্থা দেখায়। broader donor portal history বা protected account data এখানে খোলে না।
                            </p>
                        </div>

                        <div class="rounded-[1.35rem] border border-slate-200/80 bg-white px-4 py-4">
                            <p class="text-base font-semibold text-slate-950">{{ $donorModeLabel }}</p>
                            <p class="mt-2 text-sm leading-7 text-slate-600">
                                @if ($intent->donor_mode === DonationIntent::DONOR_MODE_GUEST)
                                    গেস্ট অনুদান স্বয়ংক্রিয়ভাবে donor portal access তৈরি করে না। এই reference ও access material-ই আপনার transaction-specific lookup মাধ্যম।
                                @else
                                    এই donation বর্তমান account ownership-এর সাথে সীমিতভাবে যুক্ত। broader donor history বা অতিরিক্ত portal access আলাদা flow-এর বিষয়।
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

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
                        label="Category"
                        value="{{ $intent->resolvedDonationCategoryLabel() ?: 'General donation' }}"
                        meta="Resolved from the live relation when available, otherwise from the stored metadata snapshot."
                    />
                </div>
            </div>

            <div class="space-y-6">
                <div class="status-card-shadow overflow-hidden rounded-[1.85rem] border border-slate-200/80 bg-white">
                    <div class="border-b border-slate-100 bg-[linear-gradient(135deg,rgba(255,251,235,0.85),rgba(255,255,255,0.9))] px-5 py-5 sm:px-6">
                        <p class="text-[11px] font-semibold tracking-[0.28em] text-emerald-800">Access material</p>
                        <h3 class="mt-3 text-2xl font-semibold leading-tight text-slate-950">
                            Reference, key ও direct link
                        </h3>
                        <p class="mt-2 text-sm leading-7 text-slate-600">
                            guest access বা transaction-specific narrow access-এর জন্য নিচের তথ্যগুলোই গুরুত্বপূর্ণ।
                        </p>
                    </div>

                    <div class="space-y-4 px-5 py-5 sm:px-6 sm:py-6">
                        <div class="rounded-[1.25rem] border border-slate-200/80 bg-slate-50/70 px-4 py-4">
                            <p class="text-[11px] font-semibold tracking-[0.22em] text-slate-500">Public reference</p>
                            <p class="mt-2 break-all font-mono text-sm text-slate-900">{{ $intent->public_reference }}</p>
                        </div>

                        @if ($accessKeyForDisplay)
                            <div class="rounded-[1.25rem] border border-slate-200/80 bg-slate-50/70 px-4 py-4">
                                <p class="text-[11px] font-semibold tracking-[0.22em] text-slate-500">Access key</p>
                                <p class="mt-2 break-all font-mono text-sm text-slate-900">{{ $accessKeyForDisplay }}</p>
                            </div>
                        @endif

                        <div class="rounded-[1.25rem] border border-slate-200/80 bg-slate-50/70 px-4 py-4">
                            <p class="text-[11px] font-semibold tracking-[0.22em] text-slate-500">Direct status link</p>
                            <p class="mt-2 break-all text-sm leading-7 text-slate-700">{{ $statusLink }}</p>
                        </div>

                        @unless ($accessKeyForDisplay)
                            <div class="rounded-[1.25rem] border border-amber-200 bg-amber-50/90 px-4 py-4 text-sm leading-7 text-slate-700">
                                এই session-এ access key সংরক্ষিত নেই। প্রয়োজনে সংরক্ষিত key ব্যবহার করুন, অথবা appropriate account দিয়ে sign in করুন।
                            </div>
                        @endunless
                    </div>
                </div>

                <div class="status-card-shadow overflow-hidden rounded-[1.85rem] border border-slate-200/80 bg-white">
                    <div class="border-b border-slate-100 bg-[linear-gradient(135deg,rgba(240,253,250,0.82),rgba(255,255,255,0.9))] px-5 py-5 sm:px-6">
                        <p class="text-[11px] font-semibold tracking-[0.28em] text-emerald-800">পরবর্তী নিরাপদ পদক্ষেপ</p>
                        <h3 class="mt-3 text-2xl font-semibold leading-tight text-slate-950">
                            এখন কী করবেন
                        </h3>
                    </div>

                    <div class="px-5 py-5 sm:px-6 sm:py-6">
                        <ul class="space-y-3 text-sm leading-7 text-slate-600">
                            <li class="rounded-[1.15rem] border border-emerald-200/70 bg-emerald-50/65 px-4 py-3">
                                এই public reference এবং access key একসাথে নিরাপদে সংরক্ষণ করুন।
                            </li>
                            <li class="rounded-[1.15rem] border border-slate-200/80 bg-white px-4 py-3">
                                নতুন অনুদান প্রয়োজন হলে donation entry page থেকে নতুন করে শুরু করুন।
                            </li>
                            <li class="rounded-[1.15rem] border border-slate-200/80 bg-white px-4 py-3">
                                verified status, receipt বা provider outcome নিয়ে প্রশ্ন থাকলে support-এর সাথে reference ব্যবহার করে যোগাযোগ করুন।
                            </li>
                        </ul>

                        <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                            <a href="{{ route('donations.guest.entry') }}" class="ui-public-action ui-public-action--primary w-full justify-center sm:w-auto">
                                আবার অনুদান দিন
                            </a>

                            @if (Route::has('login'))
                                <a href="{{ route('login') }}" class="ui-public-action ui-public-action--ghost w-full justify-center sm:w-auto">
                                    অ্যাকাউন্টে প্রবেশ
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="rounded-[1.6rem] border border-slate-200/80 bg-white/90 p-5 text-sm leading-7 text-slate-600 shadow-[0_18px_40px_-32px_rgba(15,23,42,0.18)] sm:p-6">
                    <p class="text-[11px] font-semibold tracking-[0.24em] text-slate-500">রসিদ ও সময়</p>
                    <div class="mt-3 space-y-2">
                        <p><span class="font-semibold text-slate-900">রসিদ:</span> {{ $receipt?->receipt_number ?: 'এখনও ইস্যু হয়নি' }}</p>
                        <p><span class="font-semibold text-slate-900">নিশ্চিত হওয়ার সময়:</span> {{ $settledAtLabel }}</p>
                        @if ($receiptIssuedAtLabel)
                            <p><span class="font-semibold text-slate-900">রসিদ ইস্যু:</span> {{ $receiptIssuedAtLabel }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-public-shell>