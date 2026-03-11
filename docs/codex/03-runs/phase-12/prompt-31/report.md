# Report

## Scope Lock Before Coding

### Exact Approved Slice

- Prompt-31 was executed as open-registration foundation only.
- The approved implementation slice was limited to:
  - public/general registration foundation
  - donor self-registration foundation
  - guardian self-registration foundation
  - the minimum route, controller, request, model, and read-path adjustments needed to support those foundations
- Prompt-31 had to preserve prompt-28's shared UI foundation and prompt-29 / prompt-30's additive account-state model and read logic.

### Approved Registration Model Restated

- One shared registration backend remains the source of truth.
- Optional donor and guardian entry pages only preselect intent; they do not create separate auth systems.
- Registration creates a base account plus optional donor or guardian foundation data only.
- Registration alone must not auto-grant donor portal access, guardian linkage, protected student data, donor settlement behavior, or payment-domain behavior.
- The neutral authenticated landing for newly registered users is a safe onboarding space rather than immediate protected portal access.

### What Was Explicitly Deferred

- no guest donation flow
- no Google sign-in
- no donor payable redesign
- no donor or guardian portal-role adoption during registration
- no guardian linkage or protected guardian rollout
- no broader verified-middleware removal beyond the already approved prompt-30 scope

## Implementation Result

Prompt-31 completed inside the approved open-registration foundation slice.

### Files Changed

- `app/Http/Controllers/Auth/RegisteredUserController.php`
- `app/Http/Middleware/EnsureManagementSurfaceAccess.php`
- `app/Http/Middleware/RedirectPortalUsersFromLegacyDashboard.php`
- `app/Http/Requests/Auth/OpenRegistrationRequest.php`
- `app/Models/User.php`
- `resources/views/auth/onboarding.blade.php`
- `resources/views/auth/register.blade.php`
- `resources/views/layouts/guest.blade.php`
- `routes/auth.php`
- `tests/Feature/Phase12/OpenRegistrationFoundationTest.php`

### Registration Behaviors Implemented

- Added a dedicated `OpenRegistrationRequest` that validates one unified registration payload with `public`, `donor`, or `guardian` intent.
- Kept one shared registration POST endpoint while adding branded GET entry pages for:
  - `/register`
  - `/register/donor`
  - `/register/guardian`
- Open registration now creates a base account with:
  - `email_verified_at = null`
  - `approval_status = approval_not_required`
  - `account_status = active`
- Added the compatibility role `registered_user` and assign it during self-registration so these accounts stay distinct from donor, guardian, and management roles.
- Donor self-registration now creates a linked donor foundation record with portal access still disabled and the donor still inactive.
- Guardian self-registration now creates a linked guardian foundation record with no portal enablement and no protected linkage.
- Added the neutral authenticated landing route `/registration/onboarding` for `registered_user` accounts and rendered it through the shared prompt-28 guest-shell primitives.
- Preserved prompt-30 account-state read logic while ensuring `registered_user` accounts are denied from legacy management surfaces.
- Preserved the existing blanket `verified` behavior on legacy verified routes: newly registered users can land on `/registration/onboarding`, but `/dashboard` still goes through the verification notice until later prompts change that behavior explicitly.

### Prompt-31 View-Layer Correction

- Initial validation exposed a Blade component naming mismatch in the new auth views.
- The prompt-28 shared UI primitives are anonymous Blade components under `resources/views/components/ui/*`, so the new views were corrected to use `<x-ui.card>` and `<x-ui.alert>` instead of undeclared dash aliases.
- This was resolved within prompt-31 and did not require a correction pass.

### Intentionally Deferred Items

- guest donation entry and finalization remain deferred to later prompts
- Google sign-in remains deferred
- donor payable redesign remains deferred
- donor and guardian route-driving roles are still not auto-assigned during registration
- guardian linkage, protected student access, invoice access, and receipt/payment access remain deferred
- broader donor/guardian no-portal rollout and final eligibility middleware changes remain deferred

## Durable Artifact Promotion

- promoted approved decisions to `docs/codex/04-decisions/approved/prompt-31-open-registration-foundation.md`
- promoted the reusable registration-flow artifact to `docs/codex/05-artifacts/workflow/prompt-31-open-registration-foundation.md`

## Validation

- `php.exe artisan route:list --path=register`
  - result: `pass`
  - summary: `4 registration routes registered: register, register.donor, register.guardian, and POST register`
- `php.exe artisan route:list --path=registration`
  - result: `pass`
  - summary: `registration.onboarding route registered`
- `php.exe artisan test --env=testing tests/Feature/Phase1/FoundationSchemaTest.php tests/Feature/Phase1/PortalRoleAccessTest.php tests/Feature/Phase2/GuardianPortalTest.php tests/Feature/Phase3/DonorPortalTest.php tests/Feature/Phase12/AccountStateSchemaFoundationTest.php tests/Feature/Phase12/AccountStateReadPathAdaptationTest.php tests/Feature/Phase12/OpenRegistrationFoundationTest.php tests/Feature/Auth/EmailVerificationTest.php`
  - result: `pass`
  - summary: `19 passed (138 assertions)`
- `php.exe artisan test --env=testing`
  - result: `expected baseline failure set only`
  - summary: `14 failed, 39 passed`
  - classification: `failure list still matches the existing auth/profile baseline manifest exactly; the added pass count comes from the prompt-29, prompt-30, and prompt-31 phase-12 tests`
  - unexpected regressions: `none`

## Contradiction / Blocker Pass

- No contradiction was found with prompt-28's shared UI foundation, prompt-29's schema-only account-state additions, prompt-30's read-path adaptation, or prompt-06's approved open-registration model.
- Prompt-31 did not pull forward guardian linkage adoption, donor settlement tables, payment-domain redesign, guest donation entry, or Google sign-in.
- Prompt-31 preserved the still-approved blanket `verified` behavior on legacy verified routes rather than widening access early.
- The PHP 8.4 `mbstring` issue remained an environment/runtime quirk only; validation used the working local PHP 8.2 runtime.
- No product blocker was found.
- No correction pass is required.

## Risks

- `registered_user` is currently created lazily at registration time, so later rollout prompts may still want a seed or backfill path if admin tooling starts depending on the role being present ahead of first use.
- Newly registered accounts still meet the existing verification notice on legacy verified routes; prompt-32 and later auth prompts must decide how far the neutral onboarding experience should extend before email and phone verification complete.
- Donor and guardian self-registration now creates draft profile rows on the shared identity, so later linking prompts must remain duplicate-safe and must not infer protected eligibility from those records alone.

## Next Safe Slice

- `docs/codex/01-prompts/prompt-32-email-phone-verification-foundation.md`
