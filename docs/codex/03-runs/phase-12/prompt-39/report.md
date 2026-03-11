# Report

## Scope Lock Before Coding

### Exact Approved Slice

- Prompt-39 is limited to `MR1` through `MR3` only.
- The approved slice is only:
  - one shared eligible-context resolver on top of the existing donor and guardian access services
  - a neutral `/dashboard` chooser for users already eligible for more than one donor/guardian context
  - additive donor/guardian switch affordances inside the existing portal shells
  - aligned post-auth routing for password login, Google callback, email-verification prompt, resend, and completion
- Prompt-39 had to preserve:
  - prompt-38's external identity foundation, Google redirect/callback flow, authenticated link support, and first-time guardian deferral
  - prompt-35 donor no-portal vs portal behavior and donor history scope
  - prompt-36 guardian informational route space
  - prompt-37 guardian protected linkage and protected-access gating
  - unchanged route names and deferred prompt-40 route/middleware finalization

### Multi-Role Isolation Rules Restated

- One shared `users` account can reach donor and guardian contexts, but those contexts remain separate derived surfaces.
- Shared home behavior must derive from eligible contexts, not raw role order.
- One eligible context redirects directly.
- Multiple eligible contexts show a neutral chooser.
- Explicit switching links only to already-eligible contexts and never grants access by itself.
- Management-compatible dashboard behavior stays outside the chooser path.

### Data-Scope Mixing That Had To Stay Disabled

- no mixed donor-plus-guardian dashboard data on `/dashboard`
- no donor receipt/history exposure inside guardian surfaces
- no guardian student, invoice, receipt, or payment data inside donor surfaces
- no switch or chooser action that bypasses protected guardian linkage rules
- no Google/email-verification shortcut that turns email trust into donor eligibility or guardian protected access
- no broad route rename or final middleware sweep beyond the `/dashboard` behavior needed for `MR1` through `MR3`

## Implementation Result

Prompt-39 completed inside the approved multi-role home/switching scope.

### Files Changed

- `app/Services/MultiRole/MultiRoleContextResolver.php`
- `app/Http/Controllers/Auth/AuthenticatedSessionController.php`
- `app/Http/Controllers/Auth/EmailVerificationNotificationController.php`
- `app/Http/Controllers/Auth/EmailVerificationPromptController.php`
- `app/Http/Controllers/Auth/GoogleAuthController.php`
- `app/Http/Controllers/Auth/VerifyEmailController.php`
- `app/Http/Controllers/DashboardController.php`
- `app/Http/Middleware/RedirectPortalUsersFromLegacyDashboard.php`
- `routes/web.php`
- `resources/views/components/portal-shell.blade.php`
- `resources/views/dashboard/multi-role-home.blade.php`
- `tests/Feature/Phase12/MultiRoleHomeAndSwitchingTest.php`

### Multi-Role Behavior Implemented

- Added `MultiRoleContextResolver` so prompt-35 donor, prompt-36 guardian informational, and prompt-37 guardian protected access services now feed one eligibility-based chooser model instead of raw guardian-first or donor-first branching.
- Updated password login, Google callback, and the email-verification prompt/resend/completion flows so true multi-context users land on `/dashboard`, while single-context users still go straight to their only eligible donor or guardian home.
- Updated `portal.home` plus `DashboardController` so `/dashboard` now:
  - redirects directly when exactly one donor/guardian context is eligible
  - renders a neutral chooser when more than one donor/guardian context is eligible
  - keeps verified `registered_user` zero-context accounts on the existing `registration.onboarding` fallback
  - keeps inaccessible accounts fail-closed and management-compatible accounts on the existing management dashboard path
- Added a neutral shared-home UI that shows only context status and routing choices; it does not query or aggregate donor records, guardian student records, invoices, receipts, or payments.
- Added additive switch controls to the shared portal shell so multi-context users can move between donor and guardian homes explicitly without changing route names or adding a stored preference layer.
- Added focused prompt-39 feature coverage for chooser rendering, switcher rendering, and Google multi-role landing behavior.

### Scope-Isolation Protections Preserved

- Donor context still resolves through prompt-35 donor-domain access rules; donor-context but non-portal accounts still land on the donor no-portal surface.
- Guardian informational vs protected routing still resolves through prompt-36 and prompt-37 access services rather than blanket role checks.
- Protected guardian routes still require accessible account state, verified email, eligible guardian profile state, and linked-student ownership.
- Google sign-in still uses prompt-38's `external_identities` linkage plus verified `users.email` fail-closed rules; prompt-39 only changes where a valid multi-context account lands after sign-in.
- Existing route names remain unchanged: `dashboard`, `donor.*`, `guardian.*`, `guardian.info.*`, and `management.*`.

