# Report

## Scope Lock Before Coding

### Exact Approved Slice

- Prompt-40 is limited to `MR4` route/middleware/policy finalization only.
- The approved slice for this run was only:
  - attach explicit donor and guardian-informational eligibility middleware to their final route buckets
  - keep the protected guardian route edge on dedicated protected middleware
  - finish payment detail and payment-initiation authorization through reusable policy checks instead of scattered controller/service shortcuts
  - remove the remaining blanket `verified` dependence from guardian payment detail and return routes only after the explicit context middleware layer was in place
  - preserve prompt-39's chooser, shared-home fallback, and additive switching behavior without reopening its UX or resolver decisions

### Legacy Management Compatibility Rules Restated

- Existing management route names and the legacy management dashboard behavior had to remain unchanged.
- Prompt-40 could harden management-surface fallthrough rules, but it could not rename management routes or rewrite legacy management metrics.
- Management-compatible accounts still remain outside the multi-role chooser path.

### No-Go Warnings Restated

- No route-name changes were allowed for `dashboard`, `donor.*`, `guardian.*`, `guardian.info.*`, `payments.*`, or `management.*`.
- Prompt-40 could not reintroduce raw role-order redirects as the final shared-home model.
- Prompt-40 could not weaken donor eligibility, guardian informational/protected separation, linked-student ownership, or protected invoice/payment boundaries.
- Prompt-40 could not reopen donor payable expansion, identity merge logic, or broader schema/auth refactors.

## Implementation Result

Prompt-40 completed inside the approved route/middleware/policy finalization scope.

### Files Changed

- `app/Http/Middleware/EnsureDonorPortalAccess.php`
- `app/Http/Middleware/EnsureGuardianInformationalAccess.php`
- `app/Policies/PaymentPolicy.php`
- `bootstrap/app.php`
- `routes/web.php`
- `routes/payments.php`
- `app/Http/Middleware/EnsureManagementSurfaceAccess.php`
- `app/Http/Controllers/DashboardController.php`
- `app/Policies/StudentPolicy.php`
- `app/Policies/StudentFeeInvoicePolicy.php`
- `app/Policies/ReceiptPolicy.php`
- `app/Http/Controllers/Payments/ManualBankPaymentController.php`
- `app/Services/Payments/StudentFeeInvoicePayableResolver.php`
- `app/Http/Requests/Payments/InitiateShurjopayPaymentRequest.php`
- `app/Http/Requests/Payments/StoreManualBankPaymentRequest.php`
- `app/Services/Payments/PaymentWorkflowService.php`
- `tests/Feature/Phase12/RouteMiddlewarePolicyFinalizationTest.php`

### Routing / Middleware / Policy Changes Implemented

- Added explicit `donor.access` middleware and attached it to the `/donor` route bucket so donor routes now fail closed at the route edge on donor-domain eligibility instead of relying on controller-only checks.
- Added explicit `guardian.info.access` middleware and attached it to the `/guardian/info*` route bucket so the informational guardian surface now has its own dedicated eligibility middleware instead of a plain `auth` edge.
- Kept the existing `guardian.protected` route edge for the live `/guardian` protected surface and kept `portal.home` as the shared-home middleware used by the chooser-aware `/dashboard` route.
- Removed blanket `verified` from `payments.manual-bank.show` and the shurjoPay browser return routes after the explicit donor, guardian informational, guardian protected, and shared-home middleware layers were all in place.
- Attached `can:view,payment` to `payments.manual-bank.show` and introduced a reusable `PaymentPolicy` so payment-detail access now resolves through:
  - management access
  - exact payer-user ownership
  - protected guardian invoice ownership
- Moved payment-initiation authorization onto reusable `StudentFeeInvoicePolicy::pay()` checks via the payment form requests and the payable resolver, so invoice payment entry is object-authorized instead of depending on looser inline controller/service logic.
- Hardened `StudentPolicy` and `StudentFeeInvoicePolicy` to reuse protected guardian eligibility rather than only profile-flag checks, which keeps unverified or role-only guardian contexts fail-closed when policies are used outside the protected route group.
- Kept `ReceiptPolicy` domain-aware while aligning it with the stronger protected-invoice policy path for guardian-owned receipts.
- Closed the remaining management-surface leak for donor-profile users without raw donor roles by blocking donor-profile fallthrough in both `EnsureManagementSurfaceAccess` and the management-compatibility branch inside `DashboardController`.

### Compatibility Notes

