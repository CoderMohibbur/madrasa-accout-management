# PHASE 5 REPORT

## Phase
PHASE_5_PAYMENT_INTEGRATION

## Final Status
PHASE 5 PARTIALLY COMPLETE (SANDBOX SCAFFOLD READY, PROVIDER CONFIRMATION STILL NEEDED)

## Objective
Implement the Laravel-side shurjoPay sandbox integration and manual-bank coexistence groundwork without touching live payment routing, the live merchant panel, or the WordPress-controlled production IPN flow.

## May Phase 5 Proceed?
Yes, in sandbox-only mode.

The blocker review changed for this thread because the official shurjoPay material available to the project now supports a safe sandbox implementation basis:
- sandbox auth plus hosted-checkout initiation
- return handling with `order_id`
- IPN/callback handling with `order_id`
- server-side verify-by-`order_id` as the authoritative finalization step

The phase therefore proceeded with verify-based sandbox finalization and without guessing any undocumented live activation behavior.

## Scope Actually Implemented
- Added a new sandbox-capable payment config surface in `config/payments.php` plus safe placeholder keys in `.env.example`.
- Added an additive Phase 5 migration to extend `payments` and `payment_gateway_events` for provider mode, verification state, review metadata, event-source metadata, and optional transaction linkage.
- Added isolated payment requests, controllers, routes, services, and views for:
  - shurjoPay sandbox initiation
  - success/fail/cancel return handling
  - sandbox IPN callback ingestion
  - verify-API based finalization
  - manual-bank evidence submission
  - management review queue plus approve/reject actions
- Kept live payment routing untouched:
  - no live provider activation
  - no live merchant-panel edits
  - no live WordPress IPN cutover
- Kept donor online payments out of scope.
- Kept canonical posting scaffolded but disabled by default so the sandbox flow does not silently alter legacy transaction/report behavior before account mapping is approved.

## Key Design Decisions
- shurjoPay browser returns remain non-authoritative. Laravel verifies the provider order server-side before marking a payment `paid`.
- IPN events are stored even when they cannot be matched safely; ambiguous or mismatched results are routed to `manual_review`.
- Manual-bank requests remain non-posting until authenticated management approval.
- Receipt issuance happens only after authoritative verification or manual approval.
- No credentials were committed into tracked source files.

## Files Touched
- New files:
  - `config/payments.php`
  - `database/migrations/2026_03_08_190500_extend_payments_for_phase_5_sandbox.php`
  - `app/Http/Controllers/Management/ManualBankPaymentReviewController.php`
  - `app/Http/Controllers/Payments/ManualBankPaymentController.php`
  - `app/Http/Controllers/Payments/ShurjopayPaymentController.php`
  - `app/Http/Requests/Payments/InitiateShurjopayPaymentRequest.php`
  - `app/Http/Requests/Payments/ReviewManualBankPaymentRequest.php`
  - `app/Http/Requests/Payments/StoreManualBankPaymentRequest.php`
  - `app/Services/Payments/PaymentAuditLogger.php`
  - `app/Services/Payments/PaymentEventLogger.php`
  - `app/Services/Payments/PaymentFlowResult.php`
  - `app/Services/Payments/PaymentReferenceGenerator.php`
  - `app/Services/Payments/PaymentWorkflowService.php`
  - `app/Services/Payments/ResolvedPayable.php`
  - `app/Services/Payments/Shurjopay/ShurjopayClient.php`
  - `app/Services/Payments/StudentFeeInvoicePayableResolver.php`
  - `resources/views/management/manual-bank-payments/index.blade.php`
  - `resources/views/payments/manual-bank/show.blade.php`
  - `resources/views/payments/shurjopay/status.blade.php`
  - `routes/payments.php`
  - `tests/Feature/Phase5/PaymentIntegrationTest.php`
- Existing files modified:
  - `.env.example`
  - `app/Models/Payment.php`
  - `app/Models/PaymentGatewayEvent.php`
  - `resources/views/guardian/invoices/show.blade.php`
  - `routes/web.php`

## Compatibility Notes For Existing Files

### `routes/web.php`
- impact class: critical
- why changed:
  Added the new isolated payment route file without renaming any existing route names.
- previous behavior preserved:
  Existing dashboard, management, guardian, donor, and legacy management route names remain intact.
- intentionally not changed:
  No legacy `reports.*`, `transactions.*`, auth routes, or WordPress-facing live routing were changed.
- regression checks:
  - `php artisan route:list --path=payments`
  - `php artisan route:list --path=manual-bank`
  - `php artisan route:list --path=shurjopay`
- rollback consideration:
  Remove the additive payment route include if the Phase 5 sandbox surface must be disabled entirely.

### `app/Models/Payment.php`
- impact class: high
- why changed:
  Added additive status constants, verification fields, reviewer/posted-transaction relations, and terminal-state helpers required by the new payment workflow.
- previous behavior preserved:
  Existing Phase 1-4 payment rows, portal history reads, and receipt linkage still work.
