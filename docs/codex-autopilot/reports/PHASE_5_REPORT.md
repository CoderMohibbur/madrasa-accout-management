# PHASE 5 REPORT

## Phase
PHASE_5_PAYMENT_INTEGRATION

## Status
- blocked for code implementation
- payment spec preparation complete

## Objective
Prepare the strict Phase 5 payment provider decision package and determine whether the project is now safe to begin payment-integration code.

## Scope Actually Implemented
- Reviewed the existing Phase 5 blocker notes against the now-approved provider decision.
- Created `docs/codex-autopilot/docs/PHASE_5_PAYMENT_PROVIDER_SPEC.md` to document the current payment-provider contract for `shurjopay`, `manual_bank`, and future-only `bkash`/`nagad`.
- Defined the later env/config-key contract without writing any secret values into tracked config or `.env` files.
- Updated the Phase 5 report, state, risk, and handoff artifacts to reflect what is now resolved and what is still unsafe to assume.
- Did not modify application code, routes, tests, views, config, `.env`, or existing financial logic.

## Branch / Commit
- safe branch: `codex/2026-03-08-phase-1-foundation-safety`
- phase start committed checkpoint: `b28160a904d88ca22ef951b89fd50a6495b56fe3`
- current committed checkpoint: `b28160a904d88ca22ef951b89fd50a6495b56fe3`
- note: this task leaves only autopilot docs/state/handoff/report files modified in the worktree; no new checkpoint commit was created in this task

## Blocker Review

### Resolved By The Provider Decision
- A concrete primary online provider now exists: `shurjopay`
- Current offline/manual fallback now exists: `manual_bank`
- Sandbox-first behavior is explicitly approved
- Live credentials are present as operator-owned inputs but must remain inactive
- Base URLs and merchant-side credential categories are now known
- Merchant order prefix is now known: `HFS`
- Future provider scope is constrained to documentation only: `bkash`, `nagad`

### Still Missing / Still Blocking Code
- Exact shurjoPay callback/IPN verification contract remains unconfirmed
- Exact signature/secret model remains unconfirmed
- Exact success/fail/cancel return payload contract remains unconfirmed
- Authoritative provider-side payment verification flow remains unconfirmed
- Dedicated receipt-number generation rule remains unconfirmed
- Donor live payment scope remains unresolved because the repository still lacks a dedicated donation payable model
- Manual-bank evidence storage details remain unresolved if uploads are required

## Files Touched
- New files:
  - `docs/codex-autopilot/docs/PHASE_5_PAYMENT_PROVIDER_SPEC.md`
- Existing files modified:
  - `docs/codex-autopilot/reports/PHASE_5_REPORT.md`
  - `docs/codex-autopilot/handoff/CURRENT_HANDOFF.md`
  - `docs/codex-autopilot/handoff/NEXT_THREAD_BOOTSTRAP.md`
  - `docs/codex-autopilot/handoff/THREAD_HISTORY_INDEX.md`
  - `docs/codex-autopilot/state/run_state.json`
  - `docs/codex-autopilot/state/next_phase_adjustments.json`
  - `docs/codex-autopilot/state/phase_manifest.md`
  - `docs/codex-autopilot/state/risk_register.md`
  - `docs/codex-autopilot/state/change_manifest.json`

## Validation Performed
- Confirmed the working tree was clean before this docs-only task started.
- Confirmed the required safe branch remained active.
- Re-read the current `payments`, `payment_gateway_events`, `receipts`, and canonical posting surfaces so the spec stays aligned with the repository.
- Parsed the updated JSON state files after editing.

## Validation Result
- partial pass
- documentation package is internally defined
- implementation remains blocked because security-critical provider details are still unresolved

## Required Inputs Before Code May Start
- The exact shurjoPay callback/IPN verification method accepted by the project
- The exact shurjoPay callback/IPN payload contract and authoritative event identifier
- The exact provider-side payment verification step for final paid confirmation
- The receipt-number generation rule for dedicated `receipts`
- The donor payable-model decision, or a formal decision to scope initial Phase 5 code to invoice payments only
- Manual-bank evidence storage and retention details if the flow requires uploads
- Explicit live go/no-go confirmation before any live credential activation

## Go / No-Go Decision
NO-GO for `PHASE_5_PAYMENT_INTEGRATION` code.

`PHASE 5 SPEC PREP COMPLETE`
