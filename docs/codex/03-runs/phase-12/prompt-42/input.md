# Input

## Requested Run

Use the docs/codex-autopilot workflow from my project.

Task: Execute the next approved step after prompt-41 completion.

Run:
`docs/codex/01-prompts/prompt-42-final-ui-consistency-pass.md`

Important carry-forward constraints from the latest report:
- Phase 12 prompt-41 is complete.
- Prompt-41 completed successfully.
- No blocker is present.
- No correction pass is required.
- Preserve the approved external admission URL implementation from prompt-41.
- Reuse the shared `ExternalAdmissionUrlResolver` and the internal public admission information surface added in prompt-41.
- Keep guardian informational admission and public admission on the same shared handoff/component path.
- Do not reintroduce hard-coded external admission URLs into live app surfaces.
- Keep this step strictly in final UI consistency pass scope.
- Do not reopen earlier prompts unless a real contradiction is found.

Required process:
1. Read the required control/orchestrator docs for the codex-autopilot workflow.
2. Execute prompt-42 exactly within its allowed scope.
3. Save outputs in the correct run folder for prompt-42.
4. Promote durable decisions/artifacts if the workflow requires it.
5. Perform a contradiction / blocker pass before finalizing.

## Prompt Text Used

Use the docs/codex-autopilot workflow from my project.

This task is `PHASE_12_FINAL_UI_CONSISTENCY_PASS_IMPLEMENTATION`.
Implement only the approved UI consistency pass slice.

Before coding:
1) restate the exact approved slice
2) restate global UI/UX rules
3) restate which surfaces are in scope

Implement only:
- consistency corrections across already-implemented affected screens
- spacing/typography/button/form/feedback alignment
- no new feature behavior
- no business-logic changes

End with:
- files changed
- UI consistency improvements made
- affected surfaces
- remaining visual debt if any
- next safe slice
