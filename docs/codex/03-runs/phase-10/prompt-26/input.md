# Input

Use the docs/codex-autopilot workflow from this project.

Base prompt:

Use the docs/codex-autopilot workflow from my project.

This task is PHASE_10_ROLLOUT_AND_RISK_PLAN_ONLY.
Do not implement code yet.

Using all approved prior analysis outputs, do only this:
1) define an additive-first phased rollout plan
2) rank the highest-risk slices
3) identify rollback points
4) identify what must not ship until later
5) define readiness criteria per phase
6) identify which early phases can provide business value with the least risk

Do not implement code.

End with:
- rollout phase order
- risk ranking
- rollback points
- must-delay items
- readiness criteria per phase
- safest early-value slices

Approved adaptations and carry-forward constraints used for this run:

- Phase 10 prompt-25 is complete.
- Prompt-25 completed successfully.
- No blocker is present.
- No correction pass is required.
- Preserve the approved test matrix from prompt-25.
- Reuse the durable test-matrix artifact and approved decision summary produced by prompt-25.
- Keep this step strictly in rollout-and-risk-plan scope; do not drift into implementation.
- Respect the approved schema analysis from prompt-23 and the approved backfill/migration posture from prompt-24.
- Do not reopen prompt-12 through prompt-25 unless a real contradiction is found.

Control/orchestrator docs and approved planning inputs read for this run:

- `docs/codex/00-control/master-orchestrator.md`
- `docs/codex/00-control/execution-rules.md`
- `docs/codex/00-control/reporting-structure.md`
- `docs/implementation-analysis-report.md`
- `docs/codex-autopilot/docs/MASTER_ORCHESTRATOR_PROMPT.md`
- `docs/codex-autopilot/docs/SYSTEM_DESIGN_AND_RULES.md`
- `docs/codex-autopilot/docs/STATE_MACHINE_SPEC.md`
- `docs/codex-autopilot/docs/CHANGE_CONTROL_POLICY.md`
- `docs/codex-autopilot/docs/PROTECTED_PATHS_AND_ALLOWED_TOUCH.md`
- `docs/codex-autopilot/state/run_state.json`
- `docs/codex-autopilot/state/validation_manifest.json`
- `docs/codex/03-runs/phase-10/prompt-25/report.md`
- `docs/codex/03-runs/phase-10/prompt-25/decisions.md`
- `docs/codex/03-runs/phase-10/prompt-25/risks.md`
- `docs/codex/03-runs/phase-10/prompt-25/blockers.md`
- `docs/codex/03-runs/phase-10/prompt-25/next-step.md`
- `docs/codex/04-decisions/approved/prompt-25-test-matrix.md`
- `docs/codex/05-artifacts/test-matrices/prompt-25-test-matrix.md`
- `docs/codex/04-decisions/approved/prompt-23-schema-change-analysis.md`
- `docs/codex/04-decisions/approved/prompt-24-data-backfill-migration.md`
- `docs/codex/04-decisions/approved/prompt-18-route-middleware-policy.md`
- `docs/codex/04-decisions/approved/prompt-20-global-ui-ux-baseline.md`
- `docs/codex/04-decisions/approved/prompt-21-screen-and-component-map.md`
- `docs/codex/04-decisions/approved/prompt-22-ui-implementation-slices.md`
- `docs/codex/04-decisions/approved/prompt-12-donor-implementation-slices.md`
- `docs/codex/04-decisions/approved/prompt-15-guardian-implementation-slices.md`
- `docs/codex/04-decisions/approved/prompt-17-multi-role-implementation-slices.md`
- `docs/codex/01-prompts/prompt-27-final-implementation-planning-packet.md`
