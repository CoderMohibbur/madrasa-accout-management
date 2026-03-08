# PHASE 2 REPORT

## Phase
PHASE_2_GUARDIAN_PORTAL

## Status
- completed

## Objective
Build a safe guardian read portal using the Phase 1 ownership and posting boundaries, without introducing live payment gateway logic.

## Scope Actually Implemented
- Replaced the placeholder `/guardian` foundation page with a real guardian dashboard, linked student profile view, invoice list/detail pages, and payment history page.
- Added a guardian portal query service so all Phase 2 reads stay scoped to guardian-student links plus the Phase 1 invoice and receipt safety rules.
- Added a dedicated guardian portal layout and Blade views that avoid reusing the legacy management navigation.
- Added a narrow legacy management-surface guard so guardian-only and donor-only users are redirected off `/dashboard` and blocked from legacy management screens while unroled legacy users still retain current access.
- Updated stale Phase 1 guardian route coverage and added Phase 2 feature tests for dashboard visibility, ownership enforcement, and management-surface isolation.

## Branch / Commit
- safe branch: `codex/2026-03-08-phase-1-foundation-safety`
- phase start commit: `39d37c34d04842d7bc75a93b085b5f83c68c835a`
- phase end commit: `a63c3af4a29c27b3158c6fdb0083413d91c58368`

## Files Touched
- New files:
  - `app/Http/Controllers/Guardian/GuardianPortalController.php`
  - `app/Http/Middleware/EnsureManagementSurfaceAccess.php`
  - `app/Http/Middleware/RedirectPortalUsersFromLegacyDashboard.php`
  - `app/Services/GuardianPortal/GuardianPortalData.php`
  - `resources/views/components/guardian-layout.blade.php`
  - `resources/views/guardian/dashboard.blade.php`
  - `resources/views/guardian/student.blade.php`
  - `resources/views/guardian/invoices/index.blade.php`
  - `resources/views/guardian/invoices/show.blade.php`
  - `resources/views/guardian/history.blade.php`
  - `tests/Feature/Phase2/GuardianPortalTest.php`
- Existing files modified:
  - `bootstrap/app.php`
  - `routes/guardian.php`
  - `routes/web.php`
  - `tests/Feature/Phase1/PortalRoleAccessTest.php`

## Compatibility Notes For Existing Files
- `bootstrap/app.php`
  - Why changed: register the additive `portal.home` and `management.surface` middleware aliases used by the guardian portal.
  - Previous behavior preserved: existing `role` middleware alias and the broader app bootstrap remain intact.
  - Rollback note: removing the aliases disables only the new guardian portal landing and management-surface guard behavior.
- `routes/guardian.php`
  - Why changed: replace the Phase 1 placeholder guardian page with real Phase 2 guardian routes.
  - Previous behavior preserved: the `/guardian` prefix and `guardian.*` route namespace remain unchanged.
  - Rollback note: reverting this file restores only the Phase 1 placeholder guardian foundation screen.
- `routes/web.php`
  - Why changed: preserve the existing route names while redirecting guardian-only and donor-only users away from `/dashboard` and blocking them from legacy management surfaces.
  - Previous behavior preserved: all existing route names remain stable, and unroled legacy users still retain their prior access until a later hardening/backfill step.
  - Rollback note: removing the new middleware wiring reopens legacy management surfaces to guardian-only and donor-only users.
- `tests/Feature/Phase1/PortalRoleAccessTest.php`
  - Why changed: align the stale guardian route expectation with the real Phase 2 guardian portal contract that now requires an active guardian profile.
  - Previous behavior preserved: the test still checks the role boundary itself.
  - Rollback note: reverting the test would reintroduce a false regression against the now-real guardian portal surface.

## Validation Performed
- `php -l app/Http/Middleware/EnsureManagementSurfaceAccess.php`
- `php -l app/Http/Middleware/RedirectPortalUsersFromLegacyDashboard.php`
- `php -l app/Services/GuardianPortal/GuardianPortalData.php`
- `php -l app/Http/Controllers/Guardian/GuardianPortalController.php`
- `php -l routes/guardian.php`
- `php -l routes/web.php`
- `php artisan route:list --path=guardian`
- `php artisan route:list --path=dashboard`
- `php artisan route:list --path=students`
- `php artisan test --env=testing tests/Feature/Phase1/PortalRoleAccessTest.php tests/Feature/Phase2/GuardianPortalTest.php tests/Unit/Policies/StudentFeeInvoicePolicyTest.php`
- `php artisan test --env=testing`

## Validation Result
- passed

## Baseline Failures Considered
- `Tests\Feature\Auth\AuthenticationTest::test_users_can_authenticate_using_the_login_screen`
- `Tests\Feature\Auth\AuthenticationTest::test_users_can_logout`
- `Tests\Feature\Auth\PasswordConfirmationTest::test_password_can_be_confirmed`
- `Tests\Feature\Auth\PasswordConfirmationTest::test_password_is_not_confirmed_with_invalid_password`
- `Tests\Feature\Auth\PasswordResetTest::test_reset_password_link_can_be_requested`
- `Tests\Feature\Auth\PasswordResetTest::test_reset_password_screen_can_be_rendered`
- `Tests\Feature\Auth\PasswordResetTest::test_password_can_be_reset_with_valid_token`
- `Tests\Feature\Auth\PasswordUpdateTest::test_password_can_be_updated`
- `Tests\Feature\Auth\PasswordUpdateTest::test_correct_password_must_be_provided_to_update_password`
- `Tests\Feature\Auth\RegistrationTest::test_new_users_can_register`
- `Tests\Feature\ProfileTest::test_profile_information_can_be_updated`
- `Tests\Feature\ProfileTest::test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged`
- `Tests\Feature\ProfileTest::test_user_can_delete_their_account`
- `Tests\Feature\ProfileTest::test_correct_password_must_be_provided_to_delete_account`

## Issues Found
- The first guardian student/invoice detail implementation used `$this->authorize()` even though this app's base controller does not include Laravel's authorization helper trait.
- The existing Phase 1 guardian route test assumed the placeholder guardian page contract instead of the real Phase 2 guardian portal profile requirement.
- A parallel rerun of two database-backed test commands temporarily dirtied the shared testing database with migration collisions; a sequential rerun restored the expected baseline.

## Corrections Applied
- Swapped the broken controller authorization helper call for explicit `StudentPolicy` and `StudentFeeInvoicePolicy` checks inside the guardian controller.
- Updated the stale Phase 1 guardian route test to provision an active guardian profile before asserting the guardian dashboard response.
- Re-ran the full suite sequentially after the temporary shared-testing-database collision and confirmed the expected 14 baseline failures with no Phase 2 regressions.

## Remaining Risks
- The broader auth/profile suite remains baseline-red and still cannot be used as a clean global regression gate.
- Legacy management surfaces still allow unroled users by design in this phase; a later hardening step must decide whether and how to backfill explicit management roles before stricter enforcement.
- Donor self-service views are still pending, so only the guardian side of the portal split is complete today.

## Next Phase Adjustments Needed
- no

## Adjustment Details
- Phase 3 can reuse the new portal landing and management-surface guard pattern while building donor-only dashboard, history, and receipt visibility from the existing donor linkage foundation.

## Go / No-Go Decision
GO for `PHASE_3_DONOR_PORTAL`.
