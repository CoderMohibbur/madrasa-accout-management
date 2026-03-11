# Input

Use the docs/codex-autopilot workflow from my project.

Task: Execute the next approved step after prompt-34 completion.

Run:
`docs/codex/01-prompts/prompt-35-donor-auth-and-portal-access.md`

Prompt file baseline:

Use the docs/codex-autopilot workflow from my project.

This task is PHASE_12_DONOR_AUTH_AND_PORTAL_ACCESS_IMPLEMENTATION.
Implement only the approved donor auth and portal-access slice.

Before coding:
1) restate the exact approved slice
2) restate donor permission matrix rules
3) restate what guest-donation behavior must remain intact

Implement only:
- donor registration/login access behavior as approved
- donor portal access gating as approved
- no guardian changes
- no Google sign-in yet unless explicitly approved for this slice
- no unrelated payment refactor

End with:
- files changed
- donor auth behavior implemented
- donor portal gating implemented
- guest-donation compatibility notes
- next safe slice

Carry-forward constraints from approved prior runs:
- Phase 12 prompt-34 is complete.
- Prompt-34 completed successfully.
- No blocker is present.
- No correction pass is required.
- Preserve the approved donor payable foundation from prompt-34.
- Reuse the new `donation_intent -> payment -> donation_record` flow and the `/donate` checkout/status flow added in prompt-34.
- Keep guardian invoice payment logic intact; do not collapse donor finalization into invoice settlement behavior.
- Keep this step strictly in donor auth, portal access, and history read-path scope.
- Do not widen into claim/link or unrelated payment-domain rewrites unless prompt-35 explicitly requires it.
- Do not reopen earlier prompts unless a real contradiction is found.
- Preserve prompt-09's donor permission separation:
  - donor payment ability is separate from donor portal eligibility
  - donor login and donation do not require universal verification
  - transaction-specific receipt/status access stays narrower than full donor portal history
- Preserve prompt-12's approved slice order:
  - prompt-34: `P1` -> `P2` -> `G2` -> `A1`
  - prompt-35: `A2` -> `O1` -> `H1`
- Preserve prompt-30's shared account-state read logic, prompt-31's unified registration backend and donor draft-profile boundary, and prompt-32's separate email/phone trust axes.

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
- `docs/codex/04-decisions/approved/prompt-18-route-middleware-policy.md`
- `docs/codex/04-decisions/approved/prompt-30-account-state-read-path-adaptation.md`
- `docs/codex/04-decisions/approved/prompt-31-open-registration-foundation.md`
- `docs/codex/04-decisions/approved/prompt-32-email-phone-verification-foundation.md`
- `docs/codex/04-decisions/approved/prompt-34-donor-payable-foundation.md`
- `docs/codex/05-artifacts/business-rules/prompt-09-donor-permission-matrix.md`
- `docs/codex/05-artifacts/rollout-plans/prompt-12-donor-implementation-slices.md`
- `docs/codex/05-artifacts/test-matrices/prompt-25-test-matrix.md`
- `docs/codex/05-artifacts/workflow/prompt-34-donor-payable-foundation.md`
- `docs/codex/03-runs/phase-12/prompt-34/report.md`
- `docs/codex/03-runs/phase-12/prompt-34/decisions.md`
- `docs/codex/03-runs/phase-12/prompt-34/blockers.md`
- `docs/codex/03-runs/phase-12/prompt-34/next-step.md`
- `docs/codex/03-runs/phase-04/prompt-12/report.md`

Required process:
1. Read the required control/orchestrator docs for the codex-autopilot workflow.
2. Execute prompt-35 exactly within its allowed scope.
3. Save outputs in the correct run folder for prompt-35.
4. Promote durable decisions/artifacts if the workflow requires it.
5. Perform a contradiction / blocker pass before finalizing.
