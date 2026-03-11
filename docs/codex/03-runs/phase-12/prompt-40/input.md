# Input

Use the docs/codex-autopilot workflow from my project.

This task is PHASE_12_ROUTE_MIDDLEWARE_POLICY_FINALIZATION_IMPLEMENTATION.
Implement only the approved route/middleware/policy finalization slice.

Before coding:
1) restate the exact approved slice
2) restate compatibility rules for legacy management routes
3) restate no-go warnings for route naming and access regression

Implement only:
- approved route structure changes
- approved middleware updates
- approved policy updates
- preserve existing management route names and behaviors unless explicitly approved otherwise

Carry forward for this run:
- phase-12 prompt-39 is complete, successful, and not blocked
- preserve the approved prompt-39 shared eligible-context resolver, neutral `/dashboard` chooser, and additive donor/guardian switching behavior
- keep donor switch affordances and chooser links limited to already-eligible homes only
- keep donor no-portal vs portal eligibility separation intact
- keep guardian informational vs protected guardian separation intact
- keep protected guardian linkage, invoice ownership, receipt ownership, and payment-entry gating intact
- keep legacy management dashboard route names and behaviors intact
- finish only `MR4` final route/middleware/policy alignment; do not reopen `MR1` through `MR3` unless a real contradiction is found
- do not rename `dashboard`, `donor.*`, `guardian.*`, `guardian.info.*`, `payments.*`, or `management.*`

Control/orchestrator and approved planning inputs read for this run:
- `docs/codex/00-control/master-orchestrator.md`
- `docs/codex/00-control/execution-rules.md`
- `docs/codex/00-control/reporting-structure.md`
- `docs/codex-autopilot/README_FIRST.txt`
- `docs/codex-autopilot/docs/MASTER_ORCHESTRATOR_PROMPT.md`
- `docs/codex-autopilot/docs/PROTECTED_PATHS_AND_ALLOWED_TOUCH.md`
- `docs/codex-autopilot/state/run_state.json`
- `docs/codex-autopilot/handoff/CURRENT_HANDOFF.md`
- `docs/codex/03-runs/phase-06/prompt-17/report.md`
- `docs/codex/03-runs/phase-07/prompt-18/report.md`
- `docs/codex/05-artifacts/route-maps/prompt-18-route-middleware-policy.md`
- `docs/codex/05-artifacts/test-matrices/prompt-25-test-matrix.md`
- `docs/codex/05-artifacts/rollout-plans/prompt-26-rollout-and-risk-plan.md`
- `docs/codex/04-decisions/approved/prompt-27-final-implementation-planning-packet.md`
- `docs/codex/03-runs/phase-12/prompt-39/report.md`
- `docs/codex/03-runs/phase-12/prompt-39/decisions.md`
- `docs/codex/03-runs/phase-12/prompt-39/blockers.md`
- `docs/codex/03-runs/phase-12/prompt-39/next-step.md`
- `docs/codex/04-decisions/approved/prompt-39-multi-role-home-and-switching.md`

End with:
- files changed
- routing/middleware/policy changes implemented
- compatibility notes
- regression-risk notes
- next safe slice
