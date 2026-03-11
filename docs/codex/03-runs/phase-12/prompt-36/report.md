# Report

## Scope Lock Before Coding

### Exact Approved Slice

- Prompt-36 is limited to the guardian informational rollout only.
- The approved implementation slice was limited to:
  - a separate authenticated guardian informational route space
  - non-sensitive institution information surfaces
  - admission-related information surfaces
  - external application handoff surfaces
  - the minimum auth/read-path changes needed so unverified or unlinked guardian-context accounts can reach that informational surface safely
- Prompt-36 had to preserve:
  - prompt-35 donor access separation and donor no-portal behavior
  - the existing protected `/guardian` route space and guardian invoice/payment behavior
  - prompt-14's admission boundary as external-only
  - prompt-17's deferral of multi-role chooser behavior
  - prompt-41 as the later full external-admission centralization pass

### Guardian Informational Permission Rules Restated

- Guardian informational access and guardian protected access are separate approved boundaries.
- Guardian login and guardian informational access must not depend on universal email or phone verification.
- Guardian role membership expresses guardian-domain potential only; it is not the final protected-portal gate.
- Informational guardian surfaces may show only institution information, admission guidance, and self-only help/status messaging.
- Students, invoices, receipts, payment history, and payment-entry controls remain protected-only surfaces.

### Protected Boundaries That Had To Stay Untouched

- The live protected `/guardian` routes had to stay protected-only in this step.
- Prompt-36 could not broaden student, invoice, receipt, or payment-sensitive reads.
- Prompt-36 could not reopen donor checkout, guardian invoice settlement, manual-bank flow, or shurjoPay behavior.
- Prompt-36 could not introduce multi-role chooser/switching logic or reuse blanket `verified` + raw role coupling as the final gate.

## Implementation Result

Prompt-36 completed inside the approved guardian informational scope.

### Files Changed

- `config/portal.php`
- `app/Services/GuardianPortal/GuardianInformationalAccessState.php`
- `app/Services/GuardianPortal/GuardianInformationalPortalData.php`
- `app/Http/Controllers/Guardian/GuardianInformationalPortalController.php`
- `routes/guardian-info.php`
- `resources/views/components/guardian-informational-layout.blade.php`
- `resources/views/guardian/info/dashboard.blade.php`
- `resources/views/guardian/info/institution.blade.php`
- `resources/views/guardian/info/admission.blade.php`
- `routes/web.php`
- `app/Http/Controllers/Auth/AuthenticatedSessionController.php`
- `app/Http/Controllers/Auth/EmailVerificationPromptController.php`
- `app/Http/Controllers/Auth/VerifyEmailController.php`
- `app/Http/Controllers/Auth/EmailVerificationNotificationController.php`
- `app/Http/Middleware/RedirectPortalUsersFromLegacyDashboard.php`
- `tests/Feature/Phase12/GuardianInformationalPortalTest.php`
- `tests/Feature/Phase12/AccountStateReadPathAdaptationTest.php`

### Guardian Informational Behavior Implemented

- Added a dedicated auth-only guardian informational route space:
  - `GET /guardian/info`
  - `GET /guardian/info/institution`
  - `GET /guardian/info/admission`
- Added a guardian informational access-state resolver that derives informational access from:
  - shared accessible account state
  - guardian-domain context through either a linked guardian profile or a guardian role row
  - current protected eligibility without collapsing informational and protected behavior together
- Added three additive non-sensitive guardian informational screens:
  - overview / safe next actions
  - institution information
  - admission guidance + external handoff
- Added guardian-only informational landing behavior for:
  - unverified guardian foundation accounts after login
  - verified but unlinked guardian accounts when `/dashboard` is reached
  - already-verified guardian-context accounts revisiting the verification prompt or verification resend path
- Preserved protected-guardian default behavior for already-eligible verified guardians by keeping the existing `/guardian` redirect path intact.

### Informational Content And External Handoff Implemented

- Guardian informational pages now render:
  - institution overview and support pathways
  - guardian linkage/help/status guidance
  - curated admission checklist and process notes
  - an external application CTA on the guardian informational admission page only
- The guardian informational admission CTA now resolves through `portal.admission.external_url`.
- The CTA fails closed when the config is missing or invalid:
  - only absolute `https://` URLs are accepted
  - the page falls back to neutral informational messaging instead of guessing another destination
- Prompt-36 intentionally did not rewrite the existing public welcome-page admission link; prompt-41 still owns the cross-product centralization pass.

### Protected Boundary Preservation