### Compatibility Notes For Existing Boundaries

- `app/Services/MultiRole/MultiRoleContextResolver.php`
  - impact class: `critical`
  - why touched: centralize eligible donor vs guardian informational vs guardian protected context resolution for chooser and switching behavior
  - preserved behavior: donor portal eligibility, guardian informational access, and guardian protected linkage rules still come from their existing services
  - intentionally not changed: management route names, donor payable logic, guardian payment ownership logic, or any account-merge behavior
- `app/Http/Middleware/RedirectPortalUsersFromLegacyDashboard.php`
  - impact class: `critical`
  - why touched: stop raw single-context role ordering from forcing multi-role users into one portal and allow the chooser to render when more than one context is independently eligible
  - preserved behavior: one-context users still get direct donor or guardian landing behavior
  - intentionally not changed: donor routes, guardian routes, or prompt-40's later full route-policy cleanup
- `app/Http/Controllers/DashboardController.php`
  - impact class: `critical`
  - why touched: keep the `dashboard` route name stable while splitting neutral chooser handling from the existing management dashboard rendering
  - preserved behavior: verified management-compatible users still use the management dashboard; verified public shared-account users still fall back to `registration.onboarding`
  - intentionally not changed: management metrics, transaction calculations, or shared navigation route names
- `resources/views/components/portal-shell.blade.php`
  - impact class: `medium`
  - why touched: expose additive switch controls only when a signed-in user already has more than one eligible donor/guardian context
  - preserved behavior: existing donor and guardian portal content stays on its current routes and shells
  - intentionally not changed: donor dashboard data, guardian dashboard data, or management navigation

## Durable Artifact Promotion

- Promoted approved decisions to `docs/codex/04-decisions/approved/prompt-39-multi-role-home-and-switching.md`
- Promoted the reusable multi-role workflow artifact to `docs/codex/05-artifacts/workflow/prompt-39-multi-role-home-and-switching.md`

## Validation

- Focused phase-12 regression pack:
  - `"/mnt/c/laragon/bin/php/php-8.2.9-Win32-vs16-x64/php.exe" artisan test --env=testing tests/Feature/Phase12/MultiRoleHomeAndSwitchingTest.php tests/Feature/Phase12/GoogleSignInFoundationTest.php tests/Feature/Phase12/DonorAuthAndPortalAccessTest.php tests/Feature/Phase12/GuardianInformationalPortalTest.php tests/Feature/Phase12/GuardianProtectedPortalGatingTest.php tests/Feature/Phase12/AccountStateReadPathAdaptationTest.php tests/Feature/Phase12/OpenRegistrationFoundationTest.php tests/Feature/Phase12/EmailPhoneVerificationFoundationTest.php`
    - result: `pass`
    - summary: `31 passed (310 assertions)`

## Contradiction / Blocker Pass

- No contradiction was found with prompt-16's multi-role model, prompt-17's `MR1` through `MR4` sequencing, prompt-18's route-policy direction, prompt-25's `MULTI-*` / `ROUTE-*` / `GOOG-*` gates, prompt-35 donor access rules, prompt-36 guardian informational rules, prompt-37 guardian protected gating, or prompt-38 Google identity rules.
- The repository still contains an older `docs/codex-autopilot` six-phase handoff/state package that reports completion of a separate earlier program. Because the active `docs/codex` prompt chain, prompt-38 run outputs, and your explicit prompt-39 instruction were internally consistent, this was treated as legacy documentation drift rather than a blocker for the numbered prompt workflow.
- No product blocker was found.
- No correction pass is required.

## Risks

- `MR4` final route/middleware/policy cleanup is still deferred to prompt-40, so prompt-39 intentionally avoids a broader route-group rewrite beyond chooser-aware `/dashboard` handling.
- Shared home still does not persist a remembered donor/guardian preference; switching remains explicit additive links only.
- Verified shared-account users with no donor or guardian context still use the existing onboarding fallback; broader neutral no-context polish remains later work if needed.

## Next Safe Slice

- `docs/codex/01-prompts/prompt-40-route-middleware-policy-finalization.md`
