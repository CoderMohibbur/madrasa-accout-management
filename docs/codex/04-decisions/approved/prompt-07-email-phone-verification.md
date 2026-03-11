# Prompt 07 Email Phone Verification

Approved baseline decisions from prompt-07:

- Email verification state and phone verification state are separate account-level contact-trust axes.
- Verification state remains independent from approval, role assignment, portal eligibility, and guardian linkage.
- Phone stays optional in the target model and is not a universal prerequisite for donor registration, donor login, donor donation, guardian login, guardian informational access, or guest donation identity capture.
- Email remains the canonical login identifier in the smallest safe rollout; phone is verification-first rather than a primary login identifier.
- Guest donation contact capture remains unverified operational data until explicit duplicate-safe account linking and explicit verification occur.
- Stronger contact assurance for later protected actions must be implemented as a separate step-up rule, not by reusing blanket `verified` middleware as the global boundary.
