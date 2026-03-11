# Input

Use the docs/codex-autopilot workflow from this project.

Base prompt:

Use the docs/codex-autopilot workflow from my project.

This task is PHASE_8_SCREEN_AND_COMPONENT_MAP_ONLY.
Do not implement code yet.

Using approved UI analysis outputs, do only this:
1) list every affected screen for:
   - auth
   - guest donation
   - donor
   - guardian informational
   - guardian protected
   - public institution info
   - admission info
   - multi-role home
2) define reusable components and shared sections needed
3) identify which screens can be implemented with shared templates or layouts
4) define the minimum shared component library needed for consistency

End with:
- full screen inventory
- shared component inventory
- layout/template reuse map
- minimum shared component library

Approved adaptations and carry-forward constraints used for this run:

- Phase 08 prompt-20 is complete.
- Prompt-20 completed successfully.
- No blocker is present.
- No correction pass is required.
- Preserve the approved global UI/UX baseline from prompt-20.
- Keep this work strictly in screen-and-component mapping scope; do not drift into implementation.
- Reuse the institutional product family direction established in prompt-20.
- Let prompts 21, 22, and 42 inherit the baseline cleanly.
- Do not reopen prompt-12 through prompt-20 unless a real contradiction is found.

Control/orchestrator docs and approved scope docs read for this run:

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
- `docs/codex/03-runs/phase-08/prompt-20/report.md`
- `docs/codex/04-decisions/approved/prompt-20-global-ui-ux-baseline.md`
- `docs/codex/05-artifacts/workflow/prompt-20-global-ui-ux-baseline.md`
- `docs/codex/04-decisions/approved/prompt-11-guest-donation-onboarding.md`
- `docs/codex/04-decisions/approved/prompt-12-donor-implementation-slices.md`
- `docs/codex/04-decisions/approved/prompt-13-guardian-permission-matrix.md`
- `docs/codex/04-decisions/approved/prompt-14-admission-information-boundary.md`
- `docs/codex/04-decisions/approved/prompt-15-guardian-implementation-slices.md`
- `docs/codex/04-decisions/approved/prompt-16-multi-role-account-analysis.md`
- `docs/codex/04-decisions/approved/prompt-17-multi-role-implementation-slices.md`
- `docs/codex/04-decisions/approved/prompt-18-route-middleware-policy.md`
