# Report

## Scope Lock Before Coding

### Exact Approved Slice

- Prompt-35 is limited to donor slices `A2` -> `O1` -> `H1`.
- The slice is only:
  - donor login and no-portal behavior separation from blanket `verified` + raw `role:donor` assumptions
  - donor portal gating derived from donor-domain eligibility instead of role membership alone
  - donor portal read-path bridging for new `donation_record` and donor-specific receipt history
- Prompt-35 must preserve prompt-34's donor payable foundation, the `/donate` checkout/status flow, and the separation between donor settlement and guardian invoice settlement.

### Donor Permission Matrix Rules Restated

- Donor payment ability is separate from donor portal eligibility.
- Donor login and donor donation do not require universal email or phone verification.
- Guest donation remains allowed without prior registration.
- Transaction-specific payment-status and receipt access remain narrower than full donor portal history.
- Verification, approval, and portal eligibility remain separate axes.

### Guest-Donation Behavior That Had To Stay Intact

- Prompt-34's `donation_intent -> payment -> donation_record -> receipt` flow remains the donor settlement truth.
- Guest and identified donors still use the prompt-34 `/donate` flow and narrow status/receipt access.
- Guest checkout still creates no account and no donor profile.
- Identified checkout still does not auto-grant donor role, donor profile linkage, or donor portal eligibility.

## Implementation Result

Prompt-35 completed inside the approved donor auth, portal access, and history read-path scope.

### Files Changed

- `app/Services/DonorPortal/DonorAccessState.php`
- `app/Services/DonorPortal/DonorPortalData.php`
- `app/Http/Controllers/Donor/DonorPortalController.php`
- `app/Http/Middleware/RedirectPortalUsersFromLegacyDashboard.php`
- `app/Http/Controllers/Auth/AuthenticatedSessionController.php`
- `app/Http/Controllers/Auth/EmailVerificationPromptController.php`
- `app/Http/Controllers/Auth/VerifyEmailController.php`
- `app/Http/Controllers/Auth/EmailVerificationNotificationController.php`
- `resources/views/components/donor-layout.blade.php`
- `resources/views/donor/no-portal.blade.php`
- `resources/views/donor/dashboard.blade.php`
- `resources/views/donor/donations/index.blade.php`
- `resources/views/donor/receipts/index.blade.php`
- `routes/web.php`
- `tests/Feature/Phase12/DonorAuthAndPortalAccessTest.php`

### Donor Auth Behavior Implemented

- Added a dedicated donor access-state resolver so donor route entry now derives from:
  - donor profile presence
  - donor portal eligibility flags
  - existing donor role rows
  - identified donor payment history
- Added a donor no-portal screen for:
  - donor-intent / donor-foundation accounts
  - identified donors whose account-linked donation history exists but whose donor portal eligibility does not
  - legacy donor-role rows without an eligible donor portal profile
- Donor-only accounts now default to `/donor` after login and after email-verification completion checks when donor context exists and no guardian/management context is being inferred early.
- Prompt-31 donor foundation accounts can now authenticate into a safe donor-specific no-portal surface without requiring blanket `verified` middleware.

### Donor Portal Gating Implemented

- The `/donor` route group no longer depends on `verified` + `role:donor`.
- Donor portal eligibility is now derived from donor-domain flags:
  - matching donor profile exists
  - `portal_enabled = true`
  - `isActived = true`
  - `isDeleted = false`
- `/donor` now behaves in two safe modes:
  - portal-eligible donors get the read-only donor dashboard
  - donor-context but non-eligible accounts get the donor no-portal state screen
- `/donor/donations` and `/donor/receipts` remain portal-only read paths; non-eligible donor-context accounts are redirected back to `/donor` with an explanatory message instead of being treated as portal users.
- Existing guardian route groups, guardian middleware, and management route names remain unchanged.

### Donor History And Receipt Bridge Implemented

- Portal-eligible donor history now combines:
  - legacy donor `transactions` donation rows
  - prompt-34 `donation_records`
