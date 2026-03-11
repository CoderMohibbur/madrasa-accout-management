# Report

This prompt defines the admission-information boundary on top of the approved prompt-03 business rules and the prompt-13 guardian informational-portal boundary. No contradiction was found with prompt-13. The live repository is currently narrower than the approved target because the public site only exposes a hard-coded external admission link in `resources/views/welcome.blade.php`, while the authenticated guardian informational portal does not exist yet. Prompt-14 therefore defines the approved target content boundary without treating the current single external link as the complete future design.

## Current-State Findings

- Public admission exposure today is limited to the `Admission` navigation link on `resources/views/welcome.blade.php`, which points directly to `https://attawheedic.com/admission/`.
- The public site also links out to external `Home`, `About`, `Contact`, and `Donate` pages, so the current landing experience is already mostly a public handoff shell rather than an internal content hub.
- There is no internal public admission-information page in the Laravel app yet.
- There is no authenticated guardian informational portal yet; every current `/guardian` route is protected-scope only.
- Prompt-13 already froze that authenticated guardian informational access may include non-sensitive institution and admission-related information only, while student/invoice/payment-sensitive content remains linkage-controlled.
- Prompt-19 and prompt-41 are the later phases for external admission URL configuration and implementation, so prompt-14 must define the boundary but not lock in low-level config mechanics yet.

## Public Admission-Information Scope

The following admission-related information may be shown publicly:

- institution overview, mission, and campus/contact basics
- class/program overview at a general level
- admission availability messaging such as `admission open`, `admission closed`, or `next intake expected`
- high-level eligibility or audience information
- high-level required-document checklist
- high-level process steps such as `review requirements`, `submit external application`, `await contact`
- general contact/help instructions for admission questions
- one external `Apply` / `Start Admission` / `External Admission Form` link or button

Public admission information must remain generic and non-personalized. It must not expose:

- application drafts
- application status
- seat allocation or waitlist details
- student-linked internal records
- internal review notes
- invoice, receipt, or payment-sensitive details

## Authenticated Admission-Information Scope

Admission-related information that may be shown only after login belongs inside the light guardian informational surface approved by prompt-13.

It may include:

- everything approved for public admission information
- guardian-specific onboarding help
- linkage-help messaging and what protected access requires next
- self-only reminders such as missing verification, missing guardian profile data, or unlinked status
- safe next-step guidance such as `complete profile`, `contact support`, or `open external admission application`
- a return path back to profile settings or the neutral authenticated home once those surfaces exist

It must not include:

- internal admission-form drafts or resume flow
- any admission decision/status tracking owned by this Laravel app
- linked-student, invoice, receipt, or payment-sensitive content
- any shortcut that implies successful guardian linkage or protected entitlement
- any donor-specific payment or donor-history content

## External Application Link Rules

The external admission application button/link must behave as an explicit handoff to a different system, not as an internal workflow step.

Approved rules:

- use one canonical destination per environment, with later configuration defined in prompt-19 and implemented in prompt-41
- present the link in approved public and authenticated informational placements only
- label the action clearly as external, for example `Apply Externally` or `Continue to External Admission Form`
- keep the handoff simple: a normal link or button to the external URL, not an embedded multi-step internal flow
- preserve user expectations by making it clear they are leaving the informational surface and continuing elsewhere
- avoid hard-coded duplicated URLs across multiple views once the later configuration prompt is executed

Not approved here:

- automatic account creation in the external system
- local storage of admission-form progress
- iframe embedding of a full external application inside the protected guardian portal
- hidden redirects that make the external handoff look like an internal authenticated resource

## Hard Non-Goals

This project must not become a full admission application system. Prompt-14 therefore rejects all of the following as out of scope:

- internal admission application forms
- application draft save/resume behavior
- document upload or evidence collection for admission
- internal admission review queues or staff workflow
- internal admission decision/status tracking
- seat assignment, batch placement, or enrollment conversion workflow
- admission-fee collection tied to a new internal application domain
- automated guardian linkage, portal eligibility, or student record creation just because someone clicked the admission link

## Content-Approach Recommendation

The safest, lowest-complexity approach is a small, curated content model with one shared admission-information set reused in public and authenticated informational placements.

Recommended approach:

- keep most institution and admission content public by default
- reuse that same core content inside the future guardian informational portal rather than writing a second divergent version
- reserve authenticated-only content for self-only status/help messaging, not for a richer admission product
- keep admission copy mostly static or editorially maintained, not workflow-driven
- use the external application link as the clear boundary between informational content and the real application process
- treat the current hard-coded public admission link as a temporary current-state implementation detail, not as the final duplication pattern

## Completion Status

- No contradiction with prompt-13 or earlier approved boundary decisions was found.
- No correction pass was required.
- Prompt-14 is complete.
- No hard blocker prevents prompt-15.
