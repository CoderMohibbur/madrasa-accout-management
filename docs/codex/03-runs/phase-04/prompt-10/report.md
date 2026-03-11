# Report

This prompt defines the donor payable model after prompt-09 froze the permission split between donation capability and donor portal eligibility. The live repo already has reusable payment infrastructure in `payments`, `payment_gateway_events`, and `receipts`, but every finalized write path still assumes an invoice-backed guardian payment. Donor live payment therefore needs a dedicated donor-domain payable path instead of reusing legacy `transactions` rows or the invoice finalization flow.

## Repo-Grounded Current-State Constraints

- `payments.user_id` is already nullable and `payments` already supports a polymorphic `payable`, so the core attempt table can support both guest and identified donor flows.
- `PaymentWorkflowService::finalizeAsPaid()` currently hard-fails unless the payable is a `StudentFeeInvoice`, updates invoice balances, issues the receipt to `payment.user_id`, and optionally creates a legacy `transactions` row for student-fee posting.
- `routes/payments.php` currently exposes payment initiation only for `auth` + `verified` + `role:guardian`, and return pages only for `auth` + `verified`, so the live routing layer is narrower than the target donor rules.
- `DonorPortalData` still reads donor history from legacy `transactions` by `doner_id`, while donor receipts come from `receipts` tied to `issued_to_user_id` or `payment.user_id`.
- `docs/codex-autopilot/state/risk_register.md` already freezes donor online payment against legacy `transactions` as unsafe until a dedicated donor payable model exists.

## Legacy Donor Payment Risks

Legacy `transactions` are unsafe or insufficient as the canonical donor live-payment finalization surface because they are accounting/report rows, not checkout-settlement rows.

- They have no pre-settlement state for donation creation, checkout initiation, redirect pending, callback waiting, timeout, retry, or manual review.
- They have no gateway-native idempotency, provider-order binding, or event log linkage comparable to `payments.idempotency_key`, `provider_reference`, and `payment_gateway_events`.
- They cannot safely represent multiple payment attempts for one donation intent without double-counting reports or creating ambiguous duplicate rows.
- They mix donor reporting and accounting semantics, while the risk register already warns that report totals are tightly coupled to legacy `transactions`.
- They do not distinguish guest donation, identified donation, and anonymous-display donation cleanly.
- They do not provide a narrow guest-access boundary for payment status and receipt retrieval.
- They would make donor payment success depend on legacy posting semantics, even though posting is intentionally optional and risk-sensitive in the current payment stack.

## Target Donor Payable Model

The safest small-domain model is:

1. `donation_intent`
   - one pre-settlement donation checkout record
   - this is the donor-side payable object that `payments.payable_type/payable_id` should target
   - stores the intended amount, currency, donor classification, anonymous-display preference, identity snapshot, contact snapshot, public reference, and guest/identified access material
2. `payment attempt`
   - reuse the existing `payments` table for each provider or manual attempt
   - one intent may have many attempts over time
3. `donation_record`
   - one post-settlement business record created only after a payment is authoritatively finalized
   - this is the canonical donor-domain “completed donation” row
4. `receipt`
   - one donor-facing receipt row for the settled donation payment
   - receipt access stays narrower than donor portal history

### Do We Need Both Donation Intent And Donation Payable?

For the smallest safe rollout, no second pre-settlement table is needed. The system needs a dedicated `donation_intent`, and that intent itself should be the `Payment.payable`.

- `donation_intent`: yes
- separate `donation_payable` table: no, not for the minimal safe rollout
- `payment attempt`: yes, via `payments`
- `donation_record`: yes
- `receipt record`: yes, via `receipts`

If the project later introduces campaign splits, partial allocations, or multi-beneficiary funding, the intent can be split from a richer payable/allocation model later. That extra layer is not required to safely launch one-time donor checkout.

## Guest / Identified Donation Record Model

`donation_intent` should carry the donor-mode and donor-visibility rules before money settles:

- `donor_mode`
  - `guest`
  - `identified`
- `display_mode`
  - `identified`
  - `anonymous_display`
- `user_id`
  - nullable
  - set only for logged-in or explicitly linked identified donations
- `donor_id`
  - nullable
  - set only when there is a safe internal donor profile match or explicit later linkage
- `name_snapshot`, `email_snapshot`, `phone_snapshot`
  - optional human identity capture
  - any guest-supplied values remain operational and unverified unless the account-verification model says otherwise
- `public_reference`
  - non-guessable donor-facing reference for status tracking
- `guest_access_token_hash` or equivalent opaque claimant secret
  - required for public guest status / receipt retrieval

`donation_record` should be created only once a payment is verified or manually approved as truly settled:

- `donation_intent_id`
- `winning_payment_id`
- `user_id` nullable
- `donor_id` nullable
- `donor_mode`
- `display_mode`
- `amount`
- `currency`
- `donated_at`
- `receipt_id`
- `posting_status`
- `operational_contact_snapshot`

Classification rules:

- `guest donation`
  - `user_id` remains null
  - optional name/email/phone may be stored as operational contact only
  - no registration, portal eligibility, or donor-profile linkage is created automatically
- `identified donation`
  - tied to a known `user_id` and optionally a known donor profile
  - donor login still does not require verification by rule freeze
  - donor payment still does not require portal eligibility
- `anonymous-display donation`
  - not a third ownership class
  - it is a display preference stored on either guest or identified donation rows
  - internal traceability must still remain intact

## Payment Success / Failure / Retry / Callback / Cancellation / Timeout / Reconciliation

### 1. Intent creation

