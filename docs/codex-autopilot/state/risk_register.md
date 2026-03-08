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

## R-09 Payment provider security contract is still incomplete
- Severity: medium
- Files:
  - `app/Models/Payment.php`
  - `app/Models/PaymentGatewayEvent.php`
  - `app/Services/Payments/PaymentWorkflowService.php`
  - `docs/codex-autopilot/docs/PHASE_5_PAYMENT_PROVIDER_SPEC.md`
  - `docs/codex-autopilot/phases/PHASE_5_PAYMENT_INTEGRATION.md`
- Why it matters:
  Phase 5 now uses a safe sandbox-only verify-API flow, but the official material available to this thread still did not expose a separate callback signature secret, authoritative provider event identifier, or a distinct fail-return contract for live readiness.
- Protection in kit:
  Phase 5 implemented only the verify-based sandbox scope and still forbids live activation.
- Runtime action:
  Do not treat the current sandbox scaffold as approval for live go-live. Reconfirm any provider-side signature, callback, or merchant-panel requirements before activation.

## R-10 Donor online payment scope lacks a safe payable model
- Severity: critical
- Files:
  - `app/Models/Payment.php`
  - `app/Models/Transactions.php`
  - `docs/implementation-analysis-report.md`
  - `docs/codex-autopilot/reports/PHASE_3_REPORT.md`
  - `docs/codex-autopilot/docs/PHASE_5_PAYMENT_PROVIDER_SPEC.md`
- Why it matters:
  Guardian invoice payments can attach to `StudentFeeInvoice`, but donor self-service payments still have no dedicated donation payable model. Reusing legacy `transactions` as a live gateway payable would violate the project analysis and payment-safety rules.
- Protection in kit:
  The implemented Phase 5 payment flow is intentionally limited to invoice-backed guardian payments and keeps donor-side online payments out of scope.
- Runtime action:
  Do not finalize donor online payments against legacy `transactions`; either add a dedicated donation payable model first or formally narrow Phase 5 implementation scope.

## R-11 Canonical posting remains intentionally disabled by default
- Severity: medium
- Files:
  - `config/payments.php`
  - `app/Services/Payments/PaymentWorkflowService.php`
  - `app/Models/Payment.php`
- Why it matters:
  The new sandbox flow finalizes payments, receipts, and invoice balances, but it intentionally skips legacy `transactions` posting unless an operator-approved account mapping is configured.
- Protection in kit:
  This avoids silently altering legacy accounting/report behavior during sandbox-only work.
- Runtime action:
  Do not enable `PAYMENT_STUDENT_FEE_POSTING_ENABLED=true` until a safe account mapping and regression plan are in place.