- The portal keeps provenance explicit with source labels instead of converting or silently merging legacy rows into the new donor-domain history.
- New donor-domain rows are surfaced by `user_id` or `donor_id`, so previously identified donations can appear later when the same account becomes portal-eligible.
- Donor receipt history now includes:
  - legacy donor receipts tied to donor-scoped payments
  - prompt-34 receipts tied to `DonationIntent`
- Receipt scoping stays donor-specific by limiting the donor portal to payments with `payable_type = DonationIntent::class` or the existing legacy null-payable donor pattern; no guardian invoice receipt bridge was pulled forward.
- Anonymous-display donor preference remains visible in the donor portal as a display label without weakening internal traceability.

### Guest-Donation Compatibility Notes

- The public `/donate` entry, checkout, return, and narrow status routes from prompt-34 were not redesigned.
- Guest transaction-specific access still depends on `public_reference` plus access key.
- Identified transaction-specific status access still remains narrower than portal history access.
- Prompt-35 did not change donor settlement, receipt issuance, or guardian invoice finalization behavior.

### Compatibility Notes For Existing Files

- `routes/web.php`
  - impact class: `critical`
  - why touched: remove blanket donor `verified` + raw role coupling and keep donor access derived inside the donor slice
  - preserved behavior: existing `dashboard`, `guardian.*`, `management.*`, `payments.*`, and `donor.*` route names remain unchanged
  - intentionally not changed: guardian route groups, management routing, route-name compatibility, public `/donate` routes
  - regression checks: `php.exe artisan route:list --path=donor`, prompt-35 donor tests, prompt-34 donor payable tests, phase-3 donor portal tests
  - rollback note: reverting this file restores the old donor route middleware coupling only
- `app/Http/Controllers/Auth/AuthenticatedSessionController.php`
  - impact class: `high`
  - why touched: send donor-only accounts to the new donor home/no-portal state instead of the old verified-first dashboard path
  - preserved behavior: shared login validation, session regeneration, and default dashboard intent remain unchanged for non-donor contexts
  - intentionally not changed: Google sign-in, guardian login behavior, multi-role chooser logic
  - regression checks: prompt-35 donor auth tests, prompt-31 open registration tests
  - rollback note: reverting this file restores the old dashboard default redirect after login
- `app/Http/Middleware/RedirectPortalUsersFromLegacyDashboard.php`
  - impact class: `high`
  - why touched: redirect donor-context accounts to `/donor` using donor-domain access rules instead of raw `role:donor` + verified assumptions
  - preserved behavior: management bypass and existing guardian redirect logic remain intact
  - intentionally not changed: multi-role chooser behavior, guardian informational rollout, management dashboard access
  - regression checks: phase-3 donor portal tests, prompt-35 donor auth tests
  - rollback note: reverting this file restores the old donor redirect requirement
- `app/Services/DonorPortal/DonorPortalData.php`
  - impact class: `high`
  - why touched: derive donor access states and bridge new donor-domain history/receipts into the read-only donor portal
  - preserved behavior: donor portal remains read-only and fail-closed for non-eligible accounts
  - intentionally not changed: donor settlement writes, guest claim/link, guardian payment or receipt queries
  - regression checks: phase-3 donor portal tests, prompt-34 donor payable tests, prompt-35 donor auth tests
  - rollback note: reverting this file restores legacy donor-profile-only portal reads and removes the new donor-domain history bridge
- `app/Http/Controllers/Auth/EmailVerificationPromptController.php`
  - impact class: `medium`
  - why touched: route already-verified donor-only users to the donor home/no-portal surface instead of a generic dashboard path
  - preserved behavior: unverified users still see the same verification notice
  - intentionally not changed: verification sending rules, phone verification, guardian informational routing
  - regression checks: prompt-32 verification tests, prompt-35 donor auth tests
  - rollback note: reverting this file restores the old post-verification notice redirect logic
