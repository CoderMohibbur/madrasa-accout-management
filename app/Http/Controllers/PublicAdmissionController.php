<?php

namespace App\Http\Controllers;

use App\Services\Portal\ExternalAdmissionUrlResolver;
use Illuminate\Contracts\View\View;

class PublicAdmissionController extends Controller
{
    public function __construct(
        private readonly ExternalAdmissionUrlResolver $externalAdmissionUrlResolver,
    ) {
    }

    public function show(): View
    {
        return view('admission.show', [
            'admissionUrl' => $this->externalAdmissionUrlResolver->resolve(),
            'admissionChecklist' => $this->admissionChecklist(),
            'admissionHighlights' => $this->admissionHighlights(),
        ]);
    }

    private function admissionChecklist(): array
    {
        return [
            'Review the public admission overview before leaving this portal.',
            'Prepare basic applicant details and any required supporting documents before opening the external application.',
            'Use the published institution contact channels if you need clarification before starting the external form.',
        ];
    }

    private function admissionHighlights(): array
    {
        return [
            [
                'title' => 'Public guidance first',
                'description' => 'Admission information stays visible here even when the live external destination is unavailable.',
            ],
            [
                'title' => 'External-only application',
                'description' => 'Applications, uploads, drafts, and submission tracking remain outside this Laravel application.',
            ],
            [
                'title' => 'Protected routes stay separate',
                'description' => 'Guardian, donor, payment, and management routes are not reused as admission fallbacks.',
            ],
        ];
    }
}
