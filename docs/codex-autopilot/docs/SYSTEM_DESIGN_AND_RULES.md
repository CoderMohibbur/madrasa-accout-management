# SYSTEM DESIGN AND RULES — v4 PROJECT-HARDENED

## Operating Model

This kit uses a phase-driven orchestration model with strict state tracking, explicit Git safety, project-specific protected paths, and deterministic thread handoff.

Implementation loop:
1. read current state and reports
2. run preflight only
3. confirm safe branch and checkpoint metadata
4. run one phase only
5. validate
6. fix same-phase issues only
7. write phase report and manifests
8. write handoff artifacts
9. start a fresh thread
10. continue with the next phase only if approved

## High-Level Safety Principles

### 1. Minimal-change principle
Change only what is required for the current phase.

### 2. Additive-first principle
Prefer new files, new services, additive migrations, and isolated integration points before editing existing core logic.

### 3. Existing-logic protection
Do not disturb working modules unless required for the approved phase objective.

### 4. Explicit phase boundaries
No freeform implementation across multiple phases in one run.

### 5. Validation before progression
A phase cannot advance until its own changes have been checked and either passed or been explicitly blocked.

### 6. Fresh-thread continuation
Completed phases hand off to a new thread. This prevents context drift and allows clean state re-entry.

### 7. Baseline-vs-regression discipline
Do not treat already-failing tests as proof that the current phase introduced a new regression. Baseline failures must be recorded first.

### 8. Project-specific freeze zones
Accounting write semantics, report totals, auth activation behavior, and current route names are frozen unless the current phase explicitly authorizes controlled integration work.

## Sensitive Existing Behaviors

Treat these as protected unless the current phase explicitly requires integration there:

- current accounting postings
- current transaction creation/update rules
- current receipt numbering or receipt lookup patterns
- current authentication/session behavior
- admin approval semantics in login/registration
- current admin dashboards and report totals
- existing student data relationships and legacy aliases
- existing route names used by production UI
- existing shared navigation active-state logic
- existing event/listener or job side effects

## What Counts as a Safe Phase-Local Correction

A phase-local correction is allowed only when it:
- fixes an error introduced during the same phase
- completes missing wiring created during the same phase
- repairs tests or validation logic directly related to the same phase
- narrows a change to protect compatibility
- improves safety reporting for the same phase

A phase-local correction does not authorize:
- jumping into the next phase
- cleaning up legacy code beyond the phase
- renaming routes
- changing old migrations
- normalizing old accounting semantics globally unless the phase explicitly includes that work

## Required Machine-Readable and Human-Readable State

### Machine-readable
- `state/run_state.json`
- `state/change_manifest.json`
- `state/next_phase_adjustments.json`
- `state/validation_manifest.json`

### Human-readable
- `state/phase_manifest.md`
- `state/risk_register.md`
- current phase report
- handoff files

## Documentation Standards

Every phase report must clearly separate:
- objective
- scope actually implemented
- files touched
- exact branch and exact commit SHA
- validation summary
- unresolved concerns
- next phase adjustments
- final go/no-go decision

Every handoff must clearly state:
- what phase closed
- whether it is safe to continue
- exact files changed
- exact branch and commit SHA
- what next thread must read first
- what next thread must not touch casually

## Phase Order Policy

Phase order for this project is fixed unless a blocker forces formal adjustment:
1. `PHASE_1_FOUNDATION`
2. `PHASE_2_GUARDIAN_PORTAL`
3. `PHASE_3_DONOR_PORTAL`
4. `PHASE_4_MANAGEMENT_REPORTING`
5. `PHASE_5_PAYMENT_INTEGRATION`
6. `PHASE_6_HARDENING_AND_FINAL_VERIFICATION`

Portal/payment phases may not bypass Phase 1 safety deliverables.

## Protected Change Categories Requiring Extra Notes

If touched, these always require a compatibility note and rollback note:
- auth controllers/requests
- route files
- legacy transaction write controllers
- report controllers/views
- shared navigation/layout
- core financial models
- transaction migration history
