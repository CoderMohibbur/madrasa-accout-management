# Report

## Scope Lock Before Coding

### Exact Approved Slice

- Prompt-32 implemented only the email/phone verification foundation on top of prompt-31's open-registration foundation.
- The slice was limited to:
  - minimum email verification foundation hardening for the new prompt-31 registration flow
  - optional account-level phone capture and verification foundation
  - approved resend, cooldown, expiry, and lockout behavior
  - neutral onboarding and profile surfaces needed to manage those contact channels safely
- Prompt-32 reused prompt-31's unified registration backend, `registered_user` compatibility role, neutral onboarding handoff, and donor/guardian draft-profile boundary.

### Phone And Email Identity Rules Restated

- Email verification state and phone verification state remain separate account-level contact-trust axes.
- Verification stays separate from approval, donor eligibility, guardian linkage, role assignment, and portal eligibility.
- Email remains the canonical login identifier in this rollout.
- Phone stays optional and verification-first.
- Changing email resets only `email_verified_at`.
- Changing phone resets only `phone_verified_at`.
- A verified phone cannot be silently re-assigned if another active account already owns it.

### Anti-Abuse Requirements Restated

- Email resend cooldown: 60 seconds
- Email resend hard cap: 6 sends per hour per account/email
- Phone resend cooldown: 60 seconds
- Phone resend hard cap: 5 sends per hour per normalized phone and IP
- Phone code expiry: 10 minutes
- Phone code retry limit: repeated invalid attempts invalidate the code and apply a temporary cooldown
- Verification sends, successes, channel changes, conflicts, and lockouts must be auditable

### Explicitly Deferred

- no donor payable redesign
- no guest donation finalization changes
- no donor or guardian portal-role rollout
- no legacy verified-route gate removal
- no Google sign-in
- no broader donor/guardian portal rollout or legacy route cleanup

## Implementation Result

Prompt-32 completed inside the approved verification-foundation slice.

### Files Changed

- `app/Http/Controllers/Auth/EmailVerificationNotificationController.php`
- `app/Http/Controllers/Auth/EmailVerificationPromptController.php`
- `app/Http/Controllers/Auth/PhoneVerificationController.php`
- `app/Http/Controllers/Auth/RegisteredUserController.php`
- `app/Http/Controllers/Auth/VerifyEmailController.php`
- `app/Http/Controllers/ProfileController.php`
- `app/Http/Requests/Auth/LoginRequest.php`
- `app/Http/Requests/Auth/OpenRegistrationRequest.php`
- `app/Http/Requests/Auth/VerifyPhoneRequest.php`
- `app/Http/Requests/ProfileUpdateRequest.php`
- `app/Models/User.php`
- `app/Services/Auth/ContactVerificationAuditLogger.php`
- `app/Services/Auth/EmailVerificationNotificationService.php`
- `app/Services/Auth/PhoneVerificationBroker.php`
- `app/Support/PhoneNumber.php`
- `resources/views/auth/onboarding.blade.php`
- `resources/views/auth/partials/contact-verification-panel.blade.php`
- `resources/views/auth/register.blade.php`
- `resources/views/auth/verify-email.blade.php`
- `resources/views/profile/partials/update-profile-information-form.blade.php`
- `routes/auth.php`
- `tests/Feature/Phase12/EmailPhoneVerificationFoundationTest.php`

### Verification Foundation Implemented

- Open registration now accepts an optional account phone, normalizes it, stores it on `users`, and still leaves it unverified by default.
- Registration continues to create only the prompt-31 base account plus optional donor or guardian draft foundation rows; no donor or guardian portal-driving roles were added.
- Email verification sending now works through a shared service that:
  - records audit events for resend and delivery-deferred cases
  - fails soft if the local mail transport is unavailable instead of breaking registration or profile updates
- Email resend now enforces the approved anti-abuse rules:
  - 60-second resend cooldown
  - 6 sends per hour per account/email
