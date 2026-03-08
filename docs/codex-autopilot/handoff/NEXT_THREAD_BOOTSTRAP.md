# NEXT THREAD BOOTSTRAP

Use this file only if a future thread is opened after the approved six-phase implementation program has already closed.

## Bootstrap Order

1. Read `docs/implementation-analysis-report.md`
2. Read `docs/codex-autopilot/docs/MASTER_ORCHESTRATOR_PROMPT.md`
3. Read `docs/codex-autopilot/state/run_state.json`
4. Read `docs/codex-autopilot/state/validation_manifest.json`
5. Read `docs/codex-autopilot/reports/PHASE_6_REPORT.md`
6. Read `docs/codex-autopilot/state/next_phase_adjustments.json`
7. Read `docs/codex-autopilot/handoff/CURRENT_HANDOFF.md`
8. Confirm that no Phase 7 exists in the approved program
9. Run preflight again
10. Confirm whether the requested work is genuinely new approved scope instead of reopening completed Phase 6 work

## Required Confirmations Before Any Future Work Starts
- `PHASE_6_HARDENING_AND_FINAL_VERIFICATION` is recorded as completed.
- The approved Phase 1-6 implementation program is already complete for sandbox-only guardian invoice payments.
- The current Laravel payment flow is sandbox-ready, not live-ready.
- The full suite still fails only in the 14 documented auth/profile baseline areas unless a future thread is intentionally addressing them.
- Live shurjoPay activation, live merchant-panel changes, WordPress IPN cutover, donor online payment finalization, and canonical posting activation are still out of scope unless newly approved and safely justified.

## Current Bootstrap Target
- previous completed phase: `PHASE_6_HARDENING_AND_FINAL_VERIFICATION`
- next approved implementation phase: `none`
- required branch: `codex/2026-03-08-phase-1-foundation-safety`
- latest validated Phase 6 checkpoint: `12c96a0b16649bfd0c574a7fb90c8aa559d7a3e3`
- human Phase 6 status: `PHASE 6 COMPLETE WITH DOCUMENTED LIMITS`
- treat any future live activation or scope expansion as a separate approval and planning event, not as an automatic continuation
