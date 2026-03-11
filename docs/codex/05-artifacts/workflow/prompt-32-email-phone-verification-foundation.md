# Prompt 32 Email Phone Verification Foundation

## Contact Verification Surfaces

- `POST /email/verification-notification`
- `POST /phone/verification-notification`
- `POST /phone/verify`
- `GET /verify-email`
- `GET /verify-email/{id}/{hash}`

## Account-Level Contact Model

- `users.email` remains the canonical login identifier
- `users.email_verified_at` tracks email trust only
- `users.phone` stores the normalized optional account phone
- `users.phone_verified_at` tracks phone trust only
- changing one channel resets only that channel's verification state

## Email Flow

- open registration issues an email verification attempt without blocking registration if the local mail transport is unavailable
- manual resend uses a 60-second cooldown and a 6-per-hour hard cap
- successful email verification keeps routing through the existing legacy email-verification boundary
- `registered_user` accounts return to neutral onboarding after verification instead of being widened into donor or guardian portals

## Phone Flow

- phone verification is opt-in and starts only after an authenticated user already has an account phone
- send rules:
  - 60-second resend cooldown
  - 5 sends per hour per normalized phone and IP combination
- verify rules:
  - 6-digit code
  - 10-minute expiry
  - repeated invalid submissions invalidate the code and apply a temporary cooldown
- verified-phone conflicts fail closed if another active account already owns the verified number

## Current Placeholder Boundary

- local and `testing` environments expose a development-only placeholder code so the foundation can be exercised without an SMS provider
- production must replace that placeholder with a real SMS delivery integration

## Preserved Deferred Boundaries

- no donor payable redesign
- no guest donation finalization changes
- no donor or guardian portal-role rollout
- no legacy verified-route gate removal
- no Google sign-in work