- Create one `donation_intent` before redirecting to any gateway.
- Capture amount, currency, donor mode, optional contact info, display preference, and public access reference.
- Do not create legacy `transactions` or donor portal history rows here.

### 2. Attempt creation

- Create a `payments` row for each checkout attempt against the `donation_intent`.
- Reuse `idempotency_key`, `provider_reference`, and `payment_gateway_events`.
- Logged-in donor attempts may set `payments.user_id`; guest attempts keep it null.

### 3. Browser success/fail/cancel return

- Browser return pages are informational only.
- A success return must never finalize the donation without authoritative server-side verification.
- A fail or cancel return may mark the attempt as failed or cancelled only when that outcome is authoritative enough; otherwise keep it pending verification.
- Guest and identified donors both need narrow attempt-status visibility without implying donor portal eligibility.

### 4. Callback and verify flow

- The system must match callback/verify results to the attempt by provider order id plus the local merchant/reference binding.
- Amount and currency must match the local attempt and intent.
- Mismatch or ambiguity routes the attempt to manual review.
- Repeated callbacks must be idempotent; the already-finalized donation record should simply be returned.

### 5. Success finalization

On authoritative success:

- lock the target payment attempt
- confirm the intent is still open and not already settled
- mark the payment as paid / verified
- create exactly one `donation_record` if none exists
- create exactly one receipt row
- mark the `donation_intent` as succeeded / settled
- record posting as `pending`, `skipped`, or `deferred`; do not force legacy posting inline

### 6. Failure

- Failed verification or provider-declared failure should terminalize only the attempt, not the whole donor identity.
- No `donation_record` and no receipt should be created for failed attempts.
- The `donation_intent` may remain retryable unless the business rule explicitly marks it expired.

### 7. Retry

- Retry should create a new `payments` row under the same still-open `donation_intent`.
- Old attempts remain immutable audit history.
- Do not reuse a paid or manual-review-blocked attempt as if it were a fresh checkout.
- Do not create a second donation record when a later callback arrives for a previously failed or superseded attempt.

### 8. Cancellation

- Browser cancellation is advisory until the provider outcome is verified.
- If a verified success arrives after browser cancellation, route to reconciliation/manual review instead of silently trusting the earlier cancel page.

### 9. Timeout

- An intent or attempt that never receives authoritative success within the configured window should become `expired` or `failed_by_timeout`.
- Timeout should block silent reuse of stale provider references.
- Timeout does not create a donation record or receipt.

### 10. Reconciliation

- Ambiguous outcomes, return/callback conflicts, duplicate provider events, or provider-verify failures must route to manual review.
- Reconciliation acts on `payments` and `donation_intent`; it must not invent legacy `transactions` as proof of settlement.
- Manual approval may finalize a donation only when operational evidence proves the funds settled.

## Receipt Model

Receipts stay post-settlement and payment-specific.

- A receipt is not created at intent creation or redirect start.
- A payment status page is not the same thing as a receipt.
- A receipt list in the donor portal is not the same thing as transaction-specific receipt access.

Guest receipt rules:

- `receipts.issued_to_user_id` may remain null for guest donations.
- Guest receipt access should use the same opaque public reference plus guest secret, or another equally strong non-guessable retrieval token.
- If no contact method was supplied and the guest loses the opaque reference, recovery may require manual support.
- Guest receipt visibility remains transaction-specific only; there is no account-wide history.

Identified receipt rules:

- Logged-in identified donors may receive `issued_to_user_id = user_id`.
- They may access the receipt for their own donation even if donor portal eligibility is absent.
- Full receipt-history browsing remains a later donor-portal boundary and must not be inferred from simple payment success.

Shared receipt content rules:

- Receipt should reflect the settled donation amount, currency, provider, settled timestamp, and public donor display mode.
- Receipt should not rely on a legacy accounting row existing.
- Anonymous-display affects public presentation only; internal receipt and reconciliation data remain complete.

## Posting Separation Rules

Donor settlement must remain separate from legacy accounting posting.

- `donation_record` is the donor-domain truth that money settled.
- `transactions` is not the donor-domain source of truth for gateway settlement.
- Donor payment success must not depend on creating a legacy `transactions` row in the same transaction.
- Posting failure must not retroactively erase a valid donor receipt or settled donor payment.
- Any later posting adapter should read from the settled donor record and write accounting entries in a separate, retryable step.
- Legacy donor portal history may remain narrower or partially legacy-backed until later implementation phases explicitly bridge the new donor record model into portal views.

## Minimal Safe Live-Payment Rollout Model

The smallest safe donor live-payment rollout is:

- one-time donor checkout only
- guest donation allowed
- identified logged-in donor donation allowed
- anonymous-display flag allowed on both guest and identified donations
- one dedicated `donation_intent` payable model reused by `payments`
- one gateway-backed online flow with authoritative verify/callback handling
- transaction-specific status and receipt access through:
  - logged-in ownership for identified donors
  - opaque public reference for guests
- no donor portal eligibility requirement for making a donation
- no recurring donations
- no saved payment methods
- no automatic account creation or account linking from guest contact capture
- no inline dependency on legacy `transactions` posting
- no assumption that old donor `transactions` can be used as live donor payment rows

Manual-bank donor checkout can be added later, but it should not be in the smallest safe rollout because it adds manual-review, proof-of-payment, and guest-retrieval complexity before the new donor payable model is proven.

## Prompt-11 Readiness

No hard blocker was discovered inside prompt-10 scope. Prompt-11 should next design guest donation onboarding on top of:

- guest and identified donation sharing one donor intent + payment-attempt model
- opaque guest status / receipt retrieval
- verification-independent donor payment ability
- donor portal eligibility remaining separate from payment completion
