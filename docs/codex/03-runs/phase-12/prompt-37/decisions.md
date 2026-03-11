# Decisions

- Prompt-37 introduces dedicated `guardian.protected` middleware for the live protected `/guardian` routes and guardian payment initiation routes.
- Guardian protected eligibility is now derived from accessible account state, verified email, guardian profile lifecycle flags, and explicit guardian-student linkage; raw guardian role membership is no longer the protected route gate.
- Protected `/guardian` route names, views, and read paths remain intact; prompt-37 changes the route-edge gate rather than replacing the protected route space.
- Verified linked guardians with a protected-eligible profile may use the protected guardian portal even without a raw guardian role row.
- Unverified, unlinked, or role-only guardian-context accounts remain fail-closed on protected routes and may use only safe informational guardian surfaces when otherwise eligible.
- Guardian payment initiation, manual-bank detail access, and payment detail authorization now align with the protected invoice ownership boundary instead of looser guardian-user shortcuts.
- Guardian-profile users are now blocked from falling through to legacy management surfaces solely because they lack a guardian role row.
- Prompt-36 informational routes, prompt-35 donor behavior, and informational-only admission CTA placement remain unchanged.
