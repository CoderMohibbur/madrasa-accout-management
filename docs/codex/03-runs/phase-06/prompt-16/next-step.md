# Next Step

Run `docs/codex/01-prompts/prompt-17-multi-role-implementation-slices.md` next.

Carry forward these prompt-16 decisions:

- keep one shared authenticated `users` account model while allowing additive donor and guardian role membership on the same account
- derive donor portal, guardian informational portal, and guardian protected portal eligibility separately instead of collapsing them into one raw role result
- keep donor-owned data and guardian-owned data isolated even for the same logged-in user
- treat `/donor` and `/guardian` as explicit context routes with eligibility-based switching, not guardian-first redirect ordering
- make the shared multi-role home a neutral chooser only when more than one context is eligible, with no mixed-scope dashboard data
- keep the smallest safe rollout limited to home and switching behavior for independently eligible contexts before any later self-service claim or account-link flow