- The existing protected `/guardian` routes, controller, views, and payment behavior were left untouched.
- No student names, invoice numbers, receipts, payment totals, or payment-entry controls are rendered on the new guardian informational pages.
- The external admission CTA does not appear on auth pages or the protected guardian portal.
- Prompt-35 donor route entry, donor no-portal behavior, donor history provenance, and donor receipt boundaries remain unchanged.

### Same-Phase Carry-Forward Correction

- Full-suite validation surfaced one stale prompt-30 test expectation that still assumed the pre-prompt-36 fail-closed dashboard behavior for guardian-role informational users.
- Updated `tests/Feature/Phase12/AccountStateReadPathAdaptationTest.php` so it now expects the approved prompt-36 redirect to `guardian.info.dashboard` instead of `403`.
- This was a same-phase test carry-forward correction, not a product blocker.

### Compatibility Notes For Existing Files

- `routes/web.php`
  - impact class: `critical`
  - why touched: add the new additive guardian informational route bucket without changing protected guardian route names
  - preserved behavior: existing `guardian.*`, `donor.*`, `payments.*`, `management.*`, and `dashboard` route names remain unchanged
  - intentionally not changed: protected guardian route definitions, donor routes, payment routes, management routes
  - regression checks: `php.exe artisan route:list --path=guardian/info`, `php.exe artisan route:list --path=guardian`, prompt-36 guardian tests, phase-2 guardian tests, phase-5 payment tests
  - rollback note: reverting this file removes only the new guardian informational route bucket and related redirect wiring
- `app/Http/Controllers/Auth/AuthenticatedSessionController.php`
  - impact class: `high`
  - why touched: send guardian-only informational accounts to the new guardian informational home without disturbing donor precedence
  - preserved behavior: donor-only accounts still prefer `/donor`; protected guardian users still use the existing `/dashboard` -> `/guardian` path; management behavior stays unchanged
  - intentionally not changed: multi-role chooser logic, registration flow, payment routing
  - regression checks: prompt-35 donor tests, prompt-36 guardian tests, prompt-31 / prompt-32 tests
  - rollback note: reverting this file restores the pre-prompt-36 default login redirect
- `app/Http/Controllers/Auth/EmailVerificationPromptController.php`
  - impact class: `medium`
  - why touched: already-verified guardian-context users now resolve to guardian informational home when that is their approved surface
  - preserved behavior: unverified users still see the existing verification notice
  - intentionally not changed: resend rules, protected guardian gating, auth form surfaces
  - regression checks: prompt-32 verification tests, prompt-36 guardian tests
  - rollback note: reverting this file restores the earlier already-verified redirect behavior
- `app/Http/Controllers/Auth/VerifyEmailController.php`
  - impact class: `high`
  - why touched: keep guardian informational post-verification landing aligned with the new guardian informational portal
  - preserved behavior: email verification still changes email trust only and does not grant protected guardian access by itself
  - intentionally not changed: donor redirect rules, approval semantics, phone verification rules, protected guardian route group
  - regression checks: prompt-32 verification tests, prompt-36 guardian tests, phase-2 guardian tests
  - rollback note: reverting this file restores the earlier post-verification redirect behavior only
- `app/Http/Controllers/Auth/EmailVerificationNotificationController.php`
  - impact class: `medium`
  - why touched: keep already-verified guardian-context users aligned with the guardian informational redirect when resend is revisited
  - preserved behavior: resend cooldowns, hourly caps, and audit logging remain intact
  - intentionally not changed: email sending rules, auth forms, protected guardian gating
  - regression checks: prompt-32 verification tests, prompt-36 guardian tests
  - rollback note: reverting this file restores the earlier already-verified redirect behavior
- `app/Http/Middleware/RedirectPortalUsersFromLegacyDashboard.php`
  - impact class: `high`
  - why touched: redirect verified guardian informational users away from `/dashboard` into the new guardian informational route space while preserving protected guardian priority
  - preserved behavior: management bypass, existing protected guardian redirect, and donor precedence remain intact
  - intentionally not changed: protected guardian ownership rules, management surface gating, multi-role chooser behavior
  - regression checks: prompt-36 guardian tests, phase-2 guardian tests, prompt-35 donor tests, phase-5 payment tests
  - rollback note: reverting this file removes only the guardian informational dashboard redirect

## Durable Artifact Promotion

- Promoted approved decisions to `docs/codex/04-decisions/approved/prompt-36-guardian-informational-portal.md`
- Promoted the reusable guardian informational workflow artifact to `docs/codex/05-artifacts/workflow/prompt-36-guardian-informational-portal.md`

## Validation

