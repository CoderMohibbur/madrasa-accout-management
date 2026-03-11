# Report

## Scope Lock Before Coding

### Exact Approved Slice

- Prompt-34 is limited to donor slices `P1` -> `P2` -> `G2` -> `A1`.
- The slice is only:
  - additive donor-domain schema for `donation_intents` and `donation_records`
  - additive donor checkout service/controller/view wiring on top of the prompt-33 `/donate` session draft
  - guest live checkout activation with narrow transaction-specific status/receipt access
  - authenticated identified checkout activation without donor portal gating or donor portal/history rollout
- Prompt-34 must preserve prompt-31 registration behavior, prompt-32 verification separation, and prompt-33's approved guest entry shell.

### Approved Donor Payment-Domain Model Restated

- The approved donor flow is `donation_intent -> payments -> donation_record -> receipt`.
- `donation_intent` is the donor-side payable object for `payments`; no second pre-settlement donor payable table is needed in the minimal rollout.
- `payments` remains the attempt table for both guest and identified donors.
- `donation_record` is created only after authoritative server-side verification.
- Guest and identified donation share one safe donor payable model, while `anonymous_display` stays a visibility preference only.

### What Legacy Behavior Must Not Be Reused Unsafely

- No donor payment finalization against legacy `transactions`.
- No donor portal/history bridge in this step.
- No donor auth/middleware redesign in this step.
- No unrelated guardian or management changes.
- No donor manual-bank rollout or broader accounting redesign.
- No account creation, donor-profile creation, or donor portal eligibility side effect from guest checkout.

## Implementation Result

Prompt-34 completed inside the approved donor payable foundation scope.

### Files Changed

- `database/migrations/2026_03_09_020000_create_donation_intents_and_records_tables.php`
- `app/Models/DonationIntent.php`
- `app/Models/DonationRecord.php`
- `app/Services/Donations/DonationReferenceGenerator.php`
- `app/Services/Donations/DonationCheckoutService.php`
- `app/Http/Controllers/Donations/DonationCheckoutController.php`
- `app/Http/Controllers/Donations/GuestDonationEntryController.php`
- `app/Http/Controllers/Payments/ShurjopayPaymentController.php`
- `app/Services/Payments/Shurjopay/ShurjopayClient.php`
- `resources/views/donations/guest-entry.blade.php`
- `resources/views/donations/status.blade.php`
- `routes/web.php`
- `tests/Feature/Phase12/DonorPayableFoundationTest.php`

### Donor Payable Foundation Implemented

- Added dark donor-domain schema for:
  - `donation_intents`
  - `donation_records`
- Added dedicated donor-domain models with:
  - `donor_mode`
  - `display_mode`
  - nullable `user_id`
  - nullable `donor_id`
  - `public_reference`
  - opaque `guest_access_token_hash`
  - contact snapshots
  - settlement/posting state separation
- Added a donor reference/access-key generator so public guest status access stays opaque and transaction-specific.
- Added a dedicated `DonationCheckoutService` that:
  - reuses the prompt-33 session draft handoff
  - creates one `donation_intent`
  - creates `payments` attempts against that intent
  - reuses the existing sandbox shurjoPay client through an additive generic initiation helper
  - verifies success server-side before finalization
  - creates exactly one `donation_record` and one receipt after verified settlement
  - keeps donor posting marked `skipped` instead of creating legacy `transactions`
  - routes mismatches or ambiguous outcomes to manual review
  - reuses the same open intent for retry when the session-draft checkout is restarted after a failed attempt

### Guest / Identified Donation Behavior Implemented

- Prompt-33's `GET /donate` plus `POST /donate/start` guest entry shell remains intact.
- Added prompt-34 routes:
  - `POST /donate/checkout`
  - `GET /donate/payments/{publicReference}`
  - `GET /donate/return/success`
  - `GET /donate/return/fail`
  - `GET /donate/return/cancel`
- Public guest checkout:
  - keeps `user_id` null
  - creates no `users` row
  - creates no donor profile
  - exposes only transaction-specific status/receipt access through `public_reference` plus access key
- Authenticated identified checkout:
  - starts from the same `/donate` session draft handoff
  - populates `donation_intent.user_id` and `payments.user_id`
  - links `donor_id` only when a safe existing donor profile already exists
  - does not require donor role or portal eligibility
  - does not create a donor profile when none exists
- Updated the public donation page so the saved session draft now exposes a secure checkout continuation card and clearly states the guest vs identified outcome boundary.

### Live Checkout And Status Handling Implemented

- Guest and identified donor checkout now initiate sandbox shurjoPay and redirect to provider checkout.
- Browser success/fail/cancel routes remain informational surfaces backed by server-side verification.
- Donor-specific success finalization:
  - marks the payment paid/verified
  - creates the settled `donation_record`
  - issues the receipt
  - marks donor posting `skipped`
  - never creates a legacy `transactions` row
- Donor-specific mismatch handling routes the payment/intent to manual review and creates no `donation_record` or receipt.
- The existing global shurjoPay IPN endpoint now dispatches donor order ids to the donor checkout service without disturbing the guardian invoice flow.

### Legacy Safety Preserved

- Guardian invoice checkout, guardian manual-bank review, and guardian receipt/report flows were not redesigned.
- Donor portal routes, donor history queries, and donor receipt-history read paths remain untouched for prompt-35.
- Prompt-31 registration and prompt-32 verification flows remain unchanged.
- Prompt-33's approved guest-entry shell remains the starting point; the only prompt-34 integration touch there is the live checkout continuation and session reset on fresh draft entry.
- No route names were renamed.

