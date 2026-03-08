# CURRENT HANDOFF

## Current Status
Phase 5 payment spec-preparation artifacts are now written. `PHASE_5_PAYMENT_INTEGRATION` remains blocked for code implementation because the provider choice is no longer the blocker, but the callback/IPN security contract and donor payable scope are still unresolved.

## Latest Safe Position
- last completed phase: `PHASE_4_MANAGEMENT_REPORTING`
- current blocked phase: `PHASE_5_PAYMENT_INTEGRATION`
- current thread id: `thread-006-phase-5-payment-spec-preparation`
- workflow status: `blocked`
- active branch during spec prep: `codex/2026-03-08-phase-1-foundation-safety`
- current committed checkpoint: `b28160a904d88ca22ef951b89fd50a6495b56fe3`
- doc-only worktree note: only autopilot docs/state/handoff/report files were changed in this task; no application code or env files were touched

## Resolved By This Thread
- The project now has a concrete provider decision:
  - primary online gateway: `shurjopay`
  - current offline/manual fallback: `manual_bank`
  - future-only providers: `bkash`, `nagad`
- Sandbox-first behavior is now explicitly recorded.
- Live credentials are now treated as documented but inactive inputs.
- The exact future env/config-key contract is now documented without storing secret values in tracked files.
- The strict provider specification is now recorded in `docs/codex-autopilot/docs/PHASE_5_PAYMENT_PROVIDER_SPEC.md`.

## Unresolved Live Runtime Blockers
- The exact shurjoPay callback/IPN verification method is still not confirmed from provider documentation accepted by the project.
- The exact shurjoPay callback/IPN payload contract, stable event identifier, and authoritative verification flow are still unconfirmed.
- Dedicated receipt-number generation rules for the new `receipts` register are still unconfirmed.
- Donor self-service online payments are still unsafe to implement against legacy `transactions` because the repository lacks a dedicated donor payable model.
- Manual-bank evidence storage details are still unresolved if uploads are required.

## Next Phase May Start?
No. The Phase 5 spec package is complete, but `PHASE_5_PAYMENT_INTEGRATION` code must remain blocked until the unresolved items above are confirmed and the doc-only worktree changes are checkpointed before any code work begins.
