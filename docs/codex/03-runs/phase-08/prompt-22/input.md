# Input

Use the docs/codex-autopilot workflow from this project.

Base prompt:

Use the docs/codex-autopilot workflow from my project.

This task is PHASE_8_UI_IMPLEMENTATION_SLICE_PLANNING_ONLY.
Do not implement code yet.

Using approved UI outputs, do only this:
1) break UI work into the smallest safe implementation slices
2) prioritize shared layouts, shared components, and shared feedback patterns before feature pages
3) separate structural UI foundation work from feature-specific page work
4) identify which UI slices can ship independently
5) identify rollback-safe checkpoints

End with:
- UI slice order
- shared-foundation-first sequence
- feature-page sequence
- independently shippable UI slices
- rollback checkpoints

Approved adaptations and carry-forward constraints used for this run:

- Phase 08 prompt-21 is complete.
- Prompt-21 completed successfully.
- No blocker is present.
- No correction pass is required.
- Preserve the approved screen-and-component map from prompt-21.
- Reuse the full screen inventory, shared component inventory, and layout reuse map produced by prompt-21.
- Keep this step strictly in UI implementation slice planning scope; do not drift into implementation.
- Ensure prompts 22 and 42 can inherit the durable UI map artifact cleanly.
- Do not reopen prompt-12 through prompt-21 unless a real contradiction is found.

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
- `docs/codex/03-runs/phase-08/prompt-20/report.md`
- `docs/codex/03-runs/phase-08/prompt-21/report.md`
- `docs/codex/04-decisions/approved/prompt-20-global-ui-ux-baseline.md`
- `docs/codex/04-decisions/approved/prompt-21-screen-and-component-map.md`
- `docs/codex/05-artifacts/workflow/prompt-20-global-ui-ux-baseline.md`
- `docs/codex/05-artifacts/ui-maps/prompt-21-screen-and-component-map.md`
- `docs/codex/04-decisions/approved/prompt-12-donor-implementation-slices.md`
- `docs/codex/04-decisions/approved/prompt-15-guardian-implementation-slices.md`
- `docs/codex/04-decisions/approved/prompt-16-multi-role-account-analysis.md`
- `docs/codex/04-decisions/approved/prompt-17-multi-role-implementation-slices.md`
- `docs/codex/04-decisions/approved/prompt-18-route-middleware-policy.md`