### Compatibility Notes For Existing Files

- `routes/web.php`
  - impact class: `critical`
  - why touched: add additive public donor checkout/status/return routes
  - preserved behavior: existing `dashboard`, `guardian.*`, `donor.*`, `payments.*`, and management route names remain unchanged
  - intentionally not changed: donor portal gating, guardian route boundaries, management routing, legacy route names
  - regression checks: `artisan route:list --path=donate`, prompt-34 donor tests, prompt-33 carry-forward tests
  - rollback note: reverting this file removes only the new donor checkout/status route surface
- `app/Http/Controllers/Payments/ShurjopayPaymentController.php`
  - impact class: `critical`
  - why touched: dispatch donor shurjoPay IPN rows to the donor checkout service by payable type
  - preserved behavior: guardian invoice return pages, guardian IPN handling, and JSON response format remain intact for non-donor payables
  - intentionally not changed: guardian initiate/success/fail/cancel controller flow
  - regression checks: prompt-34 donor tests, phase-5 payment integration tests remain the expected later validation slice
  - rollback note: reverting this file restores the old invoice-only IPN handling
- `app/Services/Payments/Shurjopay/ShurjopayClient.php`
  - impact class: `high`
  - why touched: add a generic initiation helper so donor checkout can reuse sandbox shurjoPay without rewriting the guardian invoice payload path
  - preserved behavior: existing invoice initiation still builds the same callback URLs and payload defaults
  - intentionally not changed: authentication, verification, live-mode block, endpoint selection
  - regression checks: prompt-34 donor tests, `artisan route:list --path=donate`
  - rollback note: reverting this file removes donor reuse of the gateway client only
- `app/Http/Controllers/Donations/GuestDonationEntryController.php`
  - impact class: `medium`
  - why touched: clear current donor-intent/access-key session state when a new prompt-33 draft is started
  - preserved behavior: session draft contract, optional contact capture, and guest-only storage boundary remain intact
  - intentionally not changed: amount/contact validation contract
  - regression checks: prompt-33 guest donation tests
  - rollback note: reverting this file removes the prompt-34 session-reset protection only
- `resources/views/donations/guest-entry.blade.php`
  - impact class: `low`
  - why touched: expose the prompt-34 secure checkout continuation without replacing the prompt-33 entry shell
  - preserved behavior: public guest-entry presentation, optional fields, and registration/sign-in guidance remain in place
  - intentionally not changed: welcome CTA routing, portal shells, protected views
  - regression checks: prompt-33 guest donation tests, prompt-34 donor tests
  - rollback note: reverting this file restores the prompt-33 entry-only screen

## Durable Artifact Promotion

- Promoted approved decisions to `docs/codex/04-decisions/approved/prompt-34-donor-payable-foundation.md`
- Promoted the reusable donor payable workflow artifact to `docs/codex/05-artifacts/workflow/prompt-34-donor-payable-foundation.md`

## Validation

- `"/mnt/c/laragon/bin/php/php-8.2.9-Win32-vs16-x64/php.exe" artisan route:list --path=donate`
  - result: `pass`
  - summary: prompt-34 donor checkout/status/return routes are registered alongside prompt-33 guest entry routes
- `"/mnt/c/laragon/bin/php/php-8.2.9-Win32-vs16-x64/php.exe" artisan test --env=testing tests/Feature/Phase12/DonorPayableFoundationTest.php`
  - result: `pass`
  - summary: `5 passed (85 assertions)`
- `"/mnt/c/laragon/bin/php/php-8.2.9-Win32-vs16-x64/php.exe" artisan test --env=testing tests/Feature/Phase12/DonorPayableFoundationTest.php tests/Feature/Phase12/GuestDonationEntryTest.php tests/Feature/Phase12/OpenRegistrationFoundationTest.php tests/Feature/Phase12/EmailPhoneVerificationFoundationTest.php`
  - result: `pass`
  - summary: `19 passed (222 assertions)`

## Contradiction / Blocker Pass

- No contradiction was found with prompt-10's approved donor payable model, prompt-11's guest onboarding rules, prompt-12's donor slice order, or prompt-33's approved guest entry shell.
- Prompt-34 reused the prompt-33 session draft handoff and did not reopen earlier prompt decisions.
- Guest checkout remains guest by default; authenticated checkout is account-linked only and does not pull donor auth/portal/history behavior forward.
- Donor settlement remains separated from legacy accounting posting and from legacy donor `transactions`.
- No guardian behavior was widened.
- Runtime validation had to use the approved Laragon PHP runtime because direct Windows-binary execution from the default shell remained inconsistent for some commands.
- Runtime Git cleanliness still could not be reconfirmed with `git status` because the current environment does not expose a working `git` executable; `.git/HEAD` still points at `refs/heads/codex/2026-03-08-phase-1-foundation-safety`.
- No product blocker was found.
- No correction pass is required.

## Risks

- Donor checkout is still sandbox-only because the shared shurjoPay client keeps live mode intentionally disabled.
- Prompt-34 ships only the online donor payable flow; donor manual-bank, donor claim/account-link, and donor portal/history bridges remain deferred.
- Prompt-35 must adapt donor auth/portal/history read paths carefully so new `donation_records` do not double-count or silently merge with legacy donor `transactions`.
- The current runtime still lacks a usable native `git` command, so Git-based workflow enforcement remains an environment limitation rather than a product blocker.

## Next Safe Slice

- `docs/codex/01-prompts/prompt-35-donor-auth-and-portal-access.md`
