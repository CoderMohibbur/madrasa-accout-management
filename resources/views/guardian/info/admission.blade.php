<x-guardian-informational-layout
    title="Admission guidance"
    description="Review admission summary, external application guidance, and self-only next steps without turning this portal into an internal admission workflow."
>
    <div class="grid gap-6 xl:grid-cols-[1.05fr,0.95fr]">
        <x-ui.card
            title="Admission stays an external handoff"
            description="This guardian informational surface can explain the public-facing admission path and safe next steps, but applications, uploads, drafts, and decisions stay outside this Laravel application."
        >
            <x-slot name="headerActions">
                <x-ui.badge variant="info">Admission information</x-ui.badge>
            </x-slot>

            <div class="space-y-3">
                @foreach ($admissionChecklist as $item)
                    <div class="rounded-[1.5rem] border border-slate-200 bg-stone-50 px-4 py-3 text-sm leading-6 text-slate-700">
                        {{ $item }}
                    </div>
                @endforeach
            </div>
        </x-ui.card>

        <x-ui.card
            title="External application handoff"
            description="Applications, uploads, draft saving, and status decisions stay on the institution's external admission site."
        >
            <x-admission.external-handoff
                :url="$admissionUrl"
                safety-message="Leaving this page does not expose any protected student, invoice, receipt, or payment records."
            />

            <div class="mt-5 rounded-[1.5rem] border border-slate-200 bg-stone-50 px-5 py-4 text-sm leading-7 text-slate-600">
                This page is informational only. It does not store application drafts, upload documents, or reveal applicant status.
            </div>
        </x-ui.card>
    </div>

    <div class="mt-8 grid gap-5 lg:grid-cols-3">
        @foreach ($admissionSteps as $step)
            <x-ui.card :title="$step['title']" soft>
                <p class="text-sm leading-7 text-slate-600">{{ $step['description'] }}</p>
            </x-ui.card>
        @endforeach
    </div>

    <div class="mt-8">
        <x-ui.alert variant="info" title="Protected boundary preserved">
            Admission guidance stays external-only here, and protected guardian data remains on separate routes.
        </x-ui.alert>
    </div>
</x-guardian-informational-layout>
