# Prompt 36 Guardian Informational Portal

## Route Surface

- `GET /guardian/info`
- `GET /guardian/info/institution`
- `GET /guardian/info/admission`

## Informational Access States

- `guardian_informational`
  - accessible shared account state
  - guardian-domain context through a guardian profile or guardian role
  - result: guardian informational overview, institution, and admission surfaces
- `guardian_protected_separate`
  - protected-eligible guardians may still open the informational routes directly
  - result: informational routes remain non-sensitive, while the default verified protected flow stays on `/guardian`
- `forbidden`
  - no accessible account state or no guardian-domain context
  - result: fail closed

## Redirect Rules

- donor-only precedence remains intact from prompt-35
- guardian-only informational accounts may resolve to `guardian.info.dashboard` after:
  - login
  - verification completion
  - verified `/dashboard` redirect handling
- verified protected guardians continue to use the existing `/dashboard` -> `/guardian` behavior
- management routing remains unchanged
- multi-role chooser behavior remains deferred

## Content Boundaries

- allowed:
  - institution overview
  - guardian linkage/help/status guidance
  - curated admission guidance
  - external application handoff
- forbidden:
  - student records
  - invoices
  - receipts
  - payment history
  - payment-entry controls

## External Admission Handoff

- source: `portal.admission.external_url`
- accepts only absolute `https://` destinations
- missing or invalid config removes the live CTA and shows neutral guidance
- prompt-41 still owns the later public-surface centralization pass

## Preserved Boundaries

- existing protected `/guardian` routes and guardian payment behavior remain unchanged
- prompt-35 donor no-portal behavior and donor history bridge remain unchanged
- prompt-37 protected guardian gating remains the next slice
- auth pages and protected guardian pages remain outside the approved admission CTA placements
