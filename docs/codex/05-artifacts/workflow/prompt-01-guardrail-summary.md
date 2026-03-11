# Prompt 01 Guardrail Summary

## Governing Rules

- One prompt at a time
- additive-first changes only
- protect existing auth, reporting, route-name, and financial behavior by default
- separate baseline failures from new regressions
- stop instead of widening scope

## Protected Areas

- auth controllers, login request, `User` model, `routes/auth.php`
- transaction write paths and the historical `transactions` migration
- reporting controllers/views and dashboard totals
- shared navigation, `routes/web.php`, and student-facing legacy compatibility surfaces

## Work Order

1. Prompt-02: inventory the live implemented auth, verification, role, donor, guardian, payment, routing, and UI surfaces.
2. Prompts 03-27: analyze and document business rules, permissions, models, schema, tests, and rollout constraints from the current state.
3. Prompts 28-43: document and validate the already-implemented repository state unless a narrowly scoped correction is explicitly required.

## Readiness

The repository is ready for scoped analysis, but not for an assumed fresh implementation kickoff: the autopilot program already shows Phase 6 completed, and `run_state.json` commit metadata trails the live branch by one autopilot-only handoff commit.
