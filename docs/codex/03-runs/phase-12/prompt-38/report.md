# Report

## Scope Lock Before Coding

### Exact Approved Slice

- Prompt-38 is limited to Google sign-in foundation only.
- The approved slice is only:
  - Google OAuth dependency and configuration scaffolding
  - additive redirect/callback/link route and controller foundation
  - first-time public and donor Google onboarding on the same shared `users` model
  - explicit authenticated Google linking for existing accounts
  - safe unauthenticated auto-link only when verified normalized `users.email` matches exactly one account and no provider conflict exists
- Prompt-38 had to preserve:
  - prompt-35 donor no-portal vs portal behavior
  - prompt-36's additive guardian informational route space
  - prompt-37's derived guardian protected eligibility and payment-entry boundary
  - prompt-39's deferred multi-role chooser and switching work
  - the ban on unsafe merge, reassignment, or donor-payable redesign work

### Approved Linking And Duplicate Rules Restated

- Google remains an alternate sign-in method on the same single shared `users` account model.
- Verified normalized `users.email` is the only safe unauthenticated auto-link input.
- Existing provider-subject links win over changed email snapshots.
- If the same verified email already exists on `users`, prompt-38 links that account instead of creating a second user.
- Authenticated existing-account linking is allowed from profile, but only for the current signed-in account and without broad merge logic.
- Provider verification applies only to the email axis; it does not imply phone verification, approval, donor portal eligibility, guardian linkage, or protected guardian access.

### Unsafe Auto-Linking That Had To Stay Disabled

- no donor-email-only or guardian-email-only profile matching outside `users.email`
- no donor mobile, guardian mobile, guest donation contact, or name-similarity auto-linking
- no provider-subject reassignment from one user to another
- no first-time guardian Google onboarding expansion in this rollout
- no unlink/reassignment or broad account merge tooling

## Implementation Result

Prompt-38 completed inside the approved Google sign-in foundation scope.

### Files Changed

- `.env.example`
- `composer.json`
- `composer.lock`
- `config/services.php`
- `app/Models/User.php`
- `app/Models/ExternalIdentity.php`
- `database/migrations/2026_03_10_010000_create_external_identities_table.php`
- `app/Data/Auth/GoogleOAuthUser.php`
- `app/Contracts/Auth/GoogleOAuthBroker.php`
- `app/Services/Auth/GoogleSignInOutcome.php`
- `app/Services/Auth/SocialiteGoogleOAuthBroker.php`
- `app/Services/Auth/GoogleSignInService.php`
- `app/Http/Controllers/Auth/GoogleAuthController.php`
- `app/Providers/AppServiceProvider.php`
- `bootstrap/cache/packages.php`
- `bootstrap/cache/services.php`
- `routes/auth.php`
- `resources/views/auth/login.blade.php`
- `resources/views/auth/register.blade.php`
- `resources/views/profile/edit.blade.php`
- `resources/views/profile/partials/google-link.blade.php`
- `tests/Feature/Phase12/GoogleSignInFoundationTest.php`
- `tests/Feature/ProfileTest.php`

### Google Sign-In Foundation Implemented

- Added additive Google auth routes for:
  - guest redirect
  - shared callback
  - authenticated profile linking
- Added a dedicated Google sign-in service layer so provider-subject lookup, verified-email auto-link, first-time account creation, and explicit linking all use one fail-closed policy.
- Added `external_identities` persistence with unique provider-subject linkage and one-Google-link-per-user protection.
- First-time Google sign-in now supports:
  - public shared-account foundation creation
  - donor-intent shared-account creation with a non-portal donor profile only
- Existing-account Google behavior now supports:
  - repeat sign-in through an already-linked provider subject
  - verified-email auto-link to the canonical `users` row when no provider conflict exists
  - explicit authenticated link from profile without widening donor or guardian permissions
- First-time guardian Google onboarding remains deferred. The guardian register page now says so directly.

### Config And Dependency Changes

- Added `laravel/socialite` to `composer.json` and updated `composer.lock`.
- Installed the Socialite dependency in `vendor/laravel/socialite`.
- Disabled Socialite package auto-discovery in `composer.json` and switched the broker to instantiate `SocialiteManager` directly because the current Windows-PHP-on-WSL runtime could not reliably boot the discovered provider class during tests.
- Added `services.google` configuration in `config/services.php`.
- Regenerated the Laravel package manifest so `bootstrap/cache/packages.php` and `bootstrap/cache/services.php` match the final `dont-discover` posture.
- Added prompt-08 placeholder env keys to `.env.example`:
  - `GOOGLE_CLIENT_ID`
  - `GOOGLE_CLIENT_SECRET`
  - `GOOGLE_REDIRECT_URI`
- The live flow still relies on the documented replace-later values in `docs/codex/06-production-replace/prompt-08-google-oauth-placeholders.md`.

### Supported Flow Now Available

- Login page:
  - Google sign-in can reuse an already-linked account or create the minimal public shared account when no local account exists.
- Public register page:
  - Google can create the same shared account foundation as password registration.
