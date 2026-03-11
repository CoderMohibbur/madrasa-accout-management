# Report

## Scope Lock Before Coding

### Exact Approved Slice

- Prompt-37 is limited to guardian protected portal gating only.
- The approved implementation slice was limited to:
  - protected guardian route-edge gating updates
  - linked and authorized guardian-only enforcement for protected routes
  - protected guardian payment-entry continuity inside the same boundary
- Prompt-37 had to preserve:
  - the additive guardian informational `/guardian/info*` surface from prompt-36
  - prompt-35 donor access and donor history behavior
  - prompt-14's admission boundary and prompt-36's informational-only CTA placement
  - the current protected `/guardian` route names and protected data ownership rules
  - prompt-39's later multi-role chooser/switching work

### Linkage And Portal-Eligibility Rules Restated

- Guardian role membership remains guardian-domain potential only; it is not the final protected portal gate.
- Guardian protected entry must now derive from:
  - accessible account state
  - verified email for the protected boundary
  - guardian profile present, active, portal-enabled, and not deleted
  - explicit guardian linkage through at least one linked student
- Guardian informational access remains broader than guardian protected access:
  - unverified guardians
  - unlinked guardians
  - role-only guardians
  may still use safe informational surfaces only

### Protected Ownership Rules That Had To Stay Strong

- Protected student views remain link-controlled through guardian-student linkage.
- Protected invoice access remains constrained by student linkage plus `guardian_id` compatibility when present.
- Protected payment initiation remains limited to authorized linked guardian invoice payables with positive balance.
- Receipt and payment detail access remain narrower than route membership and stay tied to authorized invoice ownership.
- Prompt-37 could not widen donor routes, donor history, public admission, or protected management surfaces.

## Implementation Result

Prompt-37 completed inside the approved guardian protected portal gating scope.

### Files Changed

- `app/Services/GuardianPortal/GuardianProtectedAccessState.php`
- `app/Services/GuardianPortal/GuardianPortalData.php`
- `app/Services/GuardianPortal/GuardianInformationalPortalData.php`
- `app/Http/Middleware/EnsureGuardianProtectedAccess.php`
- `app/Http/Middleware/RedirectPortalUsersFromLegacyDashboard.php`
- `app/Http/Middleware/EnsureManagementSurfaceAccess.php`
- `app/Services/Payments/StudentFeeInvoicePayableResolver.php`
- `app/Http/Controllers/Payments/ManualBankPaymentController.php`
- `app/Services/Payments/PaymentWorkflowService.php`
- `bootstrap/app.php`
- `routes/web.php`
- `routes/payments.php`
- `resources/views/guardian/info/dashboard.blade.php`
- `tests/Feature/Phase2/GuardianPortalTest.php`
- `tests/Feature/Phase5/PaymentIntegrationTest.php`
- `tests/Feature/Phase12/GuardianInformationalPortalTest.php`
- `tests/Feature/Phase12/GuardianProtectedPortalGatingTest.php`

### Protected Guardian Gating Implemented

- Added a dedicated guardian protected access-state resolver and `guardian.protected` middleware alias.
- Re-gated the live protected `/guardian` route group from:
  - `auth + verified + role:guardian`
  to:
  - `auth + guardian.protected`
- Guardian protected eligibility now resolves from:
  - shared accessible account state
  - verified email for the protected boundary
  - guardian profile lifecycle flags
  - actual guardian-student linkage
- Guardian role membership no longer acts as the protected route gate.
- Verified linked guardians with a protected-eligible guardian profile now keep the protected `/guardian` landing even when no raw guardian role row exists.
- Role-only, unlinked, and unverified guardian-context accounts remain fail-closed on protected routes.

### Shared Redirect And Legacy-Surface Hardening

- `RedirectPortalUsersFromLegacyDashboard` now uses derived protected guardian eligibility instead of the old raw guardian-role check.
- This keeps the prompt-36 informational split intact:
  - protected-eligible guardians go to `/guardian`
  - unlinked or unverified guardian-context users can still land on `/guardian/info`
- `EnsureManagementSurfaceAccess` now blocks guardian-profile users from falling through to legacy management pages solely because they lack a raw guardian role row.

### Protected Payment-Entry Continuity

- Guardian payment initiation routes now use the same protected guardian route-edge middleware as `/guardian`.
- `StudentFeeInvoicePayableResolver` now requires protected guardian eligibility plus invoice ownership compatibility through guardian linkage.
- Manual-bank payment detail and shurjoPay/payment detail authorization now reuse protected invoice authorization instead of a looser direct `guardian.user_id` shortcut.
- Payment initiation remains fail-closed for other protected guardians trying to use an invoice they do not own.

### Prompt-36 Informational Preservation

- The additive `/guardian/info*` routes remain unchanged and non-sensitive.
- Informational state messaging now explicitly covers:
  - protected routes waiting on email verification
  - portal-enabled guardian profiles that are still unlinked
- Prompt-37 did not move the external admission CTA onto auth pages or protected guardian pages.

### Compatibility Notes For Existing Files

