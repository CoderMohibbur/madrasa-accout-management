# Input

Use the docs/codex-autopilot workflow from my project.

Task: Execute the next approved step after prompt-33 completion.

Run:
`docs/codex/01-prompts/prompt-34-donor-payable-foundation.md`

Prompt file baseline:

Use the docs/codex-autopilot workflow from my project.

This task is PHASE_12_DONOR_PAYABLE_FOUNDATION_IMPLEMENTATION.
Implement only the approved donor payable foundation slice.

Before coding:
1) restate the exact approved slice
2) restate the approved donor payment-domain model
3) restate what legacy behavior must not be reused unsafely

Implement only:
- dedicated donation intent/payable/record foundation as approved
- guest donation support as approved
- identified donation support as approved
- optional donor identity capture as approved
- no unsafe direct finalization against legacy transactions
- no unrelated guardian changes
- no broad accounting redesign beyond approved separation

End with:
- files changed
- donor payable foundation implemented
- guest/identified donation behavior implemented
- legacy safety preserved
- next safe slice

Carry-forward constraints from approved prior runs:
- Phase 12 prompt-33 is complete.
- Prompt-33 completed successfully.
- No blocker is present.
- No correction pass is required.
- Preserve the approved guest donation entry implementation from prompt-33.
- Keep this step strictly in donor payable foundation scope.
- Reuse the new public `/donate` guest-entry flow and its session-draft handoff, but do not widen into donor auth/portal/history in this step.
- Preserve the approved donor slice order:
  - prompt-33 guest entry shell
  - prompt-34 donor payable foundation plus live checkout
  - prompt-35 donor auth/portal/history read paths
- Do not reopen earlier prompts unless a real contradiction is found.
- Preserve prompt-31's unified registration backend, `registered_user` compatibility role, neutral onboarding handoff, and donor/guardian draft-profile boundary.
- Preserve prompt-32's separate email and phone trust axes, audit trail, and anti-abuse controls.
- Convert the prompt-33 session draft into the approved dedicated donor payable model instead of touching legacy `transactions`.
- Keep guest and identified donation distinct, keep guest contact data unverified by default, and do not auto-create or auto-link accounts or donor profiles.

Control/orchestrator docs and approved planning inputs read for this run:
- `docs/codex/00-control/master-orchestrator.md`
- `docs/codex/00-control/execution-rules.md`
- `docs/codex/00-control/reporting-structure.md`
- `docs/codex/00-control/phase-order.md`
- `docs/implementation-analysis-report.md`
- `docs/codex-autopilot/docs/MASTER_ORCHESTRATOR_PROMPT.md`
- `docs/codex-autopilot/docs/SYSTEM_DESIGN_AND_RULES.md`
- `docs/codex-autopilot/docs/STATE_MACHINE_SPEC.md`
- `docs/codex-autopilot/docs/CHANGE_CONTROL_POLICY.md`
- `docs/codex-autopilot/docs/PROTECTED_PATHS_AND_ALLOWED_TOUCH.md`
- `docs/codex-autopilot/state/run_state.json`
- `docs/codex-autopilot/handoff/CURRENT_HANDOFF.md`
- `docs/codex/04-decisions/approved/prompt-09-donor-permission-matrix.md`
- `docs/codex/04-decisions/approved/prompt-10-donor-payable-model.md`
- `docs/codex/04-decisions/approved/prompt-11-guest-donation-onboarding.md`
- `docs/codex/04-decisions/approved/prompt-12-donor-implementation-slices.md`
- `docs/codex/04-decisions/approved/prompt-22-ui-implementation-slices.md`
- `docs/codex/04-decisions/approved/prompt-33-guest-donation-entry.md`
- `docs/codex/03-runs/phase-12/prompt-28/report.md`
- `docs/codex/03-runs/phase-12/prompt-28/decisions.md`
- `docs/codex/03-runs/phase-12/prompt-33/report.md`
- `docs/codex/03-runs/phase-12/prompt-33/decisions.md`
- `docs/codex/03-runs/phase-12/prompt-33/blockers.md`
- `docs/codex/03-runs/phase-12/prompt-33/next-step.md`
- `docs/codex/03-runs/phase-04/prompt-10/report.md`
- `docs/codex/03-runs/phase-04/prompt-11/report.md`
- `docs/codex/03-runs/phase-04/prompt-12/report.md`
- `docs/codex/03-runs/phase-09/prompt-23/report.md`
- `docs/codex/05-artifacts/test-matrices/prompt-25-test-matrix.md`
- `docs/codex/05-artifacts/workflow/prompt-33-guest-donation-entry.md`

Required process:
1. Read the required control/orchestrator docs for the codex-autopilot workflow.
2. Execute prompt-34 exactly within its allowed scope.
3. Save outputs in the correct run folder for prompt-34.
4. Promote durable decisions/artifacts if the workflow requires it.
5. Perform a contradiction / blocker pass before finalizing.
