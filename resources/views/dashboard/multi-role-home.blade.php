<x-portal-shell
    title="Shared Home"
    description="Use one shared landing point for eligible donor and guardian contexts without mixing donor-owned and guardian-owned records on a single screen."
    portal-label="Shared Home"
    home-route="dashboard"
    badge-variant="neutral"
    :nav-items="[]"
>
    @if ($showChooser)
        <x-ui.alert variant="info" title="Choose an eligible context">
            Switching stays explicit. Each destination applies its own donor, guardian informational, or guardian protected rules before showing data.
        </x-ui.alert>

        <div class="mt-8 grid gap-5 lg:grid-cols-2">
            @foreach ($contexts as $context)
                <x-ui.card :title="$context['title']" :description="$context['description']">
                    <x-slot name="headerActions">
                        <x-ui.badge :variant="$context['badge_variant']">{{ $context['status'] }}</x-ui.badge>
                    </x-slot>

                    <div class="space-y-4">
                        <p class="text-sm leading-6 text-slate-600">{{ $context['scope_note'] }}</p>

                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route($context['route_name']) }}" class="ui-button ui-button--primary">
                                Open {{ $context['title'] }}
                            </a>
                            <a href="{{ route('profile.edit') }}" class="ui-button ui-button--secondary">
                                Profile settings
                            </a>
                        </div>
                    </div>
                </x-ui.card>
            @endforeach
        </div>
    @else
        <x-ui.empty-state
            title="No donor or guardian context is active yet"
            description="This shared home stays neutral until donor or guardian eligibility exists. You can keep the shared account ready without being routed into a protected portal."
        >
            <x-slot name="actions">
                @if ($stateSummary['registered_user'])
                    <a href="{{ route('registration.onboarding') }}" class="ui-button ui-button--primary">
                        Continue onboarding
                    </a>
                @endif

                <a href="{{ route('profile.edit') }}" class="ui-button ui-button--secondary">
                    Profile settings
                </a>
            </x-slot>
        </x-ui.empty-state>
    @endif

    <div class="mt-8 grid gap-5 lg:grid-cols-2">
        <x-ui.card title="Current account state" description="Status only. This shared home does not aggregate donor history, guardian records, invoices, or receipts.">
            <dl class="grid gap-4 sm:grid-cols-3">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                    <dt class="text-xs uppercase tracking-[0.16em] text-slate-500">Email verification</dt>
                    <dd class="mt-2 text-sm font-semibold text-slate-900">{{ $stateSummary['email_verified'] ? 'Verified' : 'Pending' }}</dd>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                    <dt class="text-xs uppercase tracking-[0.16em] text-slate-500">Google sign-in</dt>
                    <dd class="mt-2 text-sm font-semibold text-slate-900">{{ $stateSummary['google_linked'] ? 'Linked' : 'Not linked' }}</dd>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                    <dt class="text-xs uppercase tracking-[0.16em] text-slate-500">Shared account</dt>
                    <dd class="mt-2 text-sm font-semibold text-slate-900">{{ $stateSummary['registered_user'] ? 'Open registration foundation' : 'Legacy account' }}</dd>
                </div>
            </dl>
        </x-ui.card>

        <x-ui.card title="Scope isolation rules" description="These protections stay in force even when one login can reach more than one context.">
            <div class="space-y-3">
                <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm leading-6 text-slate-700">
                    Donor views stay limited to donor-owned donations and donor-visible receipts.
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm leading-6 text-slate-700">
                    Guardian informational views stay non-sensitive and never show linked students, invoices, receipts, or payment-entry controls.
                </div>
                <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm leading-6 text-slate-700">
                    Guardian protected views still require real linkage, verified email, and protected eligibility on every route.
                </div>
            </div>
        </x-ui.card>
    </div>
</x-portal-shell>
