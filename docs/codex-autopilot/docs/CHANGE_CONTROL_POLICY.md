# CHANGE CONTROL POLICY — v4 PROJECT-HARDENED

## Goal

Protect the existing application by minimizing change scope and documenting every non-trivial touch.

## Hard Rules

1. Prefer additive changes first.
2. Modify existing files only where integration requires it.
3. Do not rename, move, or delete existing files unless unavoidable and phase-approved.
4. Do not edit historical migrations.
5. Do not rename existing route names.
6. Do not perform broad rewrites of legacy controllers.
7. Record every touched existing file in the phase report and change manifest.
8. If a touched file is production-critical, add a compatibility note and rollback note.
9. Every modified existing file must have a stated reason, impact class, and regression check list.
10. When in doubt, stop and narrow the scope.

## Production-Critical Categories

- auth and middleware
- routes and route names
- shared navigation/layout
- accounting transaction/posting services
- legacy transaction write controllers
- receipt generation and numbering
- existing financial reports
- student data mapping and legacy aliases
- payment callbacks/webhooks
- dashboard totals
- transaction migration history

## Required Compatibility Note For Critical Existing Files

Document:
- file path
- why the change was necessary
- what previous behavior must remain intact
- what was intentionally not changed
- what must be regression-checked
- rollback consideration

## Mandatory Change Impact Classification

For every modified existing file, classify one:
- `low` = isolated integration change, no business logic change
- `medium` = existing file changed, but semantics preserved
- `high` = sensitive financial/auth/reporting file changed
- `critical` = accounting, auth, or route compatibility risk

`high` and `critical` changes require explicit regression notes in the phase report.

## Forbidden Shortcuts

Do not:
- “clean up” legacy naming just because it looks odd
- change auth behavior to make stale tests pass
- change receipt/report totals without a formal before/after reasoning note
- replace route names globally
- remove legacy write paths early unless the current phase is final hardening