- `app/Http/Controllers/Auth/VerifyEmailController.php`
  - impact class: `high`
  - why touched: keep donor-only post-verification landing aligned with the new donor access surface
  - preserved behavior: email verification still changes email trust only and still does not auto-grant donor portal eligibility
  - intentionally not changed: audit logging contract, guardian logic, approval or phone-verification semantics
  - regression checks: prompt-32 verification tests, prompt-35 donor auth tests
  - rollback note: reverting this file restores the old post-verification redirect behavior only
- `app/Http/Controllers/Auth/EmailVerificationNotificationController.php`
  - impact class: `medium`
  - why touched: keep already-verified donor-only users aligned with the donor access redirect when verification resend is revisited
  - preserved behavior: resend cooldowns, hourly caps, audit logging, and warning messages remain intact
  - intentionally not changed: send-rate rules, phone verification, guardian logic
  - regression checks: prompt-32 verification tests
  - rollback note: reverting this file restores the old already-verified redirect path
- `resources/views/components/donor-layout.blade.php`
  - impact class: `low`
  - why touched: allow the donor no-portal state to reuse the donor shell with a narrower navigation set
  - preserved behavior: existing donor dashboard, donations, and receipts navigation remains unchanged by default
  - intentionally not changed: shared portal shell branding, guardian shell, layout route names
  - regression checks: phase-3 donor portal tests, prompt-35 donor auth tests
  - rollback note: reverting this file removes the no-portal shell navigation override only

## Durable Artifact Promotion

- Promoted approved decisions to `docs/codex/04-decisions/approved/prompt-35-donor-auth-and-portal-access.md`
- Promoted the reusable donor access and history workflow artifact to `docs/codex/05-artifacts/workflow/prompt-35-donor-auth-and-portal-access.md`

## Validation

- `php.exe artisan route:list --path=donor`
  - result: `pass`
  - summary: donor route names remain registered and `/donor`, `/donor/donations`, and `/donor/receipts` are still present under the same route names
- `php.exe artisan test --env=testing tests/Feature/Phase12/DonorAuthAndPortalAccessTest.php`
  - result: `pass`
  - summary: `3 passed (28 assertions)`
- `php.exe artisan test --env=testing tests/Feature/Phase3/DonorPortalTest.php tests/Feature/Phase12/DonorPayableFoundationTest.php tests/Feature/Phase12/OpenRegistrationFoundationTest.php tests/Feature/Phase12/EmailPhoneVerificationFoundationTest.php`
  - result: `pass`
  - summary: `17 passed (215 assertions)`

## Contradiction / Blocker Pass

- No contradiction was found with prompt-09's donor permission separation, prompt-12's donor slice order, prompt-18's route policy, prompt-30's account-state read-path rules, prompt-31's donor foundation model, prompt-32's verification separation, or prompt-34's donor payable foundation.
- Prompt-35 preserves the prompt-34 donor settlement truth and does not route donor finalization through guardian invoice settlement or legacy donor `transactions`.
- The donor history bridge keeps legacy `transactions` and new `donation_records` visibly separate, so prompt-35 does not reopen the rejected legacy-conversion shortcut.
- Guest and identified transaction-specific access remain narrow; prompt-35 adds no account-wide browsing for guest donors or non-portal identified donors.
- Guardian behavior was not widened, and multi-role chooser/switching behavior remains deferred to prompt-39.
- Validation had to use the Laragon `php.exe` runtime with escalation because the default shell still cannot execute the Windows PHP runtime directly.
- Runtime Git cleanliness still could not be reconfirmed with `git status` because `git` is not available in the current shell environment.
- No product blocker was found.
- No correction pass is required.

## Risks

- Donor-only redirect logic still deliberately avoids early multi-role chooser behavior; donor-plus-guardian context switching remains a later prompt-39 concern.
- Donor receipt scoping still relies on the current `DonationIntent` payable typing plus the legacy null-payable donor receipt pattern; later multi-role hardening may tighten that provenance further.
- Runtime Git validation remains limited because `git` is unavailable in the current shell environment.

## Next Safe Slice

- `docs/codex/01-prompts/prompt-36-guardian-informational-portal.md`