- intentionally not changed:
  No legacy transaction model semantics were rewritten here.
- regression checks:
  - `php artisan test --env=testing tests/Feature/Phase5/PaymentIntegrationTest.php`
  - `php artisan test --env=testing tests/Feature/Phase2/GuardianPortalTest.php`
  - `php artisan test --env=testing tests/Feature/Phase4/ManagementReportingTest.php`
- rollback consideration:
  Revert the Phase 5 migration plus this model extension together.

### `app/Models/PaymentGatewayEvent.php`
- impact class: medium
- why changed:
  Added additive provider-order and request-source metadata for sandbox callback tracing.
- previous behavior preserved:
  Existing event logging relations remain unchanged.
- intentionally not changed:
  No existing event rows were backfilled or deleted.
- regression checks:
  - `php artisan test --env=testing tests/Feature/Phase5/PaymentIntegrationTest.php`
- rollback consideration:
  Revert the migration and this model update together.

### `resources/views/guardian/invoices/show.blade.php`
- impact class: medium
- why changed:
  Added the sandbox-only payment options block so the new Laravel payment surface is reachable from linked guardian invoices.
- previous behavior preserved:
  Existing invoice summary, item list, and payment history remain intact.
- intentionally not changed:
  No shared navigation or legacy management view was modified.
- regression checks:
  - `php artisan test --env=testing tests/Feature/Phase2/GuardianPortalTest.php`
  - `php artisan test --env=testing tests/Feature/Phase5/PaymentIntegrationTest.php`
- rollback consideration:
  Remove the new sandbox options block while preserving the existing invoice details/history sections.

### `.env.example`
- impact class: low
- why changed:
  Documented the required sandbox payment keys without storing real secrets.
- previous behavior preserved:
  Existing local bootstrapping keys remain unchanged.
- intentionally not changed:
  No `.env` runtime file was edited.
- regression checks:
  - `php -l config/payments.php`
- rollback consideration:
  Remove the Phase 5 placeholder block if the sandbox payment surface is retired.

## Validation Performed
- Syntax:
  - `php -l config/payments.php`
  - `php -l database/migrations/2026_03_08_190500_extend_payments_for_phase_5_sandbox.php`
  - `php -l app/Services/Payments/PaymentWorkflowService.php`
  - `php -l app/Services/Payments/Shurjopay/ShurjopayClient.php`
  - `php -l app/Http/Controllers/Payments/ShurjopayPaymentController.php`
  - `php -l app/Http/Controllers/Payments/ManualBankPaymentController.php`
  - `php -l app/Http/Controllers/Management/ManualBankPaymentReviewController.php`
  - `php -l app/Models/Payment.php`
  - `php -l app/Models/PaymentGatewayEvent.php`
  - `php -l routes/payments.php`
  - `php -l routes/web.php`
  - `php -l tests/Feature/Phase5/PaymentIntegrationTest.php`
- Route registration:
  - `php artisan route:list --path=payments`
  - `php artisan route:list --path=manual-bank`
  - `php artisan route:list --path=shurjopay`
  - confirmed 10 new payment/manual-bank routes
- Focused tests:
  - `php artisan test --env=testing tests/Feature/Phase5/PaymentIntegrationTest.php`
    - passed: 4 tests
- Cross-phase regression tests:
  - `php artisan test --env=testing tests/Feature/Phase1/PortalRoleAccessTest.php tests/Feature/Phase2/GuardianPortalTest.php tests/Feature/Phase3/DonorPortalTest.php tests/Feature/Phase4/ManagementReportingTest.php tests/Feature/Phase5/PaymentIntegrationTest.php tests/Unit/Policies/StudentFeeInvoicePolicyTest.php`
    - passed: 14 tests
- Broad regression gate:
  - `php artisan test --env=testing`
    - result: 14 failures, 29 passes
    - classification: known pre-existing auth/profile baseline failures only
    - no new unexpected failures introduced by Phase 5

## Remaining Gaps / Deferred Items
- The official sandbox material used in this thread did not expose a separate callback signature secret. The implementation therefore relies on server-to-server verify responses instead of inventing a signature scheme.
- The distinct provider-side fail-return contract remains non-authoritative; the local fail route is implemented as safe UI handling around verification results.
- Canonical posting is intentionally disabled by default until an operator-approved account mapping exists for the legacy `transactions` surface.
- Donor online payments remain deferred because no dedicated donor payable model exists yet.
- Live shurjoPay activation, live merchant-panel configuration, and WordPress IPN cutover remain out of scope.

## Branch / Commit
- safe branch: `codex/2026-03-08-phase-1-foundation-safety`
- phase start checkpoint: `12187ebebb3a003dc6b20c7cbc979a305fe5f280`
- Phase 5 implementation checkpoint: `0b416d6f9e82679c7b720a5119383f6dff8cef69`

## Go / No-Go Decision
- Sandbox scaffold: GO
- Live activation: NO-GO
- WordPress IPN cutover: NO-GO
- Donor online payments: NO-GO until a dedicated donor payable model exists
