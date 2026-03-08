# NEXT THREAD BOOTSTRAP

Use this file only after a phase has been completed or blocked and the next thread is about to start.

## Bootstrap Order

1. Read `docs/implementation-analysis-report.md`
2. Read `docs/codex-autopilot/docs/MASTER_ORCHESTRATOR_PROMPT.md`
3. Read `docs/codex-autopilot/state/run_state.json`
4. Read `docs/codex-autopilot/state/validation_manifest.json`
5. Read `docs/codex-autopilot/docs/PHASE_5_PAYMENT_PROVIDER_SPEC.md`
6. Read latest file in `docs/codex-autopilot/reports/`
7. Read `docs/codex-autopilot/state/next_phase_adjustments.json`
8. Read `docs/codex-autopilot/handoff/CURRENT_HANDOFF.md`
9. Read the upcoming phase file under `docs/codex-autopilot/phases/`
10. Run preflight again
11. Confirm safe branch is correct and working tree is clean
12. Confirm the recorded commit SHA is the correct starting point
13. Start only the next approved phase

## Required Confirmations Before Next Phase Starts
- previous phase status is `completed`
- current blocked-phase assumptions still match the new payment provider spec
- no unresolved blocker prevents the current phase from resuming
- next phase assumptions match actual implemented outputs
- no missing state/report/handoff file exists
- baseline failures are known
- exact branch + exact commit SHA have been recorded
- any doc-only worktree drift has been checkpointed before code begins

## If Any Confirmation Fails
Do not begin the next phase.
Update `run_state.json` and the upcoming phase report with the blocker.

## Current Bootstrap Target
- previous completed phase: `PHASE_4_MANAGEMENT_REPORTING`
- current blocked phase to resume: `PHASE_5_PAYMENT_INTEGRATION`
- required branch: `codex/2026-03-08-phase-1-foundation-safety`
- required committed starting checkpoint: `b28160a904d88ca22ef951b89fd50a6495b56fe3`
- payment decision note: provider choice is now `shurjopay` with `manual_bank` fallback and future-only `bkash`/`nagad`
- blocker note: do not start Phase 5 code until the shurjoPay callback/IPN verification contract, authoritative payment verification flow, receipt-number rule, and donor payable scope are confirmed
- worktree note: if the current workspace contains only the Phase 5 doc-only autopilot edits from this spec-prep pass, checkpoint them before any code implementation work
- do not treat the 14 documented auth/profile baseline failures as new regressions unless additional failures appear