- Donor register page:
  - Google can create the same shared account plus non-portal donor foundation without reopening donor portal history work.
- Existing account profile:
  - signed-in users can explicitly link Google to the current account
- Existing guardian accounts:
  - Google can sign in or link an already-existing guardian account, but protected guardian access still derives only from the real prompt-37 account/linkage rules

### Compatibility Notes For Existing Boundaries

- `routes/auth.php`
  - impact class: `critical`
  - why touched: add Google redirect, callback, and authenticated link routes without renaming existing auth routes
  - preserved behavior: `login`, `register*`, `verification.*`, `password.*`, and `logout` route names remain unchanged
  - intentionally not changed: donor routes, guardian routes, payments routes, and prompt-39 multi-role route behavior
- `app/Services/Auth/GoogleSignInService.php`
  - impact class: `critical`
  - why touched: centralize safe first-time create, safe existing-account auto-link, and explicit authenticated link behavior
  - preserved behavior: donor portal eligibility, guardian informational/protected gating, and account-state approval rules remain authoritative outside Google
  - intentionally not changed: donor payable, guardian student linkage, unlink/reassignment tooling
- `resources/views/auth/register.blade.php`
  - impact class: `medium`
  - why touched: surface Google entry points only where prompt-38 allows them and keep first-time guardian onboarding deferred
  - preserved behavior: email/password registration flow and intent cards remain intact
  - intentionally not changed: auth page admission CTA rules, guardian protected UI, donor history UI
- `resources/views/profile/edit.blade.php`
  - impact class: `medium`
  - why touched: expose authenticated Google linking on the current account without widening profile ownership or account-merge behavior
  - preserved behavior: profile update, password change, and delete flows remain intact
  - intentionally not changed: unlink/reassignment actions

## Durable Artifact Promotion

- Promoted approved decisions to `docs/codex/04-decisions/approved/prompt-38-google-signin-foundation.md`
- Promoted the reusable Google sign-in workflow artifact to `docs/codex/05-artifacts/workflow/prompt-38-google-signin-foundation.md`

## Validation

- Dependency install:
  - `php.exe composer.phar update laravel/socialite --with-all-dependencies`
    - result: `pass`
    - summary: `composer.lock now includes laravel/socialite and vendor/laravel/socialite is present`
- Package manifest refresh:
  - `php.exe artisan package:discover --ansi`
    - result: `pass`
    - summary: `cached package manifests were regenerated without Socialite auto-discovery`
- Route validation:
  - `php.exe artisan route:list --path=auth/google`
    - result: `pass`
    - summary: `3 Google auth routes registered: redirect, callback, and authenticated link`
- Prompt-38 focused validation:
  - `php.exe artisan test --env=testing tests/Feature/Phase12/GoogleSignInFoundationTest.php`
    - result: `pass`
    - summary: `6 passed (64 assertions)`
- Adjacent regression slice:
  - `php.exe artisan test --env=testing tests/Feature/ProfileTest.php tests/Feature/Phase12/GoogleSignInFoundationTest.php tests/Feature/Phase12/OpenRegistrationFoundationTest.php tests/Feature/Phase12/EmailPhoneVerificationFoundationTest.php tests/Feature/Phase12/DonorAuthAndPortalAccessTest.php tests/Feature/Phase12/GuardianInformationalPortalTest.php tests/Feature/Phase12/GuardianProtectedPortalGatingTest.php tests/Feature/Phase12/AccountStateReadPathAdaptationTest.php`
    - result: `pass`
    - summary: `33 passed (290 assertions)`

## Contradiction / Blocker Pass

- No contradiction was found with prompt-08's approved Google model, prompt-16's multi-role deferral, prompt-18's route policy, prompt-25's `GOOG-*` gates, prompt-31's registration foundation, prompt-32's verification separation, prompt-35's donor access rules, or prompt-37's guardian protected gating.
- Prompt-38 keeps first-time guardian Google onboarding deferred and does not reopen the prompt-36/prompt-37 guardian route split.
- Prompt-38 does not treat Google verification as guardian linkage or protected-access proof. Existing guardian protected access still derives from real accessible-account, verified-email, profile-state, and linked-student rules.
- Prompt-38 does not reopen donor history, donor receipts, donor payable, or legacy donor transaction conversion.
- Prompt-38 keeps Google unlink/reassignment and broad merge logic disabled, so provider-subject conflicts still fail closed.
- The only extra change outside prompt-38 product code was a test-harness alignment in `tests/Feature/ProfileTest.php` so the existing profile feature tests disable CSRF like the newer prompt suites already do.
- No product blocker was found.
- No correction pass is required.

## Risks

- Live Google redirect and callback use dummy placeholders until real client credentials and callback registration replace them.
- Multi-role chooser/switching remains deferred to prompt-39, so Google sign-in still lands through the current donor-first/shared-home compatibility routing.
- Prompt-38 intentionally leaves unlink/reassignment and broader Google recovery tooling off to avoid unsafe merge behavior.

## Next Safe Slice

- `docs/codex/01-prompts/prompt-39-multi-role-home-and-switching.md`
