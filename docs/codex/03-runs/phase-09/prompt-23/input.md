# Input

Use the docs/codex-autopilot workflow from this project.

Base prompt:

Use the docs/codex-autopilot workflow from my project.

This task is PHASE_9_SCHEMA_CHANGE_ANALYSIS_ONLY.
Do not implement code yet.

Using approved outputs from account-state, donor, guardian, verification, multi-role, and guest-donation analysis, do only this:
1) list all required schema changes
2) separate mandatory schema changes from optional improvements
3) identify which changes are blockers vs later enhancements
4) define migration safety concerns
5) define nullable/default strategy for additive-first rollout

Do not implement code.

End with:
- mandatory schema changes
- optional schema changes
- blocker vs later-change classification
- migration safety notes
- default/nullability strategy

Approved adaptations and carry-forward constraints used for this run:

- Phase 08 prompt-22 is complete.
- Prompt-22 completed successfully.
- No blocker is present.
- No correction pass is required.
- Preserve the approved UI implementation slice plan from prompt-22.
- Keep work inside schema-change analysis scope only; do not drift into implementation.
- Respect the approved UI map and rollout sequencing already established by prompts 20, 21, and 22.
- Do not reopen prompt-12 through prompt-22 unless a real contradiction is found.

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
- `docs/codex-autopilot/handoff/CURRENT_HANDOFF.md`
- `docs/codex/04-decisions/approved/prompt-04-account-state-model.md`
- `docs/codex/04-decisions/approved/prompt-05-role-profile-portal-linkage.md`
- `docs/codex/04-decisions/approved/prompt-06-open-registration-model.md`
- `docs/codex/04-decisions/approved/prompt-07-email-phone-verification.md`
- `docs/codex/04-decisions/approved/prompt-08-google-signin-onboarding.md`
- `docs/codex/04-decisions/approved/prompt-09-donor-permission-matrix.md`
- `docs/codex/04-decisions/approved/prompt-10-donor-payable-model.md`
- `docs/codex/04-decisions/approved/prompt-11-guest-donation-onboarding.md`
- `docs/codex/04-decisions/approved/prompt-12-donor-implementation-slices.md`
- `docs/codex/04-decisions/approved/prompt-13-guardian-permission-matrix.md`
- `docs/codex/04-decisions/approved/prompt-15-guardian-implementation-slices.md`
- `docs/codex/04-decisions/approved/prompt-16-multi-role-account-analysis.md`
- `docs/codex/04-decisions/approved/prompt-17-multi-role-implementation-slices.md`
- `docs/codex/04-decisions/approved/prompt-18-route-middleware-policy.md`
- `docs/codex/04-decisions/approved/prompt-20-global-ui-ux-baseline.md`
- `docs/codex/04-decisions/approved/prompt-21-screen-and-component-map.md`
- `docs/codex/04-decisions/approved/prompt-22-ui-implementation-slices.md`
- `docs/codex/03-runs/phase-02/prompt-04/report.md`
- `docs/codex/03-runs/phase-04/prompt-10/report.md`
- `docs/codex/03-runs/phase-04/prompt-11/report.md`
- `docs/codex/03-runs/phase-04/prompt-12/report.md`
- `docs/codex/05-artifacts/state-models/prompt-04-account-state-model.md`
