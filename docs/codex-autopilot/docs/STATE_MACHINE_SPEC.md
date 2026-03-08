# STATE MACHINE SPECIFICATION — v4 PROJECT-HARDENED

## Purpose

This specification defines how the autopilot tracks progress, validates a phase, blocks unsafe progression, records Git checkpoints, and prepares a clean thread handoff.

## Source of Truth

Primary machine-readable state:
- `docs/codex-autopilot/state/run_state.json`

Supporting state:
- `docs/codex-autopilot/state/change_manifest.json`
- `docs/codex-autopilot/state/next_phase_adjustments.json`
- `docs/codex-autopilot/state/validation_manifest.json`
- `docs/codex-autopilot/state/phase_manifest.md`
- `docs/codex-autopilot/state/risk_register.md`

## Core State Fields for `run_state.json`

Required structure:
- `project_name`
- `implementation_series_id`
- `base_branch`
- `safe_branch`
- `workflow_status`
- `current_phase`
- `current_phase_status`
- `completed_phases`
- `blocked_phases`
- `last_completed_phase`
- `last_validation_result`
- `current_thread_id`
- `next_thread_required`
- `blocker_present`
- `blocker_summary`
- `last_updated`
- `final_verification_status`
- `git_runtime`
- `baseline_validation`
- `phase_gate_notes`

## Explicit Workflow Status Discipline

Keep `run_state.json.workflow_status` current with an explicit stage-specific value such as:
- `preflight_in_progress`
- `preflight_passed`
- `preflight_blocked`
- `phase_1_in_progress`
- `phase_1_complete`
- `phase_2_in_progress`
- `phase_2_complete`
- `blocked`

`current_phase_status` may continue to use the generic legal status values below, but `workflow_status` should reflect the live runtime position without requiring inference.

## Legal Status Values

- `pending`
- `in_progress`
- `validating`
- `correction_required`
- `completed`
- `blocked`
- `deferred`

## Preflight Self-Repair Rule

If preflight dirties only autopilot-owned files under:
- `docs/codex-autopilot/state/*`
- `docs/codex-autopilot/handoff/*`
- `docs/codex-autopilot/reports/*`
- `docs/codex-autopilot/docs/*`
- `docs/codex-autopilot/templates/*`

then the runtime must:
1. classify the issue as autopilot/self-repair, not as an application-integrity blocker
2. normalize the affected files
3. create an autopilot-only checkpoint commit if that restores a clean tree
4. rerun preflight automatically
5. leave `run_state.json` in the final rerun result rather than a permanent self-blocked state

## Legal Phase Progression

Normal successful phase:
`pending -> in_progress -> validating -> completed -> next_thread_required=true -> new thread starts next phase`

Phase with fixes:
`pending -> in_progress -> validating -> correction_required -> validating -> completed`

Blocked phase:
`pending -> in_progress -> blocked`

## Thread Boundary Rule

When `current_phase_status` becomes `completed`:
- `next_thread_required` must be set to `true`
- current thread must write handoff artifacts
- current thread must not start the next phase
- current thread must record exact branch + exact phase-end commit SHA

The next phase may begin only when:
- a fresh thread has been opened
- `NEXT_THREAD_BOOTSTRAP.md` has been read
- `run_state.json` has been updated for the new phase
- the recorded commit SHA is the starting point for the new thread

## Required State Actions by Status

### When entering `in_progress`
- set `current_phase`
- set `current_phase_status` to `in_progress`
- set `blocker_present` to `false`
- update `last_updated`
- record starting branch and current HEAD SHA in `git_runtime`

### When entering `validating`
- set `current_phase_status` to `validating`
- update `last_updated`

### When entering `correction_required`
- set `current_phase_status` to `correction_required`
- set `last_validation_result` to `failed`
- update `last_updated`

### When entering `completed`
- set `current_phase_status` to `completed`
- append phase to `completed_phases`
- set `last_completed_phase`
- set `last_validation_result` to `passed`
- set `next_thread_required` to `true`
- record phase-end branch and commit SHA
- update `last_updated`

### When entering `blocked`
- set `current_phase_status` to `blocked`
- append phase to `blocked_phases`
- set `blocker_present` to `true`
- write a concise `blocker_summary`
- set `last_validation_result` appropriately
- update `last_updated`

## Required Handoff Actions After `completed`

Before the thread ends:
- write/update phase report
- write/update `CURRENT_HANDOFF.md`
- write/update `NEXT_THREAD_BOOTSTRAP.md`
- write/update `THREAD_HISTORY_INDEX.md`
- update `change_manifest.json`
- update `next_phase_adjustments.json`
- update `validation_manifest.json`
- update `phase_manifest.md`

## Baseline Validation Rule

Before tests are used as a regression gate:
- confirm known baseline failures are documented in `validation_manifest.json`
- distinguish baseline failures from new failures introduced by the current phase
- never “fix” intentional auth behavior merely to satisfy stale tests unless the phase explicitly authorizes that redesign

## Final Verification State

After the last phase:
- do not directly declare success
- run final verification
- update `final_verification_status` with one of:
  - `pending`
  - `in_progress`
  - `passed`
  - `failed`
  - `blocked`

Only when final verification passes should the implementation series be considered safely complete.
