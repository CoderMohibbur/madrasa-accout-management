# Prompt 18 Route Middleware Policy

Approved baseline decisions from prompt-18:

- Blanket `verified` middleware is too broad for the approved target model and must not remain the final gate for donor portal, guardian informational, shared-home, or guest-donation/status surfaces.
- The safest additive route design keeps current live `/guardian` routes protected and introduces a separate authenticated informational guardian route space instead of repurposing `/guardian` early.
- The `dashboard` route name must remain for compatibility, but its future behavior must shift from guardian-first role redirect plus `management.surface` blocking to eligibility-based shared-home handling.
- Raw `role:guardian` and `role:donor` checks are too coarse to remain the final portal gates; dedicated donor, guardian-informational, guardian-protected, and shared-home eligibility middleware are required later.
- Guardian payment initiation must stay protected-guardian-controlled, and payment detail/status access should move toward explicit reusable ownership authorization rather than scattered route-role assumptions.
- Student, invoice, and receipt ownership policies remain protected-domain object checks and are not to be broadened for informational access.
