# Input

Use the docs/codex-autopilot workflow from this project.

Base prompt:

Use the docs/codex-autopilot workflow from my project.

This task is PHASE_7_EXTERNAL_ADMISSION_URL_CONFIGURATION_ANALYSIS_ONLY.
Do not implement code yet.

Read:
1) config files
2) relevant public/auth/guardian views
3) relevant routes/controllers

Do only this:
1) define the safest way to configure the external admission URL
2) define environment/config ownership
3) define validation and fallback behavior
4) define all approved UI placements for the link/button
5) define how to avoid hard-coded, duplicated, or inconsistent usage

Do not implement code.

End with:
- config design
- validation/fallback rules
- approved UI placements
- duplication-prevention rules

Approved adaptations and carry-forward constraints used for this run:

- Phase 07 prompt-18 is complete.
- Prompt-18 completed successfully.
- No blocker is present.
- No correction pass is required.
- Preserve the approved route/middleware/policy analysis from prompt-18.
- Keep donor, guardian, and multi-role work in their already-approved lanes.
- Do not reopen prompt-12 through prompt-17 unless a real contradiction is found.
- Preserve the additive route approach:
  - keep live `/guardian` protected
  - add separate informational/public entry surfaces only where approved
- Keep route-level flexibility distinct from object-level ownership/policy protection.

Control/orchestrator docs read for this run:

- `docs/codex/00-control/master-orchestrator.md`
- `docs/codex/00-control/execution-rules.md`
- `docs/codex/00-control/reporting-structure.md`
- `docs/codex-autopilot/docs/MASTER_ORCHESTRATOR_PROMPT.md`
- `docs/codex-autopilot/docs/SYSTEM_DESIGN_AND_RULES.md`
- `docs/codex-autopilot/docs/STATE_MACHINE_SPEC.md`
- `docs/codex-autopilot/docs/CHANGE_CONTROL_POLICY.md`
- `docs/codex-autopilot/docs/PROTECTED_PATHS_AND_ALLOWED_TOUCH.md`
- `docs/codex-autopilot/state/run_state.json`
- `docs/codex-autopilot/handoff/CURRENT_HANDOFF.md`
