# POST PROGRAM LIVE READINESS GAP ASSESSMENT

## Scope
Post-program verification only for the approved sandbox-only guardian invoice payment scope.

This assessment does not reopen Phases 1-6, does not change application code, does not touch live merchant settings, does not cut over the current WordPress IPN path, does not enable canonical posting by default, and does not introduce donor online payment finalization.

## Verification Performed
- Read the required autopilot, handoff, state, and report files in the user-specified order.
- Verified Git/runtime position:
  - branch: `codex/2026-03-08-phase-1-foundation-safety`
  - working tree: clean at assessment start
  - current HEAD: `a3f048c4d18312a854d99f0470d851cafc6b3cab`
  - diff from recorded Phase 6 application checkpoint `12c96a0b16649bfd0c574a7fb90c8aa559d7a3e3`: autopilot docs/state/handoff/report files only
- Verified runtime surface:
  - `php artisan route:list --path=payments`: 10 payment/manual-bank routes registered
  - `php artisan test --env=testing tests/Feature/Phase5/PaymentIntegrationTest.php`: 6 passed
  - `php artisan test --env=testing`: 14 failed, 31 passed
  - full-suite failure classification: only the 14 documented auth/profile baseline failures remain

## Decision Summary
- Approved Phase 1-6 implementation program complete: `YES`
- Completed phases should be reopened now: `NO`
- Sandbox readiness confirmed: `YES`
- Live readiness confirmed: `NO`
- Live activation still blocked: `YES`
- Real unresolved bug found in the approved sandbox-only guardian invoice payment scope: `NO`
- Pre-existing non-scope baseline failures still exist: `YES` (`tests/Feature/Auth/**`, `tests/Feature/ProfileTest.php`)

## Completed Approved Scope
- Phase 1-6 artifacts consistently record the six-phase program as completed for the approved sandbox-only guardian invoice payment scope.
- The repository still matches that conclusion:
  - sandbox shurjoPay initiation exists
  - success/fail/cancel browser returns exist
  - sandbox IPN ingestion exists
  - verify-by-order-id finalization exists
  - manual-bank request/review/approve/reject flow exists
  - dedicated receipt issuance exists
  - invoice balance updates exist
  - canonical posting remains scaffolded but disabled by default
  - donor portal remains read-only and outside online finalization scope
- No repository evidence requires reopening completed implementation work.

## Still-Blocked Live Scope

### 1. Provider-native callback/IPN verification contract
Current state:
- The repo exposes `SHURJOPAY_SIGNATURE_SECRET` as config only, but it is not used to validate inbound provider requests.
- The IPN endpoint exists, but the payment flow still treats server-to-server verify-by-order-id as the authoritative signal instead of a confirmed provider-native signed callback contract.
- `signature_valid` in `payment_gateway_events` is currently a locally supplied flag, not the result of a provider-native signature check.

Code evidence:
- `config/payments.php`
- `app/Services/Payments/Shurjopay/ShurjopayClient.php`
- `app/Services/Payments/PaymentWorkflowService.php`
- `app/Services/Payments/PaymentEventLogger.php`
- `routes/payments.php`

Blocking reason:
- Live callback authenticity is still unproven. Without the confirmed provider contract, Laravel cannot safely treat the inbound callback/IPN as authoritative for production finalization.

Required human/business input:
- Provider documentation or provider-confirmed contract for:
  - callback/IPN HTTP method
  - payload fields
  - headers
  - signature or equivalent authenticity mechanism
  - authoritative success/failure semantics
  - merchant-panel callback configuration requirements

Future technical work package:
- Implement provider-native callback verification.
- Reject or quarantine unsigned/unverifiable inbound requests.
- Persist the confirmed contract fields needed for audit/replay analysis.
- Add negative tests for forged, replayed, and malformed callback/IPN requests.

### 2. Authoritative provider event identifier
Current state:
- `payment_gateway_events.provider_event_id` is unique, but it is populated with a locally generated ULID rather than a provider-authoritative event identifier.
- The current flow therefore logs events safely for sandbox traceability, but not against a provider-native event identity contract.

Code evidence:
- `database/migrations/2026_03_08_100400_create_payment_receipt_and_audit_tables.php`
- `app/Services/Payments/PaymentEventLogger.php`

Blocking reason:
- Live duplicate suppression, replay handling, and reconciliation cannot rely on a locally invented event id when the provider's authoritative callback/event identity is still unconfirmed.

Required human/business input:
- Confirmation of the exact provider-native event id or transaction id to use for deduplication.
- If the provider does not expose a stable event id, approval of the deterministic dedupe contract that should replace it.

Future technical work package:
- Store the authoritative provider event identifier in `payment_gateway_events`.
- Update the uniqueness/deduplication strategy to align with the provider contract.
- Add duplicate callback/IPN tests using the real provider-native identifier rules.

### 3. Live activation prerequisites
Current state:
- Live config keys exist in the repo surface, but the client intentionally refuses any non-sandbox mode.
- No live credentials are stored in tracked files.
- The current implementation is intentionally sandbox-only.

Code evidence:
- `config/payments.php`
- `app/Services/Payments/Shurjopay/ShurjopayClient.php`

Blocking reason:
- Live mode is intentionally disabled until provider contract completion, operational approval, and merchant-panel readiness are all confirmed.