- Phone verification foundation is now available for authenticated accounts through dedicated POST routes:
  - `verification.phone.send`
  - `verification.phone.verify`
- Phone verification issues a 6-digit code, expires it after 10 minutes, limits per-code retries, and applies a temporary cooldown after repeated invalid attempts.
- Verified-phone ownership fails closed if another active account already owns the verified number.
- Login email normalization was tightened so email stays the canonical identifier even when casing or whitespace differs.
- Profile updates now reset only the changed verification axis:
  - email change resets email verification only
  - phone change resets phone verification only
- Neutral onboarding and profile settings now surface the new verification state and actions without widening portal access.

### Preserved Boundaries

- Prompt-31's neutral onboarding remains the post-registration landing for `registered_user`.
- Donor and guardian registration intent still creates only non-portal, inactive or unlinked draft foundation rows.
- Legacy verified routes still keep their current email-verification gate.
- Prompt-32 did not widen donor or guardian portal access and did not remove or alter the legacy verified boundary.
- Prompt-32 did not pull guest donation, donor payable, Google sign-in, or legacy routing cleanup forward.

### Anti-Abuse Notes

- Email resend throttles are independent from login throttles.
- Phone send throttles are independent from login throttles and keyed by normalized phone plus IP.
- Phone verification uses per-code retry counting plus a separate temporary cooldown for repeated failures.
- Verification sends, successes, channel changes, conflicts, and lockouts are all written to `audit_logs`.

### Compatibility Notes For Critical Existing Files

- `app/Http/Controllers/Auth/RegisteredUserController.php`
  - impact class: `critical`
  - why touched: reuse prompt-31 registration flow while adding optional phone capture and the new email verification foundation
  - preserved behavior: unified backend, prompt-31 donor/guardian draft-state boundary, neutral onboarding destination
  - intentionally not changed: donor or guardian role rollout, portal eligibility, guest donation, legacy verified-route middleware
  - regression checks: prompt-31 registration tests, prompt-32 verification tests
  - rollback note: reverting this file removes optional phone capture and prompt-32 email-send hardening but should not require schema rollback
- `app/Http/Controllers/Auth/EmailVerificationNotificationController.php`
  - impact class: `critical`
  - why touched: implement the approved resend cooldown/hourly cap and audit trail
  - preserved behavior: verified users still bypass resend, existing verify-email route names stay intact
  - intentionally not changed: legacy verified-route middleware, approval model, login gating
  - regression checks: `EmailVerificationTest`, prompt-32 resend tests, full-suite baseline separation
  - rollback note: reverting this file restores Breeze-style resend behavior and removes the prompt-32 anti-abuse protections
- `app/Http/Controllers/Auth/VerifyEmailController.php`
  - impact class: `critical`
  - why touched: keep registered users on neutral onboarding after verification and audit successful verification
  - preserved behavior: non-registered users still land back on the legacy dashboard compatibility path
  - intentionally not changed: verified-route middleware, donor or guardian access logic
  - regression checks: `EmailVerificationTest`, prompt-32 phone/email coexistence tests
  - rollback note: reverting this file restores the prior dashboard redirect behavior but loses prompt-32 audit coverage
- `app/Http/Controllers/ProfileController.php`
  - impact class: `high`
  - why touched: reset only the changed verification axis and support optional phone editing
  - preserved behavior: profile editing stays auth-only, deletion behavior stays unchanged
  - intentionally not changed: password, deletion, and broader auth baseline behavior
  - regression checks: prompt-32 profile coexistence test, full-suite baseline comparison
  - rollback note: reverting this file removes prompt-32's channel-specific reset behavior
- `app/Http/Requests/Auth/LoginRequest.php`
  - impact class: `critical`
  - why touched: normalize the canonical email identifier before lookup and throttling
  - preserved behavior: approval/account-state gating from prompt-30 remains intact
  - intentionally not changed: phone login, approval rules, account-status rules
  - regression checks: `AccountStateReadPathAdaptationTest`, full-suite baseline comparison
  - rollback note: reverting this file restores case-sensitive or whitespace-sensitive login lookup risk
