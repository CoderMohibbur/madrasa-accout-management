# Input

Use the docs/codex-autopilot workflow from my project.

Task: Execute the next approved step after prompt-30 completion.

Run:
docs/codex/01-prompts/prompt-31-open-registration-foundation.md

Important carry-forward constraints from the latest report:
- Phase 12 prompt-30 is complete.
- Prompt-30 completed successfully.
- No blocker is present.
- No correction pass is required.
- Preserve the approved account-state read-path adaptation from prompt-30.
- Reuse the derived account-state reads now exposed on User.
- Keep login, role checks, management checks, and portal-entry checks aligned with the new account-state reads.
- Do not remove blanket verified behavior beyond the exact scope already adapted unless prompt-31 explicitly requires it.
- Donor/guardian no-portal rollout and broader gating changes are still deferred; do not pull them forward accidentally.
- Keep this step strictly in open-registration foundation scope.
- Do not reopen earlier prompts unless a real contradiction is found.

Required process:
1) Read the required control/orchestrator docs for the codex-autopilot workflow.
2) Execute prompt-31 exactly within its allowed scope.
3) Save outputs in the correct run folder for prompt-31.
4) Promote durable decisions/artifacts if the workflow requires it.
5) Perform a contradiction / blocker pass before finalizing.

At the end, report only:
- current stage
- whether prompt-31 completed successfully
- whether any blocker or correction is required
- the exact next prompt to run