- `routes/web.php`
  - impact class: `critical`
  - why touched: finalize the donor and guardian informational route buckets with their own explicit eligibility middleware
  - preserved behavior: `dashboard`, `donor.*`, `guardian.*`, `guardian.info.*`, and `management.*` route names remain unchanged
  - intentionally not changed: prompt-39 chooser rendering, switcher UI, management route naming, or protected guardian route names
- `routes/payments.php`
  - impact class: `critical`
  - why touched: remove the last blanket `verified` dependency from protected payment detail/return routes and move those routes onto reusable payment authorization
  - preserved behavior: guardian payment initiation still remains behind `auth + guardian.protected`, and management manual-bank review routes stay management-only
  - intentionally not changed: guest donor checkout routes, donor payment route names, or IPN routing
- `app/Policies/StudentFeeInvoicePolicy.php`
  - impact class: `critical`
  - why touched: make protected invoice view/pay authorization reflect the real protected guardian boundary instead of profile flags alone
  - preserved behavior: management override, guardian linkage checks, and `guardian_id` compatibility remain intact
  - intentionally not changed: invoice settlement behavior, receipt issuance, or payment posting semantics
- `app/Policies/PaymentPolicy.php`
  - impact class: `high`
  - why touched: consolidate payment-detail authorization into one reusable rule for exact payer, management, or protected guardian owner
  - preserved behavior: no raw route membership or raw guardian role now grants payment detail access
  - intentionally not changed: donor guest-status routes under `/donate`, or later donor/guest `payments.*` expansion
- `app/Http/Middleware/EnsureManagementSurfaceAccess.php`
  - impact class: `high`
  - why touched: prevent donor-profile accounts without a donor role row from slipping into legacy management surfaces
  - preserved behavior: legacy management access itself remains unchanged, and prompt-39's chooser path still stays outside the management dashboard body
  - intentionally not changed: management metrics, management navigation, or legacy transaction logic

## Durable Artifact Promotion

- Promoted approved decisions to `docs/codex/04-decisions/approved/prompt-40-route-middleware-policy-finalization.md`
- Promoted the reusable route-finalization workflow artifact to `docs/codex/05-artifacts/workflow/prompt-40-route-middleware-policy-finalization.md`

## Validation

- Focused prompt-40 policy/route validation:
  - `powershell.exe -Command "& 'C:\laragon\bin\php\php-8.2.9-Win32-vs16-x64\php.exe' artisan test --env=testing tests/Feature/Phase12/RouteMiddlewarePolicyFinalizationTest.php"`
    - result: `pass`
    - summary: `process exited 0`
- Cross-slice regression pack:
  - `powershell.exe -Command "& 'C:\laragon\bin\php\php-8.2.9-Win32-vs16-x64\php.exe' artisan test --env=testing tests/Feature/Phase12/RouteMiddlewarePolicyFinalizationTest.php tests/Feature/Phase12/MultiRoleHomeAndSwitchingTest.php tests/Feature/Phase12/DonorAuthAndPortalAccessTest.php tests/Feature/Phase12/GuardianInformationalPortalTest.php tests/Feature/Phase12/GuardianProtectedPortalGatingTest.php tests/Feature/Phase5/PaymentIntegrationTest.php"`
    - result: `pass`
    - summary: `process exited 0`

## Contradiction / Blocker Pass

- No contradiction was found with prompt-17's `MR4` scope, prompt-18's route/middleware/policy direction, prompt-25's `ROUTE-*` / `MULTI-*` / `POL-*` expectations, prompt-26's route-finalization risk posture, prompt-35 donor-domain rules, prompt-36 guardian informational separation, prompt-37 guardian protected gating, or prompt-39's approved chooser/switching behavior.
- Prompt-40 preserved the approved prompt-39 neutral chooser, additive switching, and no-mixed-records boundary.
- Prompt-40 did not reopen donor payable expansion, broader Google identity behavior, or shared-home UI changes.
- No product blocker was found.
- No correction pass is required.

## Risks

- Prompt-40 intentionally finalizes the current guardian-payment route and policy boundaries only; it does not add later donor or guest `payments.*` status/detail routes beyond the already-approved `/donate` checkout/status surfaces.
- Legacy management compatibility still relies on the existing non-portal account heuristics; prompt-40 closed the donor-profile leak but did not replace the legacy management identity model wholesale.

## Next Safe Slice

- `docs/codex/01-prompts/prompt-41-external-admission-url-implementation.md`
