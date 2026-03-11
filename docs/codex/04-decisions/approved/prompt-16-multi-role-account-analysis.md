# Prompt 16 Multi-Role Account Analysis

Approved baseline decisions from prompt-16:

- One authenticated `users` account may safely hold both donor and guardian roles at the same time.
- Role membership remains domain potential only; donor portal eligibility, guardian informational eligibility, and guardian protected eligibility must stay separately derived.
- Donor-owned data and guardian-owned data remain isolated even when the same logged-in user can reach both contexts.
- `/donor` and `/guardian` remain explicit context routes. Multi-role users should switch explicitly between eligible contexts instead of being routed by raw guardian-first role ordering.
- Shared home behavior must derive from eligible contexts: one eligible context redirects directly, multiple eligible contexts land on a neutral chooser, and that chooser must not show mixed donor or guardian records.
- The smallest safe multi-role rollout is a home-and-switching layer for independently eligible contexts only; it does not merge dashboards, auto-link identities, reopen donor boundaries, or weaken protected guardian gating.
- Future donor-plus-guardian-informational coexistence and any optional identity-claim/account-link flow remain later additive work and are not treated as already implemented behavior.