Required human/business input:
- Explicit go-live approval
- named owner for live merchant settings
- final live callback/return URLs
- activation window
- rollback owner and rollback trigger
- reconciliation owner for first live transactions

Future technical work package:
- Add a controlled live-mode activation path after approval.
- Validate the live env/config contract without committing secrets.
- Run a production-safe smoke test and first-day reconciliation checklist.
- Add an operator runbook for live monitoring, exception handling, and rollback.

### 4. WordPress-to-Laravel cutover prerequisites
Current state:
- Laravel has its own payment return and IPN endpoints.
- The current live WordPress-controlled IPN path was intentionally not cut over during the approved program.

Code evidence:
- `routes/payments.php`
- `docs/codex-autopilot/docs/PHASE_5_PAYMENT_PROVIDER_SPEC.md`
- `docs/codex-autopilot/reports/PHASE_5_REPORT.md`
- `docs/codex-autopilot/reports/PHASE_6_REPORT.md`

Blocking reason:
- A live callback cutover without provider-contract certainty and merchant-panel control risks double-processing, missed callbacks, or split-brain payment handling between WordPress and Laravel.

Required human/business input:
- Who owns the live WordPress/payment endpoint today
- Who can change the live merchant-panel callback target
- Exact cutover timing
- Rollback decision owner
- Whether any dual-run period is allowed or forbidden

Future technical work package:
- Prepare a formal WordPress-to-Laravel cutover runbook.
- Change the live merchant callback target only inside an approved cutover window.
- Define rollback steps and reconciliation checkpoints for the first live settlement cycle.
- Preserve a no-double-finalization guarantee during the switchover.

### 5. Canonical posting activation prerequisites
Current state:
- Payment finalization can mark payments paid, update invoices, and issue receipts.
- Legacy `transactions` posting is skipped unless `PAYMENT_STUDENT_FEE_POSTING_ENABLED=true`.
- The skip is intentional and documented in payment metadata.

Code evidence:
- `config/payments.php`
- `app/Services/Payments/PaymentWorkflowService.php`
- `app/Services/Finance/CanonicalPostingService.php`

Blocking reason:
- No operator-approved account mapping exists yet for the legacy `transactions` surface, so enabling canonical posting now would risk altering accounting/report behavior without approval.

Required human/business input:
- Approved `transactions_type_key`
- approved `account_id`
- posting/reference rule approval
- operator sign-off that the resulting `transactions` rows match accounting expectations

Future technical work package:
- Configure the approved mapping.
- Rehearse posting in a controlled environment.
- Compare resulting `transactions` and management reports against expected accounting outputs.
- Enable canonical posting only after sign-off and a rollback plan exist.

### 6. Donor payable expansion prerequisites
Current state:
- The payment workflow is limited to `StudentFeeInvoice`.
- Donor portal pages are read-only against legacy donation history and receipt visibility.
- No dedicated donor payable/finalizable online payment model exists yet.

Code evidence:
- `app/Services/Payments/StudentFeeInvoicePayableResolver.php`
- `app/Services/Payments/PaymentWorkflowService.php`
- `routes/donor.php`
- `docs/implementation-analysis-report.md`

Blocking reason:
- Finalizing donor online payments against legacy `transactions` would violate the approved system design and payment-safety rules.

Required human/business input:
- Whether donor online payments are approved at all for the next scope
- the approved donation payable/domain model
- receipt and reconciliation rules for donor payments
- any fund/campaign or allocation requirements

Future technical work package:
- Design and add a dedicated donor payable model.
- Add donor-specific initiation/finalization rules.
- Add donor payment reconciliation, receipt, and reporting logic.
- Add donor payment tests before any live donor finalization is considered.

## Exact Required Human/Business Decisions Next
- Confirm the shurjoPay live callback/IPN verification contract from the provider.
- Confirm the authoritative provider event identifier or approved dedupe rule.
- Approve or reject live shurjoPay activation for this repository.
- Approve the WordPress-to-Laravel cutover plan and named owners.
- Approve the canonical posting account mapping before `PAYMENT_STUDENT_FEE_POSTING_ENABLED` is ever enabled.
- Approve or reject donor online payment expansion as a new scope item.
- Confirm whether manual-bank evidence must remain non-file or needs approved upload/storage scope in a future package.

## Exact Future Technical Work Packages
- `WP-A Provider Contract Completion`
  - Finalize callback/IPN authenticity, payload, event-id, and verification contract.
- `WP-B Provider Event Identity And Replay Safety`
  - Replace the local `provider_event_id` surrogate with provider-authoritative replay/dedupe handling.
- `WP-C Live Activation Package`
  - Add approved live-mode activation, env rollout, smoke test, monitoring, and reconciliation runbook.
- `WP-D WordPress Cutover Package`
  - Execute the approved merchant-panel callback change and controlled rollback/reconciliation plan.
- `WP-E Canonical Posting Activation Package`
  - Apply approved account mapping, rehearse posting, validate reports, and enable only after sign-off.
- `WP-F Donor Payable Expansion Package`
  - Introduce a dedicated donor payable model before any donor online finalization work starts.

## Final Assessment
- The approved implementation program is complete and should not be reopened.
- No unresolved bug was found in the approved sandbox-only guardian invoice payment scope.
- Sandbox readiness is confirmed.
- Live readiness is not confirmed.
- Live activation remains blocked until the provider contract, WordPress cutover prerequisites, canonical posting approvals, and donor-scope decisions are all explicitly resolved.
