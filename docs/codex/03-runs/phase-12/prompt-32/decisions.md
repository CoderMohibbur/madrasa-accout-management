# Decisions

- Prompt-32 keeps email verification and phone verification as separate account-level contact-trust axes on `users`.
- Email remains the canonical login identifier in this slice; phone remains optional and verification-first only.
- Open registration and profile editing now support optional normalized account phones without auto-verifying them.
- Prompt-31's unified registration flow, `registered_user` role, neutral onboarding destination, and donor/guardian draft-profile boundary all remain intact.
- Legacy verified routes still keep their current email-verification gate; prompt-32 does not widen donor or guardian portal access.
- Email resend now enforces a 60-second cooldown and a 6-per-hour cap per account/email, with audit logging.
- Phone resend now enforces a 60-second cooldown and a 5-per-hour cap per normalized phone and IP, with a 10-minute code expiry and temporary cooldown after repeated failures.
- Verified-phone conflicts fail closed if another active account already owns the verified number.
- Changing email resets only email verification, and changing phone resets only phone verification.
- Local and `testing` environments use a documented phone-code delivery placeholder that must be replaced before production SMS rollout.
