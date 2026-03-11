# Input

Use the docs/codex-autopilot workflow from this project.

Base prompt:

Use the docs/codex-autopilot workflow from my project.

This task is PHASE_11_FINAL_IMPLEMENTATION_PLANNING_PACKET_ONLY.
Do not implement code yet.

Using all previously approved outputs from earlier phases, do only this:
1) consolidate the final business rules
2) consolidate the final technical architecture
3) consolidate the final account-state model
4) consolidate the final donor model, including guest donation
5) consolidate the final guardian informational vs protected model
6) consolidate the final multi-role model
7) consolidate the final route/middleware/policy plan
8) consolidate the final global UI/UX baseline
9) consolidate the final schema/migration plan
10) consolidate the final testing plan
11) consolidate the final rollout order
12) consolidate final no-go warnings

Do not implement code.

End with:
- final business rules
- final technical architecture
- final account-state model
- final donor model
- final guardian model
- final multi-role model
- final routing/policy plan
- final global UI/UX direction
- final schema/migration plan
- exact implementation phase order
- exact no-go warnings
- whether implementation may begin

Approved adaptations and carry-forward constraints used for this run:

- Phase 10 prompt-26 is complete.
- Prompt-26 completed successfully.
- No blocker is present.
- No correction pass is required.
- Preserve the approved rollout and risk plan from prompt-26.
- Keep the phased rollout order, risk ranking, rollback checkpoints, must-delay items, readiness gates, and lowest-risk early-value slices intact unless a real contradiction is found.
- Respect prompt-23 schema limits, prompt-24's classify-first migration posture, and prompt-25's approved test matrix.
- Keep this step strictly in final implementation planning packet scope; do not drift into implementation.
- Do not reopen prompt-12 through prompt-26 unless a real contradiction is found.

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
- `docs/codex/04-decisions/approved/prompt-01-workflow-guardrail-baseline.md`
- `docs/codex/04-decisions/approved/prompt-02-current-system-baseline.md`
- `docs/codex/04-decisions/approved/prompt-03-business-rule-freeze.md`
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
- `docs/codex/04-decisions/approved/prompt-14-admission-information-boundary.md`
- `docs/codex/04-decisions/approved/prompt-15-guardian-implementation-slices.md`
- `docs/codex/04-decisions/approved/prompt-16-multi-role-account-analysis.md`
- `docs/codex/04-decisions/approved/prompt-17-multi-role-implementation-slices.md`
- `docs/codex/04-decisions/approved/prompt-18-route-middleware-policy.md`
- `docs/codex/04-decisions/approved/prompt-19-external-admission-url-config.md`
- `docs/codex/04-decisions/approved/prompt-20-global-ui-ux-baseline.md`
- `docs/codex/04-decisions/approved/prompt-21-screen-and-component-map.md`
- `docs/codex/04-decisions/approved/prompt-22-ui-implementation-slices.md`
- `docs/codex/04-decisions/approved/prompt-23-schema-change-analysis.md`
- `docs/codex/04-decisions/approved/prompt-24-data-backfill-migration.md`
- `docs/codex/04-decisions/approved/prompt-25-test-matrix.md`
- `docs/codex/04-decisions/approved/prompt-26-rollout-and-risk-plan.md`
- `docs/codex/05-artifacts/test-matrices/prompt-25-test-matrix.md`
- `docs/codex/05-artifacts/rollout-plans/prompt-26-rollout-and-risk-plan.md`
- `docs/codex/01-prompts/prompt-28-shared-ui-foundation-implementation.md`
