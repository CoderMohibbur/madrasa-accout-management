# Input

Use the docs/codex-autopilot workflow from this project.

Base prompt:

Use the docs/codex-autopilot workflow from my project.

This task is PHASE_9_DATA_BACKFILL_AND_MIGRATION_ANALYSIS_ONLY.
Do not implement code yet.

Using approved schema and state-model outputs, do only this:
1) identify which existing records require interpretation or backfill
2) identify risks around overloaded email_verified_at semantics
3) identify risks around donor/guardian portal flags and profile state
4) define the safest backfill order
5) define rollback-safe migration strategy
6) define what must be migrated before rollout vs what can be deferred

Do not implement code.

End with:
- backfill targets
- interpretation risks
- safe migration order
- rollback-safe migration strategy
- deferable vs non-deferable migration work

Approved adaptations and carry-forward constraints used for this run:

- Phase 09 prompt-23 is complete.
- Prompt-23 completed successfully.
- No blocker is present.
- No correction pass is required.
- Preserve the approved schema change analysis from prompt-23.
- Keep this work strictly in data backfill and migration planning scope; do not drift into implementation beyond the prompt's allowed analysis and migration definition.
- Ensure prompt-24 inherits the schema plan cleanly.
- Respect the approved UI sequencing and earlier donor/guardian/multi-role boundaries already established.
- Do not reopen prompt-12 through prompt-23 unless a real contradiction is found.

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
- `docs/codex/03-runs/phase-09/prompt-23/report.md`
- `docs/codex/04-decisions/approved/prompt-23-schema-change-analysis.md`
- `docs/codex/05-artifacts/schemas/prompt-23-schema-change-analysis.md`
- `docs/codex/03-runs/phase-00/prompt-02/report.md`
- `docs/codex/03-runs/phase-02/prompt-04/report.md`
- `docs/codex/03-runs/phase-04/prompt-12/report.md`
- `docs/codex/04-decisions/approved/prompt-04-account-state-model.md`
- `docs/codex/04-decisions/approved/prompt-05-role-profile-portal-linkage.md`
- `docs/codex/04-decisions/approved/prompt-07-email-phone-verification.md`
- `docs/codex/04-decisions/approved/prompt-12-donor-implementation-slices.md`
- `docs/codex/04-decisions/approved/prompt-13-guardian-permission-matrix.md`
- `docs/codex/04-decisions/approved/prompt-15-guardian-implementation-slices.md`
- `docs/codex/04-decisions/approved/prompt-16-multi-role-account-analysis.md`
- `docs/codex/04-decisions/approved/prompt-18-route-middleware-policy.md`
- `app/Http/Requests/Auth/LoginRequest.php`
- `app/Http/Controllers/Auth/RegisteredUserController.php`
- `app/Services/DonorPortal/DonorPortalData.php`
- `app/Services/GuardianPortal/GuardianPortalData.php`
- `app/Policies/StudentPolicy.php`
- `app/Policies/StudentFeeInvoicePolicy.php`
- `app/Http/Middleware/RedirectPortalUsersFromLegacyDashboard.php`
