# Input

Use the docs/codex-autopilot workflow from my project.

Task: Execute the next approved step after prompt-42 completion.

Run:
`docs/codex/01-prompts/prompt-43-tests-and-rollout-readiness.md`

Important carry-forward constraints from the latest report:
- Phase 12 prompt-42 is complete.
- Prompt-42 completed successfully.
- No blocker is present.
- No correction pass is required.
- Preserve the approved final UI consistency pass from prompt-42.
- Reuse the shared `ui-*` card/stat/table/alert/form patterns now applied across auth, donor, guardian informational, and guardian protected surfaces.
- Keep prompt-41’s shared admission handoff path intact; do not reintroduce hard-coded external admission URLs into live UI surfaces.
- Treat prompt-42 changes as presentational consistency work plus the focused UI smoke test; do not reopen behavior changes unless a real contradiction is found.
- Keep this step strictly in tests-and-rollout-readiness scope.

Required process:
1. Read the required control/orchestrator docs for the codex-autopilot workflow.
2. Execute prompt-43 exactly within its allowed scope.
3. Save outputs in the correct run folder for prompt-43.
4. Promote durable decisions/artifacts if the workflow requires it.
5. Perform a contradiction / blocker pass before finalizing.

Operational adaptation used for this run:
- Validate the existing prompt-series release-candidate workspace directly.
- Treat any same-phase stale test expectation exposed by the release gate as eligible for a test-only correction.
- Do not widen scope into unrelated application behavior changes or git-hygiene cleanup.
