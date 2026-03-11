# Input

Use the docs/codex-autopilot workflow from my project.

Task: Execute the next approved step after prompt-16 completion.

Run:
`docs/codex/01-prompts/prompt-17-multi-role-implementation-slices.md`

Important carry-forward constraints from the latest report:
- Phase 06 prompt-16 is complete.
- Prompt-16 completed successfully.
- No blocker is present.
- No correction pass is required.
- Preserve the approved donor and guardian slice-planning boundaries already established.
- Preserve the approved multi-role account analysis conclusions from prompt-16.
- Do not reopen prompt-12 through prompt-15 unless a real contradiction is found.
- Keep this step tightly scoped to multi-role implementation slice planning only.

Required process:
1. Read the required control/orchestrator docs for the codex-autopilot workflow.
2. Execute prompt-17 exactly within its allowed scope.
3. Save outputs in the correct run folder for prompt-17.
4. Promote durable decisions/artifacts if the workflow requires it.
5. Perform a contradiction / blocker pass before finalizing.

Prompt file executed:

Use the docs/codex-autopilot workflow from my project.

This task is PHASE_6_MULTI_ROLE_IMPLEMENTATION_SLICE_PLANNING_ONLY.
Do not implement code yet.

Using approved multi-role analysis outputs, do only this:
1) break multi-role support into the smallest safe implementation slices
2) identify dependencies on auth, donor, guardian, routing, and UI work
3) define the earliest safe phase where multi-role can be introduced
4) identify rollback-safe checkpoints

End with:
- multi-role slice order
- dependencies
- earliest safe introduction phase
- rollback checkpoints
