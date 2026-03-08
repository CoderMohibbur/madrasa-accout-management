# SAFE GIT WORKFLOW — v4 PROJECT-HARDENED

## Mandatory Rules

- Never implement on `main`, `master`, or another protected/shared branch.
- Require a clean working tree before starting.
- If the working tree is dirty only because autopilot-owned files under `docs/codex-autopilot/state/*`, `handoff/*`, `reports/*`, `docs/*`, or `templates/*` were updated during preflight or handoff maintenance, checkpoint those files and rerun the gate instead of treating that as an application blocker.
- Create or switch to the dedicated branch before any implementation changes.
- Record actual branch and actual commit SHA in `state/run_state.json`.
- Record phase-end commit SHA in report + handoff + thread index.

## Required Branch Pattern

Default branch for first implementation phase:
- `codex/2026-03-08-phase-1-foundation-safety`

Subsequent recommended patterns:
- `codex/2026-03-08-phase-2-guardian-portal`
- `codex/2026-03-08-phase-3-donor-portal`
- `codex/<yyyy-mm-dd>-phase-<n>-<short-name>`

A long-lived dedicated branch is acceptable only if:
- every phase still ends with a clean validated checkpoint commit
- exact commit SHA is recorded at each handoff

## Preflight Git Checklist

1. confirm repository exists
2. confirm no uncommitted changes (`git status --porcelain`) unless any pending changes are autopilot-only and will be checkpointed before implementation starts
3. confirm current branch is not protected/shared
4. create or switch to the dedicated branch from the intended base
5. record actual current branch in `run_state.json`
6. record actual HEAD SHA in `run_state.json`
7. do not continue until all required docs are present

## Required Commit Checkpoints

At minimum:
- checkpoint A: preflight passed and implementation is about to begin
- checkpoint B: phase completed and validated

Recommended commit message pattern:
- `chore(codex): preflight checkpoint for phase 1`
- `feat(phase-1): foundation safety scaffold complete`
- `fix(phase-1): validation correction`

## If Git Preflight Fails

If the failure is autopilot-only dirty state/handoff/report/doc output, checkpoint it and rerun preflight.
Otherwise stop immediately.
Do not edit code.
Write the blocker into:
- current phase report
- `run_state.json`
- `CURRENT_HANDOFF.md` if a restart context is needed
