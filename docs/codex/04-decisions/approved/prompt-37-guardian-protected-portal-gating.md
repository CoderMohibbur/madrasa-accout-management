# Prompt 37 Guardian Protected Portal Gating

Approved implementation decisions from prompt-37:

- The live protected `/guardian` routes and guardian payment initiation routes now use dedicated `guardian.protected` middleware instead of blanket `verified + role:guardian`.
- Guardian protected eligibility is derived from accessible account state, verified email, guardian profile lifecycle flags, and explicit guardian-student linkage.
- Guardian role membership remains guardian-domain potential only; it is not the final protected route gate.
- Verified linked guardians with protected-eligible guardian profiles may use the protected guardian portal even without a raw guardian role row.
- Unverified, unlinked, or role-only guardian-context accounts remain fail-closed on protected routes and stay limited to safe informational surfaces when otherwise eligible.
- Protected payment initiation and payment detail authorization now align with protected invoice ownership and fail closed for mismatched guardian invoice access.
- Prompt-36's additive guardian informational route space, prompt-35 donor behavior, and informational-only admission CTA placement remain preserved.
