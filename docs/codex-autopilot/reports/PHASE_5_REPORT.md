# PHASE 5 REPORT

## Phase
PHASE_5_PAYMENT_INTEGRATION

## Final Status
PHASE 5 PARTIALLY COMPLETE (SANDBOX READY WITH DOCUMENTED LIMITS)

## Objective
Implement the Laravel-side shurjoPay sandbox integration and manual-bank coexistence groundwork without touching live payment routing, the live merchant panel, or the WordPress-controlled production IPN flow.

## Closeout Decision
Phase 5 is sandbox-ready for invoice-backed guardian payments only. It is not live-ready, it does not activate donor online payments, and it does not change the current WordPress-controlled live IPN path.

## Scope Actually Implemented
- Added a sandbox-capable payment config surface in `config/payments.php` plus safe placeholder keys in `.env.example` without committing secrets.
- Added an additive Phase 5 migration to extend `payments` and `payment_gateway_events` for provider mode, verification state, review metadata, event-source metadata, and optional transaction linkage.
- Added isolated payment requests, controllers, routes, services, views, and tests for:
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

## Bugs Found And Fixed During Closeout
- `app/Services/Payments/Shurjopay/ShurjopayClient.php` was not forwarding the reserved fail return URL during checkout initiation. It now sends success, fail, and cancel URLs together.
- The default shurjoPay order-prefix fallback in `config/payments.php` and `app/Services/Payments/PaymentReferenceGenerator.php` still used `SPAY` instead of the project-approved `HFS`. The fallback and `.env.example` now align to `HFS`.
- `app/Services/Payments/PaymentWorkflowService.php` could surface raw verification/auth transport exceptions during browser returns or IPN handling. It now logs a `verification_error` event and routes the payment to manual review without issuing a receipt or posting.
- `tests/Feature/Phase5/PaymentIntegrationTest.php` now covers the fail-url payload wiring and the manual-review fallback for shurjoPay verification transport failures.

## Files Touched
- New Phase 5 files remain:
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
- Existing files modified during Phase 5 remain:
  - `.env.example`
  - `app/Models/Payment.php`
  - `app/Models/PaymentGatewayEvent.php`
  - `resources/views/guardian/invoices/show.blade.php`
  - `routes/web.php`

## Validation Performed
- Syntax/static sanity:
  - `php -l config/payments.php`
  - `php -l database/migrations/2026_03_08_190500_extend_payments_for_phase_5_sandbox.php`
  - `php -l app/Services/Payments/PaymentReferenceGenerator.php`
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
  - `php artisan route:list --path=shurjopay -v`
  - confirmed 10 payment/manual-bank routes, 5 manual-bank routes, and 5 shurjopay routes
- Migration status/safety:
  - `php artisan migrate:status --env=testing`
  - confirmed `2026_03_08_190500_extend_payments_for_phase_5_sandbox` is present and ran
- Focused tests:
  - `php artisan test --env=testing tests/Feature/Phase5/PaymentIntegrationTest.php`
    - passed: 5 tests
- Cross-phase regression slice:
  - `php artisan test --env=testing tests/Feature/Phase1/PortalRoleAccessTest.php tests/Feature/Phase2/GuardianPortalTest.php tests/Feature/Phase3/DonorPortalTest.php tests/Feature/Phase4/ManagementReportingTest.php tests/Feature/Phase5/PaymentIntegrationTest.php tests/Unit/Policies/StudentFeeInvoicePolicyTest.php`
    - passed: 15 tests
- Broad regression gate:
  - `php artisan test --env=testing`
    - result: 14 failures, 30 passes
    - classification: known pre-existing auth/profile baseline failures only
    - no new unexpected failures introduced by Phase 5 or its closeout corrections

## Remaining Limits
- The official material available in-repo still does not confirm a provider-native callback signature method or authoritative provider event identifier. The sandbox implementation therefore remains verify-by-order-id based rather than live-ready.
- Canonical posting is intentionally disabled by default until an operator-approved account mapping exists for the legacy `transactions` surface.
- Donor online payments remain deferred because no dedicated donor payable model exists yet.
- Live shurjoPay activation, live merchant-panel configuration, and WordPress IPN cutover remain out of scope.

## Branch / Commit
- safe branch: `codex/2026-03-08-phase-1-foundation-safety`
- phase start checkpoint: `12187ebebb3a003dc6b20c7cbc979a305fe5f280`
- initial Phase 5 implementation checkpoint: `0b416d6f9e82679c7b720a5119383f6dff8cef69`
- closeout correction checkpoint: `acc00f9aafe35cfd461266d0ae2754603acb5273`

## Go / No-Go Decision
- Sandbox invoice-backed guardian payments: GO
- Phase 6 hardening thread: GO
- Live activation: NO-GO
- WordPress IPN cutover: NO-GO
- Donor online payments: NO-GO until a dedicated donor payable model exists