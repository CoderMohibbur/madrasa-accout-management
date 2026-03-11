# Report

This prompt re-runs verification analysis against the live repository plus approved prompt-01 to prompt-06 outputs. The current repo still has only Laravel email verification, no base-account phone field, no SMS verification workflow, and an overloaded `email_verified_at` that doubles as both verification and login approval. Prompt-07 therefore defines the target coexistence model without treating the live email-only behavior as the final rule.

## Current Verification Conflicts

- `users` contains `email` and `email_verified_at` only. There is no base-identity phone field and no general-purpose account-level phone verification state.
- `User` still implements `MustVerifyEmail`, while `routes/auth.php` exposes only email notice, resend, and signed-URL verification routes.
- `RegisteredUserController` suppresses the `Registered` event, so registration does not start the normal email-verification flow.
- `LoginRequest` blocks login when `email_verified_at` is null, which conflicts with the frozen prompt-03 rules that donor login and guardian login must remain possible without universal verification.
- `routes/web.php` and `routes/payments.php` still use the `verified` middleware broadly for dashboard, donor, guardian, management, and payment routes, so current email verification behavior is still standing in for multiple unrelated concerns.
- Donor and guardian `mobile` fields exist only on the domain profiles, not on the base account. That is too narrow for a single-account multi-role verification model because verified contact trust must follow the account across donor and guardian contexts.
- `ProfileController` resets `email_verified_at` when email changes, which is reasonable for email-channel trust, but it also highlights why phone verification must live on its own axis instead of sharing the same field.

## Verification Coexistence Model

- Email verification state and phone verification state must be modeled as two independent contact-trust axes on the base identity or a shared account-contact layer.
- Verification state must remain separate from:
  - admin approval
  - role assignment
  - portal eligibility
  - guardian linkage
  - donor or guardian profile activation
- Phone should be optional overall in the safe target model.
  - It must not be required for public registration.
  - It must not be required for donor registration, donor login, or donor donation.
  - It must not be required for guardian login or guardian informational access.
  - It must not be required for guest donation identity capture.
- Donor and guardian profile `mobile` values may continue to exist as domain contact fields, but canonical verified-phone status should belong to the account-level identity layer so one account can safely expand across roles.
- Recommended state behavior:
  - `neither verified`
    - base login may still be allowed if approval and lifecycle policy allow it
    - donor login and donor donation remain allowed
    - guardian login remains allowed
    - guardian informational access remains allowed when guardian intent or guardian presence exists
    - no channel is trusted for high-assurance notices, recovery, or verification-dependent step-up checks
  - `email only`
    - email can be trusted for receipts, verification notices, password reset, and account communications
    - donor login and donation remain allowed
    - guardian login and informational access remain allowed
    - phone is still untrusted
  - `phone only`
    - phone can be trusted for SMS or OTP communication and later step-up checks
    - donor login and donation remain allowed
    - guardian login and informational access remain allowed
    - email is still untrusted
  - `both`
    - both contact channels are trusted
    - the account has the strongest contact-assurance state for recovery, alerts, and later high-risk actions
    - this still does not by itself grant approval, role changes, portal eligibility, guardian linkage, or protected access
- Recommended guardrail for later prompts:
  - if a later protected or payment-sensitive guardian or donor action wants stronger assurance, require that as a separate step-up rule, not through blanket `verified` middleware and not as a substitute for linkage or portal eligibility

## Identifier Rules

- Keep email as the canonical login identifier in the smallest safe rollout.
  - The live auth flow is already email-based.
  - `users.email` is already unique.
  - There is no account-level phone identifier yet.
  - Making phone a login identifier now would widen scope into schema, duplicate resolution, recovery, and UX changes that exceed prompt-07.
- In prompt-07, phone should be verification-only plus notification/recovery-ready, not a primary login identifier.
- Normalize email before comparison and storage:
  - trim whitespace
  - lowercase for uniqueness and lookup
- Normalize phone before comparison and storage:
  - convert to one canonical format
  - use the normalized form for duplicate checks, resend throttles, and verification attempts
