# Decisions

- Blanket `verified` middleware is currently too broad for the approved target model and must be removed from donor portal, guardian informational, shared multi-role home, and future guest-donation entry/status surfaces once the shared account-state foundations exist.
- The safest additive route structure keeps current live `/guardian` routes protected and introduces a separate authenticated informational guardian prefix rather than repurposing `/guardian` before the approved guardian foundations are in place.
- `/dashboard` must keep its existing route name for compatibility, but its guard stack must move from guardian-first role redirect plus `management.surface` blocking toward eligibility-based shared-home behavior.
- Raw `role:guardian` and `role:donor` checks are too coarse to remain the final portal gates; later dedicated donor-portal, guardian-informational, guardian-protected, and shared-home eligibility middleware are required.
- Guardian payment initiation should move from raw `role:guardian` route gating toward protected-guardian eligibility gating, while payment return/show access should rely on dedicated payment-access authorization rather than blanket portal-role assumptions.
- Student, invoice, and receipt ownership policies remain protected-domain object checks and must not be weakened or reused as informational-access gates.
