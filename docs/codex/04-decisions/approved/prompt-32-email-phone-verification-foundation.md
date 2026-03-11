# Prompt 32 Email Phone Verification Foundation

Approved implementation decisions from prompt-32:

- Email verification and phone verification now coexist as separate account-level contact-trust axes on `users`.
- Open registration and profile editing accept an optional account phone, normalize it, and keep it unverified until an explicit phone verification step succeeds.
- Email remains the canonical login identifier in this slice; login normalization was tightened, but phone is still verification-first rather than a login identifier.
- Prompt-31's shared registration backend, `registered_user` compatibility role, neutral onboarding handoff, and donor/guardian draft-profile boundary all remain intact.
- Legacy verified routes still keep their existing email-verification gate; prompt-32 does not pull donor or guardian portal rollout forward.
- Email verification resend now uses the approved anti-abuse rules: 60-second cooldown and 6 sends per hour per account/email, with audit logging.
- Phone verification uses a separate foundation flow with the approved anti-abuse posture: 60-second resend cooldown, 5 sends per hour per normalized phone and IP, 10-minute code expiry, per-code retry limits, and a temporary cooldown after repeated failures.
- Verified phone ownership fails closed if another active account already owns the verified number; prompt-32 does not silently reassign verified phone ownership.
- Contact-channel changes reset only the changed verification axis: changing email resets only `email_verified_at`, and changing phone resets only `phone_verified_at`.
- Verification sends, verification successes, channel changes, phone conflicts, and lockouts are now auditable through `audit_logs`.
- In local and `testing` environments, phone code delivery uses a development-only placeholder reveal that must be replaced with a real SMS provider before production.
