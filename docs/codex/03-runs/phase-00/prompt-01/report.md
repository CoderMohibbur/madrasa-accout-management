# Report

## Mandatory Workflow Rules

- Run one prompt at a time and stay strictly inside that prompt's approved scope.
- Treat additive-first changes as the default: prefer new files, migrations, services, middleware, policies, tests, route groups, and views before editing existing core files.
- Protect existing production logic by default. Do not casually alter auth/session behavior, report semantics, transaction posting behavior, shared navigation logic, or legacy compatibility aliases.
- Never rename existing route names, never edit historical migrations, and never do broad refactors of legacy controllers just because they look inconsistent.
- Distinguish baseline failures from new regressions before using tests as a gate; the documented auth/profile failures remain baseline until proven otherwise.
- Validate each phase before any progression, fix same-phase issues before handoff, and stop immediately if continuation would require protected-path violations, unsafe financial assumptions, or broad unapproved rewrites.
- For implementation work, the autopilot workflow requires safe-branch Git hygiene, checkpoint commits, state updates, and fresh-thread handoff at each completed phase boundary.
- For this `docs/codex` run, later prompts should be executed as retrospective analysis/documentation against the current repository state unless a prompt reveals a narrowly scoped gap that can be corrected without violating the guardrails above.

## Protected Path Summary

Protected by default:
- `app/Http/Controllers/**`
- `app/Models/**`
- `routes/**`
- `resources/views/layouts/**`
- existing production views under `resources/views/**`
- historical migrations under `database/migrations/**`
- existing stable suites under `tests/**`

Project-specific critical paths requiring explicit justification:
- Auth and identity: `app/Http/Controllers/Auth/RegisteredUserController.php`, `app/Http/Requests/Auth/LoginRequest.php`, `app/Models/User.php`, `routes/auth.php`
- Financial write paths: `app/Http/Controllers/TransactionCenterController.php`, `app/Http/Controllers/TransactionsController.php`, `app/Http/Controllers/LenderController.php`, `app/Support/TransactionLedger.php`, `app/Models/Transactions.php`, `database/migrations/2024_10_09_132953_create_table_transactions_table.php`
- Reporting and dashboard: `app/Http/Controllers/DashboardController.php`, `app/Http/Controllers/ReportController.php`, `app/Http/Controllers/Reports/**`, `resources/views/reports/**`, `resources/views/dashboard/**`
- Shared navigation and route-sensitive student surfaces: `app/Models/Student.php`, `app/Http/Controllers/StudentController.php`, `resources/views/layouts/navigation.blade.php`, `routes/web.php`

Restricted and frozen behaviors:
- Existing route names used by shared navigation must remain intact.
- `email_verified_at` approval/login semantics must not be changed casually.
- Existing report totals semantics are frozen unless a prompt explicitly documents a controlled change.
- Historical migration contents are frozen.
- Legacy transaction semantics must not be normalized globally outside an approved, bounded phase.

## Additive-First Constraints

Generally safe additions:
- new migrations
- new request classes
- new middleware
- new policy classes
- new service classes
- new portal controllers
- new dedicated Blade views for new portals
- new tests for the current phase
- docs, state, handoff, and run-report files
- new route groups that preserve existing route names

Change-control rules for touching existing files:
- modify existing files only where integration requires it
- do not rename, move, or delete existing files unless unavoidable and phase-approved
- record every touched existing file in the phase report with reason, impact class, regression checks, compatibility note, and rollback consideration when critical
- stop and narrow scope if a prompt would require touching multiple critical paths at once

## Key No-Go Warnings

Auth and roles:
- The live auth model is a single `users` table with a single `web` guard; do not introduce multi-guard drift casually.
- Do not change registration/login semantics just to satisfy stale Breeze-default tests.
- Role and ownership boundaries must be additive and must not break existing management access without a documented backfill and regression plan.

Routes and portal work:
- Do not rename or globally reorganize existing management route names.
- New guardian or donor routes must remain additive and portal-scoped.
- Portal/payment work may not bypass Phase 1 safety outputs or reuse legacy assumptions as a shortcut.

Payment and finance:
- New financial work must follow `credit = inflow` and `debit = outflow`.
- Do not base new payment finalization on legacy `TransactionsController` or `LenderController` behavior.
- Do not bypass server-side payable resolution or treat legacy `transactions` rows as a safe online-payment domain.
- Donor online payment finalization remains out of scope until a dedicated donor payable model exists.

UI work:
- Do not copy weak existing UI patterns forward as the target standard.
- Shared navigation and existing production views are protected; any touch there must be additive and compatibility-aware.

## Safe High-Level Work Order

1. Use this prompt-01 output as the governing guardrail baseline for all remaining `docs/codex` prompts.
2. Adapt prompt-02 so it inventories the repository exactly as it exists now, including the already-implemented guardian, donor, reporting, and sandbox payment surfaces.
3. Run prompts 03-27 as scoped analysis and planning artifacts derived from the current codebase plus existing autopilot reports, decisions, risks, and final documents.
4. Treat prompts 28-43 as retrospective implementation documentation and validation of the already-landed repository state unless a prompt exposes a narrow, safe, in-scope correction.
5. Stop immediately if any later prompt would require real secrets, route-name rewrites, historical migration edits, broad auth/reporting/financial refactors, or reopening the completed autopilot program without explicit approval.

## Repository Readiness For Scoped Analysis

Ready for scoped analysis: Yes, with documented caveats.

Evidence:
- All prompt-01 source documents exist and are populated.
- `validation_manifest.json` documents the known baseline auth/profile failures.
- `risk_register.md` captures the main auth, route, reporting, and payment hazards.
- `.git/HEAD` points to `refs/heads/codex/2026-03-08-phase-1-foundation-safety`, and the branch ref currently resolves to `a3f048c4d18312a854d99f0470d851cafc6b3cab`.

Caveats:
- `run_state.json` still records `actual_head_sha` and `phase_end_commit_sha` as `12c96a0b16649bfd0c574a7fb90c8aa559d7a3e3`, while the branch ref has advanced to `a3f048c4d18312a854d99f0470d851cafc6b3cab` for the Phase 6 handoff-metadata commit. Later prompts should treat live repo contents plus the latest handoff/report artifacts as authoritative when commit metadata disagrees.
- The autopilot program is already marked complete through Phase 6, so this `docs/codex` sequence should analyze and document the current state rather than assume a fresh implementation kickoff.
- The `git` CLI was not available in the current shell, so working-tree cleanliness could not be rechecked directly from the command line during this prompt.