- Channel change rules:
  - changing email resets only email verification state
  - changing phone resets only phone verification state
  - changing one channel must not erase the other channel's verification record
- Guest donation identity capture does not create a login identifier automatically. Captured email or phone remains an operational contact only until the user later enters an authenticated identity flow.

## Duplicate Rules

- Registered-account email should remain hard-unique at the account layer after normalization.
- A verified email cannot be auto-claimed by another account, auto-merged from guest donation data, or copied into another role-specific identity.
- Registered-account phone should use stricter rules once phone verification exists:
  - unverified duplicate phone captures may exist temporarily in drafts, guest-donation metadata, or inactive onboarding states
  - verified phone ownership should resolve to only one active account-level identity at a time
- Donor and guardian profile `mobile` fields are not sufficient proof of ownership and must not auto-promote into a verified account phone without an explicit verification step.
- Guest donation contact capture rules:
  - email and phone may be absent
  - if present, they are unverified by default
  - matching a guest email or phone to an existing account must never auto-verify, auto-link, auto-merge, or auto-enable portal access
- Later account-linking rules should require authenticated proof or explicit user confirmation before attaching an existing donor or guardian record to a registered account that shares contact data.

## Anti-Abuse Rules

- Verification send and verify operations should use separate throttles from login throttles.
- Email verification resend:
  - minimum resend cooldown: 60 seconds
  - hard cap: 6 sends per hour per account and normalized email
  - only one latest verification link should be treated as active for trust purposes
- Phone verification resend:
  - minimum resend cooldown: 60 seconds
  - hard cap: 5 sends per hour per normalized phone and IP combination
  - OTP expiry should stay short-lived, such as 10 minutes
- Verification attempt rules:
  - limit OTP entry retries per issued code
  - invalidate the code after repeated failures
  - add a longer temporary cooldown after repeated send or verify abuse
- Login throttling may remain separate from verification throttling; the current login rate limit keyed by identifier and IP is still a useful independent control.
- Public and guest-facing flows must not reveal whether an email or phone already belongs to an account, is already verified, or is linked to a donor or guardian profile.
- Guest donation should not auto-send verification messages simply because optional contact data was supplied; explicit user intent is required before contacting that channel for account verification.
- Verification requests, success events, channel changes, duplicate conflicts, and lockouts should all be auditable for later abuse review and manual support.

## Donor/Guardian Implications

- Donor implications:
  - donor registration must not require phone verification
  - donor login must not require email or phone verification as a universal gate
  - donor donation must not require email or phone verification as a universal gate
  - donor portal eligibility remains separate from verification state
  - verified contact channels may later improve receipts, support, recovery, or fraud controls, but they must not silently become the donor portal eligibility rule
- Guardian implications:
  - guardian login must not require completed email or phone verification as a universal gate
  - guardian informational access must remain available without completed verification when guardian intent or guardian presence exists
  - guardian protected access still depends first on linkage and authorization
  - if later prompts want stronger assurance for payment-sensitive or account-claim actions, requiring at least one verified contact channel is safer than blanket `verified` middleware, but that rule must remain separate from linkage and portal eligibility
- Multi-role implication:
  - verified email and phone belong to the shared account identity and follow that user across donor and guardian contexts
  - verifying a channel does not create a second role or a second portal context
- Guest donation implication:
  - guest donors may provide neither, email only, phone only, or both as operational contact data
  - guest donation contact capture does not create a verified identity state
  - if a later donor account is created from guest flows, both channels must still start from explicit duplicate-safe linking and explicit verification rules

## Unresolved Risks If Any

- The current repo has no account-level phone field or contact-method model, so later implementation prompts must decide where phone verification state actually lives.
- The current repo still uses `verified` middleware broadly, which conflicts with the frozen requirement that donor login/donation and guardian login/info access must not depend on universal verification.
- A strict one-active-account-per-verified-phone rule is the safest default for recovery and anti-abuse, but it may need an explicit exception policy if the business requires shared household phone numbers across separate accounts.
- Password reset is email-only today. If phone verification later expands into recovery, later prompts must avoid turning a newly added phone channel into an account-takeover shortcut.
