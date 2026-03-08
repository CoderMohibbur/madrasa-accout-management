# RISK REGISTER

## R-01 Mixed financial write semantics
- Severity: critical
- Files:
  - `app/Http/Controllers/TransactionCenterController.php`
  - `app/Http/Controllers/TransactionsController.php`
  - `app/Http/Controllers/LenderController.php`
  - `app/Support/TransactionLedger.php`
- Why it matters:
  Current write paths do not consistently agree on whether inflow is `credit` or `debit`.
- Protection in kit:
  Phase 1 requires canonical posting safety and freezes payment work until that exists.
- Runtime action:
  Do not allow new payment/invoice posting work outside the canonical path.

## R-02 Auth approval nuance
- Severity: high
- Files:
  - `app/Http/Controllers/Auth/RegisteredUserController.php`
  - `app/Http/Requests/Auth/LoginRequest.php`
- Why it matters:
  Registration does not auto-login and `email_verified_at` is used as an activation gate.
- Protection in kit:
  Auth behavior is frozen unless explicitly phase-approved.
- Runtime action:
  Baseline tests must not silently override live auth semantics.

## R-03 Route-name sensitivity
- Severity: high
- Files:
  - `resources/views/layouts/navigation.blade.php`
  - `routes/web.php`
- Why it matters:
  Current UI active states depend on specific route names.
- Protection in kit:
  Route-name rename is forbidden by default.
- Runtime action:
  New portal routes must be additive and preserve legacy names.

## R-04 Report semantics coupled to `transactions`
- Severity: high
- Files:
  - `app/Http/Controllers/ReportController.php`
  - `app/Http/Controllers/Reports/**`
  - `resources/views/reports/**`
- Why it matters:
  Existing totals depend on legacy transaction rows.
- Protection in kit:
  Report semantics are frozen unless explicitly documented.
- Runtime action:
  Any reporting changes require before/after reasoning notes.

## R-05 Legacy compatibility aliases in student/transaction models
- Severity: medium
- Files:
  - `app/Models/Student.php`
  - `app/Models/Transactions.php`
- Why it matters:
  Hidden dependencies may rely on old names and aliases.
- Protection in kit:
  These paths are critical protected files.
- Runtime action:
  Avoid cleanup refactors that remove legacy-friendly accessors early.

## R-06 Baseline test failures
- Severity: high
- Files:
  - `tests/Feature/Auth/**`
  - `tests/Feature/ProfileTest.php`
- Why it matters:
  Test suite is not a clean green baseline.
- Protection in kit:
  `validation_manifest.json` captures known failures separately.
- Runtime action:
  Distinguish baseline vs new regressions before deciding go/no-go.
