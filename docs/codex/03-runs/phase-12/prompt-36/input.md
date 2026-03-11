# Input

Use the docs/codex-autopilot workflow from my project.

Task: Execute the next approved step after prompt-35 completion.

Run:
docs/codex/01-prompts/prompt-36-guardian-informational-portal.md

Important carry-forward constraints from the latest report:
- Phase 12 prompt-35 is complete.
- Prompt-35 completed successfully.
- No blocker is present.
- No correction pass is required.
- Preserve the approved donor auth and portal access implementation from prompt-35.
- Keep donor access derived from donor context and portal eligibility, not from the old verified + role:donor coupling.
- Reuse the donor portal read-path bridge for donation_record history, but do not reopen donor checkout or guardian invoice behavior.
- Keep this step strictly in guardian informational portal scope.
- Do not reopen earlier prompts unless a real contradiction is found.

Required process:
1) Read the required control/orchestrator docs for the codex-autopilot workflow.
2) Execute prompt-36 exactly within its allowed scope.
3) Save outputs in the correct run folder for prompt-36.
4) Promote durable decisions/artifacts if the workflow requires it.
5) Perform a contradiction / blocker pass before finalizing.

Prompt file content used:

Use the docs/codex-autopilot workflow from my project.

This task is PHASE_12_GUARDIAN_INFORMATIONAL_PORTAL_IMPLEMENTATION.
Implement only the approved light guardian informational portal slice.

Before coding:
1) restate the exact approved slice
2) restate guardian permission matrix rules for informational access
3) restate what protected boundaries must remain untouched

Implement only:
- non-sensitive institution information surfaces
- admission-related information surfaces
- external application link/button surfaces
- authenticated guardian informational access behavior for unverified or unlinked states as approved
- no protected student/invoice/payment-sensitive access changes

End with:
- files changed
- informational guardian behavior implemented
- protected boundaries preserved
- next safe slice
