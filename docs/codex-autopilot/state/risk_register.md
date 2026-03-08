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

## R-07 Legacy management surfaces still allow unroled users
- Severity: medium
- Files:
  - `routes/web.php`
  - `app/Http/Middleware/EnsureManagementSurfaceAccess.php`
- Why it matters:
  Phase 2 blocks guardian-only and donor-only users from legacy management pages, but it intentionally preserves access for existing unroled users to avoid breaking live workflows before role backfill.
- Protection in kit:
  The new `management.surface` middleware narrows portal leakage without forcing a risky management-role cutover in the same phase.
- Runtime action:
  Revisit full explicit management-role enforcement only after role backfill and regression proof during a later hardening step.

## R-08 Legacy donor rows do not guarantee receipt backfill
- Severity: medium
- Files:
  - `app/Models/Transactions.php`
  - `app/Models/Receipt.php`
  - `app/Services/DonorPortal/DonorPortalData.php`
- Why it matters:
  Legacy manual donation rows and Phase 1 receipt records are separate data surfaces, so older donor ledger rows may not have a corresponding receipt record for the portal to display.
- Protection in kit:
  Phase 3 keeps donor receipt visibility strictly user-bound and read-only instead of inventing synthetic receipt records from legacy transactions.
- Runtime action:
  Any later backfill or reporting reconciliation must be explicit and documented rather than inferred inside the portal views.

## R-09 Payment provider contract is unspecified
- Severity: critical
- Files:
  - `app/Models/Payment.php`
  - `app/Models/PaymentGatewayEvent.php`
  - `docs/codex-autopilot/phases/PHASE_5_PAYMENT_INTEGRATION.md`
- Why it matters:
  The repository has payment schema placeholders but no concrete provider choice, webhook signature model, or deployment configuration contract for live payment finalization.
- Protection in kit:
  Phase 5 is explicitly blocked until the provider/business decision exists.
- Runtime action:
  Do not implement live payment initiation, webhook handling, or finalization logic until the provider contract is supplied.