- `app/Models/User.php`
  - impact class: `critical`
  - why touched: add normalized email/phone setters and explicit account-level phone verification helpers
  - preserved behavior: approval/account-state helpers from prompt-29 and prompt-30 remain intact
  - intentionally not changed: portal eligibility derivation, donor or guardian role logic
  - regression checks: prompt-29 and prompt-30 phase-12 tests plus prompt-32 verification tests
  - rollback note: reverting this file removes normalized phone/email handling and account-level phone helper methods
- `routes/auth.php`
  - impact class: `critical`
  - why touched: register the new phone verification endpoints and keep email verification route names intact
  - preserved behavior: existing auth route names and verify-email routes remain unchanged
  - intentionally not changed: route names, guest donation routes, donor/guardian portal routes
  - regression checks: route-list checks, `EmailVerificationTest`, prompt-32 verification tests
  - rollback note: reverting this file removes the phone verification endpoints

## Durable Artifact Promotion

- promoted approved decisions to `docs/codex/04-decisions/approved/prompt-32-email-phone-verification-foundation.md`
- promoted the reusable verification workflow artifact to `docs/codex/05-artifacts/workflow/prompt-32-email-phone-verification-foundation.md`
- recorded the development-only phone-code placeholder in `docs/codex/06-production-replace/prompt-32-phone-verification-placeholder.md`

## Validation

- `php.exe artisan route:list --path=verification`
  - result: `pass`
  - summary: `email verification resend route is registered`
- `php.exe artisan route:list --path=verify-email`
  - result: `pass`
  - summary: `verification.notice and verification.verify remain registered`
- `php.exe artisan route:list --path=phone`
  - result: `pass`
  - summary: `verification.phone.send and verification.phone.verify are registered`
- `php.exe artisan test --env=testing tests/Feature/Phase12/AccountStateSchemaFoundationTest.php tests/Feature/Phase12/AccountStateReadPathAdaptationTest.php tests/Feature/Phase12/OpenRegistrationFoundationTest.php tests/Feature/Phase12/EmailPhoneVerificationFoundationTest.php tests/Feature/Auth/EmailVerificationTest.php`
  - result: `pass`
  - summary: `17 passed (143 assertions)`
- `php.exe artisan test --env=testing`
  - result: `expected baseline failure set only`
  - summary: `14 failed, 45 passed (302 assertions)`
  - classification: `failure list still matches the existing auth/profile baseline manifest exactly`
  - unexpected regressions: `none`

## Contradiction / Blocker Pass

- No contradiction was found with prompt-07's approved phone/email coexistence model or prompt-25's `EMAIL-*` and `PHONE-*` coverage expectations.
- Prompt-32 preserved prompt-31's unified registration backend, neutral onboarding route, `registered_user` compatibility role, and donor/guardian draft-state boundary.
- Prompt-32 preserved the current legacy `verified` gate on legacy verified routes rather than widening donor or guardian access early.
- The local mail transport gap was handled inside the prompt-32 slice as an environment compatibility issue rather than a blocker; registration and resend flows now fail soft and record deferred delivery audit events.
- The development-only phone-code reveal was documented under `docs/codex/06-production-replace/` and does not block the approved foundation slice.
- No product blocker was found.
- No correction pass is required.

## Risks

- Real SMS delivery is still deferred; the local and `testing` placeholder reveal must be replaced before production.
- Legacy verified routes still depend on email verification until later prompts replace that boundary explicitly.
- Email delivery can be deferred safely in the local environment now, but production rollout still depends on valid mail transport configuration.

## Next Safe Slice

- `docs/codex/01-prompts/prompt-33-guest-donation-entry.md`
