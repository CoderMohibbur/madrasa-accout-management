# NEXT THREAD BOOTSTRAP

Use this file only after a phase has been completed or blocked and the next thread is about to start.

## Bootstrap Order

1. Read `docs/implementation-analysis-report.md`
2. Read `docs/codex-autopilot/docs/MASTER_ORCHESTRATOR_PROMPT.md`
3. Read `docs/codex-autopilot/state/run_state.json`
4. Read `docs/codex-autopilot/state/validation_manifest.json`
5. Read latest file in `docs/codex-autopilot/reports/`
6. Read `docs/codex-autopilot/state/next_phase_adjustments.json`
7. Read `docs/codex-autopilot/handoff/CURRENT_HANDOFF.md`
8. Read the upcoming phase file under `docs/codex-autopilot/phases/`
9. Run preflight again
10. Confirm safe branch is correct and working tree is clean
11. Confirm the recorded commit SHA is the correct starting point
12. Start only the next approved phase

## Required Confirmations Before Next Phase Starts
- previous phase status is `completed`
- `next_thread_required` is `true`
- no unresolved blocker prevents the next phase
- next phase assumptions match actual implemented outputs
- no missing state/report/handoff file exists
- baseline failures are known
- exact branch + exact commit SHA have been recorded

## If Any Confirmation Fails
Do not begin the next phase.
Update `run_state.json` and the upcoming phase report with the blocker.

## Current Bootstrap Target
- previous completed phase: `PHASE_4_MANAGEMENT_REPORTING`
- next approved phase: `PHASE_5_PAYMENT_INTEGRATION`
- required branch: `codex/2026-03-08-phase-1-foundation-safety`
- phase end implementation commit: `3e4588377616d35840c206def5029b18db0a00de`
- phase-end validation result: management reporting routes and tests passed; full suite remained at the 14 documented baseline auth/profile failures only
- blocker note: do not start Phase 5 until a concrete payment provider, webhook/signature verification model, and integration configuration contract exist
- do not treat the 14 documented auth/profile baseline failures as new regressions unless additional failures appear
