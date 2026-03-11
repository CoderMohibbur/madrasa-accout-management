# Decisions

- Freeze email verification state and phone verification state as separate base-identity contact-trust axes, independent from approval, role assignment, portal eligibility, and guardian linkage.
- Keep phone optional in the target model; it must not become a universal prerequisite for donor registration, donor login, donor donation, guardian login, guardian informational access, or guest donation identity capture.
- Keep email as the canonical login identifier in the smallest safe rollout; treat phone as verification-only plus notification/recovery-ready until a later prompt explicitly widens identifier scope.
- Treat donor and guardian profile `mobile` fields as domain metadata, not as the canonical verified phone source for the shared account identity.
- Treat guest donation contact data as unverified operational contact only; it must never auto-verify, auto-link, auto-merge, or auto-grant portal access.
- If later protected flows need stronger assurance, require verified contact as a separate step-up rule rather than reusing blanket `verified` middleware as the global gate.
