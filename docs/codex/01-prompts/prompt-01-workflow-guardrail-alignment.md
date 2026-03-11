Use the docs/codex-autopilot workflow from my project.

This task is PHASE_0_WORKFLOW_GUARDRAIL_ALIGNMENT_ONLY.
Do not implement code yet.

Read:
1) docs/implementation-analysis-report.md
2) docs/codex-autopilot/docs/MASTER_ORCHESTRATOR_PROMPT.md
3) docs/codex-autopilot/docs/PROTECTED_PATHS_AND_ALLOWED_TOUCH.md
4) docs/codex-autopilot/docs/SYSTEM_DESIGN_AND_RULES.md
5) docs/codex-autopilot/docs/STATE_MACHINE_SPEC.md
6) docs/codex-autopilot/state/run_state.json
7) docs/codex-autopilot/state/risk_register.md
8) docs/codex-autopilot/state/validation_manifest.json

Do only this:
1) summarize the mandatory codex-autopilot workflow rules that must govern all later runs
2) identify protected paths, restricted paths, additive-first paths, and any change-control rules
3) identify any workflow prerequisites that must be satisfied before implementation starts
4) identify repo-level no-go warnings relevant to auth, roles, routes, portal work, payment work, and UI work
5) produce a safe high-level work order only

Do not:
- redesign features yet
- propose schema changes yet
- implement code
- edit unrelated files

End with:
- mandatory workflow rules
- protected path summary
- additive-first constraints
- key no-go warnings
- safe high-level work order
- whether the repository is ready for scoped analysis
