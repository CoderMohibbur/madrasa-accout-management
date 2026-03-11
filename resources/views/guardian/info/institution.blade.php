<x-guardian-informational-layout
    title="Institution information"
    description="Review non-sensitive institution guidance, public-facing highlights, and safe help pathways for guardian accounts."
>
    <div class="grid gap-6 xl:grid-cols-[1.1fr,0.9fr]">
        <x-ui.card
            title="A calm guardian-facing starting point"
            description="This informational area keeps institution overview, support guidance, and guardian next steps visible without widening access into linked student, invoice, receipt, or payment data."
        >
            <x-slot name="headerActions">
                <x-ui.badge variant="info">Institution overview</x-ui.badge>
            </x-slot>

            <div class="grid gap-4 md:grid-cols-3">
                @foreach ($institutionHighlights as $highlight)
                    <div class="rounded-[1.5rem] border border-slate-200 bg-stone-50 px-4 py-4">
                        <div class="text-sm font-semibold text-slate-950">{{ $highlight['title'] }}</div>
                        <p class="mt-3 text-sm leading-7 text-slate-600">{{ $highlight['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </x-ui.card>

        <x-ui.card
            title="Safe next actions"
            description="These steps keep the guardian informational surface useful without crossing into protected work."
            soft
        >
            <div class="space-y-3">
                <div class="rounded-[1.5rem] border border-slate-200 bg-white px-4 py-3 text-sm leading-6 text-slate-700">
                    Keep your shared account profile current so later guardian review or linkage steps can proceed safely.
                </div>
                <div class="rounded-[1.5rem] border border-slate-200 bg-white px-4 py-3 text-sm leading-6 text-slate-700">
                    Use the admission guidance page for public-facing enrollment information and the external application handoff only.
                </div>
                <div class="rounded-[1.5rem] border border-slate-200 bg-white px-4 py-3 text-sm leading-6 text-slate-700">
                    Continue to treat student, invoice, and payment-sensitive tasks as protected-only work on separate guardian routes.
                </div>
            </div>
        </x-ui.card>
    </div>

    <div class="mt-8 grid gap-5 lg:grid-cols-3">
        @foreach ($supportChannels as $channel)
            <x-ui.card :title="$channel['label']" :description="$channel['value']">
                <p class="text-sm leading-7 text-slate-600">{{ $channel['description'] }}</p>
            </x-ui.card>
        @endforeach
    </div>

    <div class="mt-8">
        <x-ui.alert variant="info" title="Informational-only route">
            This page stays limited to non-sensitive institution information and help guidance. No student, invoice, receipt, or payment-specific records are rendered here.
        </x-ui.alert>
    </div>
</x-guardian-informational-layout>
