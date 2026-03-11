# Decisions

- One authenticated `users` account may safely hold both donor and guardian roles at the same time, but each role must continue to resolve into a separate derived portal context rather than one blended identity surface.
- Role membership remains domain potential only. Donor portal eligibility, guardian informational eligibility, and guardian protected eligibility must stay independently derived from role, profile lifecycle, linkage, and approved auth-state rules.
- Donor-owned data and guardian-owned data must stay isolated even when the same authenticated user can reach both contexts. A shared login does not create a shared data scope.
- `/donor` and `/guardian` remain explicit context routes. Multi-role users must switch between them explicitly rather than being auto-routed by raw role order once they are already inside a portal context.
- The safest shared home behavior is eligibility-based: one eligible context redirects directly, multiple eligible contexts land on a neutral chooser, and that chooser shows no mixed donor or guardian records.
- The smallest safe rollout version is a neutral multi-role home plus explicit donor/guardian switching for users who are already independently eligible for both live portal contexts, without changing donor data rules, guardian protected rules, or adding self-service role claiming.
- Future role expansion may later add donor plus guardian-informational coexistence and optional identity-claim/account-link flows, but those remain additive later phases and are not treated as already live behavior.
