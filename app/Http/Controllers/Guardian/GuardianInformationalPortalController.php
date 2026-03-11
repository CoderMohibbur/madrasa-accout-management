<?php

namespace App\Http\Controllers\Guardian;

use App\Http\Controllers\Controller;
use App\Services\GuardianPortal\GuardianInformationalPortalData;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class GuardianInformationalPortalController extends Controller
{
    public function __construct(
        private readonly GuardianInformationalPortalData $guardianInformationalPortalData,
    ) {
    }

    public function index(Request $request): View
    {
        $access = $this->guardianInformationalPortalData->requireInformationalAccess($request->user());

        return view('guardian.info.dashboard', [
            'access' => $access,
            'guardian' => $access->guardian,
            'institutionHighlights' => $this->institutionHighlights(),
            'supportChannels' => $this->supportChannels(),
        ]);
    }

    public function institution(Request $request): View
    {
        $access = $this->guardianInformationalPortalData->requireInformationalAccess($request->user());

        return view('guardian.info.institution', [
            'access' => $access,
            'guardian' => $access->guardian,
            'institutionHighlights' => $this->institutionHighlights(),
            'supportChannels' => $this->supportChannels(),
        ]);
    }

    public function admission(Request $request): View
    {
        $access = $this->guardianInformationalPortalData->requireInformationalAccess($request->user());

        return view('guardian.info.admission', [
            'access' => $access,
            'guardian' => $access->guardian,
            'admissionUrl' => $this->guardianInformationalPortalData->admissionExternalUrl(),
            'admissionChecklist' => $this->admissionChecklist(),
            'admissionSteps' => $this->admissionSteps(),
        ]);
    }

    private function institutionHighlights(): array
    {
        return [
            [
                'title' => 'Institution overview',
                'description' => 'Review the institution story, learning environment, and public-facing guidance without entering any protected student or invoice screen.',
            ],
            [
                'title' => 'Guardian onboarding',
                'description' => 'Keep your shared account ready while guardian linkage, eligibility, and protected access continue through separate approval steps.',
            ],
            [
                'title' => 'Support pathways',
                'description' => 'Use profile tools and the public institution contact channels for help without widening guardian access automatically.',
            ],
        ];
    }

    private function supportChannels(): array
    {
        return [
            [
                'label' => 'Public site',
                'value' => 'At-Tawheed Islami Complex',
                'description' => 'Use the institution website for current public information, contact details, and general announcements.',
            ],
            [
                'label' => 'Shared account tools',
                'value' => 'Profile + verification',
                'description' => 'Keep your account email, phone, and password current without assuming student, invoice, or payment eligibility.',
            ],
            [
                'label' => 'Guardian boundary',
                'value' => 'Informational only',
                'description' => 'Student details, invoices, receipts, and payment controls remain on separate protected guardian routes.',
            ],
        ];
    }

    private function admissionChecklist(): array
    {
        return [
            'Review the institution admission overview and requirements before leaving this portal.',
            'Prepare basic applicant details and supporting documents before using the external application site.',
            'Use the published institution help channels if you need clarification before starting the external form.',
        ];
    }

    private function admissionSteps(): array
    {
        return [
            [
                'title' => 'Review admission guidance',
                'description' => 'This portal can show only high-level admission information, reminders, and next-step guidance.',
            ],
            [
                'title' => 'Confirm account readiness',
                'description' => 'Profile and verification tools stay available here, but they do not turn this portal into an internal admission workflow.',
            ],
            [
                'title' => 'Continue on the external site',
                'description' => 'Applications, uploads, draft saving, and submission tracking stay outside this Laravel application.',
            ],
        ];
    }
}
