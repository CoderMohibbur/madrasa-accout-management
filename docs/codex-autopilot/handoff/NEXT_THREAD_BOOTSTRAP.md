# NEXT THREAD BOOTSTRAP

Use this file only after the Phase 5 correction checkpoint is in place and a fresh thread is about to begin.

## Bootstrap Order

1. Read `docs/implementation-analysis-report.md`
2. Read `docs/codex-autopilot/docs/MASTER_ORCHESTRATOR_PROMPT.md`
3. Read `docs/codex-autopilot/state/run_state.json`
4. Read `docs/codex-autopilot/state/validation_manifest.json`
5. Read `docs/codex-autopilot/reports/PHASE_5_REPORT.md`
6. Read `docs/codex-autopilot/state/next_phase_adjustments.json`
7. Read `docs/codex-autopilot/handoff/CURRENT_HANDOFF.md`
8. Read `docs/codex-autopilot/phases/PHASE_6_HARDENING_AND_FINAL_VERIFICATION.md` if present
9. Run preflight again
10. Confirm the safe branch is still correct and the working tree is clean
11. Confirm the recorded Phase 5 correction checkpoint is still the basis for the thread
12. Start only `PHASE_6_HARDENING_AND_FINAL_VERIFICATION`

## Required Confirmations Before Next Phase Starts
- `PHASE_5_PAYMENT_INTEGRATION` is recorded as completed.
- The Phase 5 sandbox scaffold still matches the repository.
- The corrected fail-url wiring and verification-error manual-review fallback remain in place.
- The full suite still fails only in the 14 documented auth/profile baseline areas unless new regressions are intentionally being addressed in Phase 6.
- No live shurjoPay credential activation or WordPress IPN cutover is attempted in the next thread without explicit approval.
- Donor online payments remain out of scope unless a dedicated donor payable model is introduced first.

## Current Bootstrap Target
- previous completed phase: `PHASE_5_PAYMENT_INTEGRATION`
- next phase: `PHASE_6_HARDENING_AND_FINAL_VERIFICATION`
- required branch: `codex/2026-03-08-phase-1-foundation-safety`
- required Phase 5 correction checkpoint: `acc00f9aafe35cfd461266d0ae2754603acb5273`
- human Phase 5 status: `PHASE 5 PARTIALLY COMPLETE (SANDBOX READY WITH DOCUMENTED LIMITS)`
- continue to treat live activation, live merchant-panel changes, and WordPress IPN cutover as out of scope