- Git/runtime context:
  - `git status --short`
    - result: `working tree already dirty`
    - summary: the repository already contained broader in-progress prompt-series changes; no attempt was made to revert them
  - `git branch --show-current`
    - result: `pass`
    - summary: `codex/2026-03-08-phase-1-foundation-safety`
  - `git rev-parse HEAD`
    - result: `pass`
    - summary: `a3f048c4d18312a854d99f0470d851cafc6b3cab`
- Syntax / route sanity:
  - `php.exe -l app/Http/Controllers/Guardian/GuardianInformationalPortalController.php`
  - `php.exe -l app/Services/GuardianPortal/GuardianInformationalPortalData.php`
  - `php.exe -l app/Services/GuardianPortal/GuardianInformationalAccessState.php`
  - `php.exe -l app/Http/Controllers/Auth/AuthenticatedSessionController.php`
  - `php.exe -l app/Http/Controllers/Auth/EmailVerificationPromptController.php`
  - `php.exe -l app/Http/Controllers/Auth/VerifyEmailController.php`
  - `php.exe -l app/Http/Controllers/Auth/EmailVerificationNotificationController.php`
  - `php.exe -l app/Http/Middleware/RedirectPortalUsersFromLegacyDashboard.php`
  - `php.exe -l routes/guardian-info.php`
  - `php.exe -l tests/Feature/Phase12/GuardianInformationalPortalTest.php`
  - `php.exe -l tests/Feature/Phase12/AccountStateReadPathAdaptationTest.php`
  - result: `pass`
- Route validation:
  - `php.exe artisan route:list --path=guardian/info`
    - result: `pass`
    - summary: `3 guardian informational routes registered`
  - `php.exe artisan route:list --path=guardian`
    - result: `pass`
    - summary: existing protected guardian routes remain registered alongside the new guardian informational routes
- Prompt-36 focused validation:
  - `php.exe artisan test --env=testing tests/Feature/Phase12/GuardianInformationalPortalTest.php`
    - result: `pass`
    - summary: `3 passed (30 assertions)`
- Guardian carry-forward validation:
  - `php.exe artisan test --env=testing tests/Feature/Phase12/AccountStateReadPathAdaptationTest.php tests/Feature/Phase12/GuardianInformationalPortalTest.php`
    - result: `pass`
    - summary: `6 passed (47 assertions)`
- Cross-prompt regression slice:
  - `php.exe artisan test --env=testing tests/Feature/Phase2/GuardianPortalTest.php tests/Feature/Phase5/PaymentIntegrationTest.php tests/Feature/Phase12/GuardianInformationalPortalTest.php tests/Feature/Phase12/DonorAuthAndPortalAccessTest.php tests/Feature/Phase12/OpenRegistrationFoundationTest.php tests/Feature/Phase12/EmailPhoneVerificationFoundationTest.php`
    - result: `pass`
    - summary: `25 passed (249 assertions)`
- Broad regression gate:
  - `php.exe artisan test --env=testing`
    - result: `expected baseline failure set only`
    - summary: `14 failed, 60 passed`
    - classification: only the long-standing auth/profile baseline failures remain; prompt-36 introduced no new regression after the same-phase stale-test update

## Contradiction / Blocker Pass

- No contradiction was found with prompt-13's guardian informational/protected split, prompt-14's admission boundary, prompt-15's GI1/GI2 rollout order, prompt-18's separate guardian informational route-space requirement, prompt-19's external destination rules, prompt-30's account-state model, prompt-31's guardian foundation model, prompt-32's verification separation, or prompt-35's donor access changes.
- Prompt-36 preserved prompt-35's donor context and donor portal eligibility separation, including the donor `donation_record` history bridge.
- Prompt-36 kept the current protected `/guardian` portal, guardian invoice/payment flows, and route names intact instead of reopening prompt-37 protected gating early.
- Prompt-36 kept multi-role chooser and switching behavior deferred to prompt-39.
- The full suite returned to the documented 14 baseline auth/profile failures after the stale prompt-30 dashboard expectation was carried forward.
- Runtime Git preflight now confirms the workspace is not clean, but this was an existing repository state rather than a prompt-36 product blocker.
- No product blocker was found.
- No correction pass is required.

## Risks

- Prompt-41 still needs to centralize the existing public admission CTA onto the same canonical config/helper path; prompt-36 intentionally scoped the config-backed admission CTA to guardian informational surfaces only.
- The repository working tree was already dirty before prompt-36 validation, so commit-level isolation is still an operational risk outside the application behavior itself.
- Dedicated guardian-informational and guardian-protected eligibility middleware are still later route-hardening work; prompt-36 uses additive redirect/read-path logic rather than the final middleware cutover.

## Next Safe Slice

- `docs/codex/01-prompts/prompt-37-guardian-protected-portal-gating.md`
