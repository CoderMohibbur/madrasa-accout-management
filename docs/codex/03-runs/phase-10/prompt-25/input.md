# Input

Use the docs/codex-autopilot workflow from this project.

Base prompt:

Use the docs/codex-autopilot workflow from my project.

This task is PHASE_10_TEST_MATRIX_ANALYSIS_ONLY.
Do not implement code yet.

Using all approved prior analysis outputs, do only this:
1) define required test coverage for:
   - auth
   - registration
   - email verification
   - phone verification
   - guest donation
   - donor portal
   - guardian informational portal
   - guardian protected portal
   - multi-role behavior
   - payment flow
   - Google sign-in
   - route and middleware behavior
   - policy/authorization boundaries
   - UI consistency smoke checks
2) define high-risk regression tests
3) define rollout-blocking test cases
4) define the minimum test pack required before each rollout phase

Do not implement code.

End with:
- full test matrix
- high-risk regression list
- rollout blockers
- minimum test pack per phase

Approved adaptations and carry-forward constraints used for this run:

- Phase 09 prompt-24 is complete.
- Prompt-24 completed successfully.
- No blocker is present.
- No correction pass is required.
- Preserve the approved data backfill and migration analysis from prompt-24.
- Keep the approved schema plan from prompt-23 and the backfill/migration sequencing from prompt-24.
- Keep the "classify first, mutate later" posture for legacy rows and do not guess on ambiguous identity/linkage records.
- Keep this step strictly in test-matrix scope; do not drift into implementation.
- Do not reopen prompt-12 through prompt-24 unless a real contradiction is found.

Control/orchestrator docs and approved planning inputs read for this run:

- `docs/codex/00-control/master-orchestrator.md`
- `docs/codex/00-control/execution-rules.md`
- `docs/codex/00-control/reporting-structure.md`
- `docs/codex/00-control/phase-order.md`
- `docs/implementation-analysis-report.md`
- `docs/codex-autopilot/README_FIRST.txt`
- `docs/codex-autopilot/docs/MASTER_ORCHESTRATOR_PROMPT.md`
- `docs/codex-autopilot/docs/SYSTEM_DESIGN_AND_RULES.md`
- `docs/codex-autopilot/docs/STATE_MACHINE_SPEC.md`
- `docs/codex-autopilot/docs/CHANGE_CONTROL_POLICY.md`
- `docs/codex-autopilot/docs/PROTECTED_PATHS_AND_ALLOWED_TOUCH.md`
- `docs/codex-autopilot/state/run_state.json`
- `docs/codex-autopilot/state/validation_manifest.json`
- `docs/codex-autopilot/handoff/CURRENT_HANDOFF.md`
- `docs/codex/03-runs/phase-09/prompt-24/report.md`
- `docs/codex/03-runs/phase-09/prompt-24/decisions.md`
- `docs/codex/03-runs/phase-09/prompt-24/risks.md`
- `docs/codex/03-runs/phase-09/prompt-24/blockers.md`
- `docs/codex/03-runs/phase-09/prompt-24/next-step.md`
- `docs/codex/04-decisions/approved/prompt-23-schema-change-analysis.md`
- `docs/codex/04-decisions/approved/prompt-24-data-backfill-migration.md`
- `docs/codex/05-artifacts/state-models/prompt-04-account-state-model.md`
- `docs/codex/05-artifacts/business-rules/prompt-06-open-registration-model.md`
- `docs/codex/05-artifacts/business-rules/prompt-07-email-phone-verification.md`
- `docs/codex/05-artifacts/business-rules/prompt-08-google-signin-onboarding.md`
- `docs/codex/05-artifacts/business-rules/prompt-09-donor-permission-matrix.md`
- `docs/codex/05-artifacts/business-rules/prompt-10-donor-payable-model.md`
- `docs/codex/05-artifacts/business-rules/prompt-11-guest-donation-onboarding.md`
- `docs/codex/05-artifacts/business-rules/prompt-13-guardian-permission-matrix.md`
- `docs/codex/05-artifacts/business-rules/prompt-14-admission-information-boundary.md`
- `docs/codex/05-artifacts/business-rules/prompt-16-multi-role-account-analysis.md`
- `docs/codex/05-artifacts/route-maps/prompt-18-route-middleware-policy.md`
- `docs/codex/05-artifacts/workflow/prompt-20-global-ui-ux-baseline.md`
- `docs/codex/05-artifacts/ui-maps/prompt-21-screen-and-component-map.md`
- `app/Http/Requests/Auth/LoginRequest.php`
- `app/Http/Controllers/Auth/RegisteredUserController.php`
- `app/Http/Middleware/RedirectPortalUsersFromLegacyDashboard.php`
- `app/Policies/StudentPolicy.php`
- `app/Policies/StudentFeeInvoicePolicy.php`