- `routes/web.php`
  - impact class: `critical`
  - why touched: replace coarse protected guardian route-edge middleware with the dedicated protected guardian middleware
  - preserved behavior: `guardian.*`, `guardian.info.*`, `donor.*`, `payments.*`, and `management.*` route names remain unchanged
  - intentionally not changed: donor routes, public admission routes, multi-role route naming
  - rollback note: reverting this file restores the old protected guardian middleware coupling only
- `routes/payments.php`
  - impact class: `critical`
  - why touched: keep guardian payment initiation aligned with the new protected guardian route gate
  - preserved behavior: payment return pages, IPN route, and management manual-bank review routes remain unchanged
  - intentionally not changed: donor payment routes, guest donation status routes
  - rollback note: reverting this file restores the old guardian payment-entry middleware only
- `app/Services/GuardianPortal/GuardianPortalData.php`
  - impact class: `critical`
  - why touched: centralize protected guardian eligibility derivation and shared protected-home checks
  - preserved behavior: existing linked student, invoice, and payment queries remain ownership-scoped
  - intentionally not changed: protected view rendering, donor logic, multi-role chooser behavior
  - rollback note: reverting this file restores the pre-prompt-37 profile-flag-only guardian gate
- `app/Services/Payments/StudentFeeInvoicePayableResolver.php`
  - impact class: `high`
  - why touched: ensure payment initiation respects the same protected guardian linkage boundary as the portal itself
  - preserved behavior: positive-balance enforcement and management override stay intact
  - intentionally not changed: donor checkout settlement, provider integration, posting semantics
  - rollback note: reverting this file restores the older direct `invoice.guardian.user_id` payment check
- `app/Http/Middleware/EnsureManagementSurfaceAccess.php`
  - impact class: `high`
  - why touched: close the legacy management-surface fallback for guardian-profile users without raw guardian roles
  - preserved behavior: management access and registered-user denial remain unchanged
  - intentionally not changed: donor management-surface rules, shared-home chooser behavior
  - rollback note: reverting this file restores the old role-only management-surface block

## Durable Artifact Promotion

- Promoted approved decisions to `docs/codex/04-decisions/approved/prompt-37-guardian-protected-portal-gating.md`
- Promoted the reusable guardian protected gating workflow artifact to `docs/codex/05-artifacts/workflow/prompt-37-guardian-protected-portal-gating.md`

## Validation

- Git/runtime context:
  - `git status --short`
    - result: `working tree already dirty`
    - summary: the repository already contained broader in-progress prompt-series changes; prompt-37 changes were made on top of that existing state
  - `git branch --show-current`
    - result: `pass`
    - summary: `codex/2026-03-08-phase-1-foundation-safety`
  - `git rev-parse HEAD`
    - result: `pass`
    - summary: `a3f048c4d18312a854d99f0470d851cafc6b3cab`
- Route validation:
  - `php.exe artisan route:list --path=guardian`
    - result: `pass`
    - summary: `9 guardian routes registered, including the protected /guardian routes and additive /guardian/info routes under unchanged names`
  - `php.exe artisan route:list --path=payments`
    - result: `pass`
    - summary: `11 payment routes registered, including guardian payment initiation, manual-bank detail, provider returns, IPN, and management review routes`
- Prompt-37 focused validation:
  - `php.exe artisan test --env=testing tests/Feature/Phase12/GuardianProtectedPortalGatingTest.php`
    - result: `pass`
    - summary: `3 passed (16 assertions)`
- Guardian and payment regression slice:
  - `php.exe artisan test --env=testing tests/Feature/Phase2/GuardianPortalTest.php tests/Feature/Phase5/PaymentIntegrationTest.php tests/Feature/Phase12/GuardianInformationalPortalTest.php tests/Feature/Phase12/GuardianProtectedPortalGatingTest.php tests/Feature/Phase12/AccountStateReadPathAdaptationTest.php tests/Feature/Phase12/DonorAuthAndPortalAccessTest.php`
    - result: `pass`
    - summary: `21 passed (168 assertions)`

## Contradiction / Blocker Pass

- No contradiction was found with prompt-13's guardian informational/protected split, prompt-15's `GP1` through `GP3` protected rollout intent, prompt-18's dedicated guardian-protected eligibility middleware requirement, prompt-25's `GPROT-*` and `POL-*` expectations, prompt-30's account-state rules, prompt-32's verification separation, prompt-35's donor access rules, or prompt-36's informational route preservation.
- Prompt-37 preserved the prompt-36 guardian informational route space as additive and non-sensitive.
- Prompt-37 preserved prompt-35 donor behavior and did not reopen donor history, donor receipt provenance, or donor checkout work.
- Prompt-37 did not expand admission handling beyond the approved informational surfaces.
- The protected guardian route edge is no longer granted from role membership alone, informational access alone, or one ownership signal alone.
- No product blocker was found.
- No correction pass is required.

## Risks

- Payment return/detail routes still use the current auth plus authorization shape; later prompt-40 route finalization may still consolidate that boundary further.
- Multi-role chooser and explicit context switching remain deferred to prompt-39, so prompt-37 keeps the existing single-context redirect posture.
- The repository working tree remains broadly dirty, which is an operational isolation risk outside prompt-37's application behavior.

## Next Safe Slice

- `docs/codex/01-prompts/prompt-38-google-signin-foundation.md`
