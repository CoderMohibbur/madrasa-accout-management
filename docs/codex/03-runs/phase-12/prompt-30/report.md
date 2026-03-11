# Report

## Scope Lock Before Coding

### Exact Approved Slice

- prompt-30 was executed as account-state read-path adaptation only
- the approved read-path work was limited to:
  - interpreting prompt-29 `users.approval_status`
  - interpreting prompt-29 `users.account_status`
  - interpreting prompt-29 `users.deleted_at`
  - preserving `email_verified_at` as the email-trust field while removing it as the sole approval gate in login and related account-state checks
  - adapting the smallest set of existing login / dashboard / role / management / portal-entry reads to use the new separated account-state logic

### Account-State Distinctions Now Respected

- approval is read separately from email verification
- account lifecycle is read separately from approval
- deletion overrides access separately from both approval and lifecycle
- donor/guardian portal eligibility still depends on profile flags and later role-domain prompts; prompt-30 only adds the shared account-state prerequisite underneath those existing checks

### What Had To Remain Backward-Compatible

- prompt-29 schema stays additive and nullable-first
- `email_verified_at` remains unchanged as the email-verification field
- route names remain unchanged
- `verified` middleware stays in place on existing routes for now
- donor/guardian profile flags, guardian linkage, payment-domain schema, and legacy management behavior remain outside this prompt's redesign scope

## Implementation Result

Prompt-30 completed inside the approved read-path adaptation slice.

### Read-Path Behavior Adapted

- Added shared derived account-state reads on `App\Models\User`:
  - explicit `approval_status` when present, else legacy fallback from `email_verified_at`
  - explicit `account_status` when present, else legacy fallback to `active`
  - explicit `deleted_at` check as the account-level deletion override
  - one shared `hasAccessibleAccountState()` gate for approved + active + not-deleted access
- Updated `LoginRequest` so login approval now reads separated account-state logic instead of raw `email_verified_at` alone.
- Updated `EnsureUserHasRole` and `EnsureManagementSurfaceAccess` so protected role and management entry points fail closed on pending, inactive, suspended, or deleted accounts.
- Updated `RedirectPortalUsersFromLegacyDashboard` so `/dashboard` only redirects by role order when the matching donor/guardian portal profile is actually eligible; role-only guardian/donor rows now fail closed instead of being misrouted.
- Updated donor and guardian portal service entry checks to honor shared account-state access before checking domain-profile eligibility.

### Files Changed

- `app/Models/User.php`
- `app/Http/Requests/Auth/LoginRequest.php`
- `app/Http/Middleware/EnsureUserHasRole.php`
- `app/Http/Middleware/EnsureManagementSurfaceAccess.php`
- `app/Http/Middleware/RedirectPortalUsersFromLegacyDashboard.php`
- `app/Services/DonorPortal/DonorPortalData.php`
- `app/Services/GuardianPortal/GuardianPortalData.php`
- `tests/Feature/Phase12/AccountStateReadPathAdaptationTest.php`

## Compatibility Notes

- Prompt-30 preserved prompt-29's nullable-first dark schema posture by using fallback reads whenever `approval_status` or `account_status` is still null.
- `email_verified_at` still controls Laravel's email-verification semantics and existing `verified` middleware behavior.
- Approved-but-unverified accounts can now authenticate when explicit account-state fields allow it, but protected routes that still carry `verified` continue to redirect those users to the verification notice instead of widening portal access.
- Guardian and donor portal eligibility still depend on existing profile flags and later prompt-specific boundary work; prompt-30 did not introduce guardian informational access, donor no-portal access, or multi-role chooser logic.

## Durable Artifact Promotion

- promoted approved decisions to `docs/codex/04-decisions/approved/prompt-30-account-state-read-path-adaptation.md`
- promoted the reusable account-state read rules to `docs/codex/05-artifacts/state-models/prompt-30-account-state-read-path-adaptation.md`

## Validation

- `D:\laragon\bin\php\php-8.2.9-Win32-vs16-x64\php.exe artisan test --env=testing tests/Feature/Phase1/FoundationSchemaTest.php tests/Feature/Phase1/PortalRoleAccessTest.php tests/Feature/Phase2/GuardianPortalTest.php tests/Feature/Phase3/DonorPortalTest.php tests/Feature/Phase12/AccountStateSchemaFoundationTest.php tests/Feature/Phase12/AccountStateReadPathAdaptationTest.php tests/Feature/Auth/EmailVerificationTest.php`
  - result: `pass`
  - summary: `15 passed (85 assertions)`
- `D:\laragon\bin\php\php-8.2.9-Win32-vs16-x64\php.exe artisan test --env=testing`
  - result: `expected baseline failure set only`
  - summary: `14 failed, 35 passed`
  - classification: failure list still matches the existing auth/profile baseline manifest exactly; the added pass count comes from the prompt-29 and prompt-30 phase-12 tests
  - unexpected regressions: `none`

## Contradiction / Blocker Pass

- No contradiction was found with prompt-29's schema-only foundation, prompt-24's classify-first backfill posture, prompt-25's `AUTH-*` / `ROUTE-*` / `MIG-*` expectations, prompt-18's middleware direction, or prompt-27's no-go warning against removing blanket `verified` too early.
- Prompt-30 intentionally did not remove existing `verified` middleware; it introduced shared account-state reads underneath the current route stack and left final eligibility-middleware replacement for later prompts.
- No guardian linkage adoption, donor settlement schema, payment-domain redesign, Google sign-in work, or registration rewrite was pulled forward.
- Validation again used the local PHP 8.2 runtime because the local PHP 8.4 runtime lacks `mbstring`; this remained an environment/runtime quirk, not a product blocker.

## Risks

- Some auth-only self-service routes still rely on legacy `auth` behavior rather than the new shared account-state gate; prompt-31 and prompt-32 need to decide which of those flows should adopt explicit account-state enforcement.
- Role-only guardian or donor accounts now fail closed on `/dashboard` instead of being misrouted, but they still do not have the later no-portal/informational experience until the donor and guardian auth slices land.
- The new read-path logic still falls back to `email_verified_at` and implicit `active` when prompt-29 columns are null, so prompt-24 style backfill remains necessary before the fallback can be retired.

## Next Safe Slice

- `docs/codex/01-prompts/prompt-31-open-registration-foundation.md`
