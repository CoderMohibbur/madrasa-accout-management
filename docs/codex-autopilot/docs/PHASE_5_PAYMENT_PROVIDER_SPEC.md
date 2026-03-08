# PHASE 5 PAYMENT PROVIDER SPEC

## Status
- Payment spec preparation: complete
- Phase 5 sandbox code implementation: complete
- Live activation readiness: not approved

## Approved Provider Matrix

| Provider | Role In This Project | Current Status | Implementation Scope |
|---|---|---|---|
| `shurjopay` | Primary online payment gateway | approved | sandbox-first only until remaining security contract items are confirmed |
| `manual_bank` | Current offline/manual fallback | approved | admin-reviewed manual confirmation flow only |
| `bkash` | Future planned provider | documented only | do not implement in current Phase 5 scope |
| `nagad` | Future planned provider | documented only | do not implement in current Phase 5 scope |

## Strict Decision Checklist

- [x] Primary online provider selected: `shurjopay`
- [x] Current offline/manual fallback selected: `manual_bank`
- [x] Future providers restricted to documentation only: `bkash`, `nagad`
- [x] Sandbox-first implementation policy selected for all initial gateway work
- [x] Live credentials are treated as documented but inactive until explicit go-live confirmation
- [x] Base URLs and operator-owned credential sets exist for sandbox and live use
- [x] The project now has a concrete env/config-key surface for later implementation
- [ ] shurjoPay callback/IPN signature verification method is confirmed from provider documentation
- [ ] shurjoPay callback/IPN HTTP method, payload fields, and authoritative event identifier are confirmed
- [ ] shurjoPay server-to-server payment verification step is confirmed
- [ ] Exact redirect parameter contract for success, fail, and cancel returns is confirmed
- [ ] Receipt numbering rule for dedicated `receipts` rows is confirmed
- [ ] Initial live payment scope is explicitly narrowed or approved for donor payments
- [ ] Manual bank evidence storage surface is confirmed if file uploads are required

## Current Safe Scope

- Safe current online-gateway target surface: `StudentFeeInvoice` payments for guardians
- Safe current manual fallback target surface: `StudentFeeInvoice` manual bank confirmation requests
- Donor self-service live payment finalization is not yet safe to implement against legacy `transactions`
- If donor online payments are required in Phase 5, the project must first approve a dedicated donation payable surface or formally narrow Phase 5 to student-fee payments only

## Required Env / Config Contract

Actual usernames and passwords were supplied outside the repository on 2026-03-08. Do not store those secret values in tracked files. The repo should record only these key names and their runtime purpose.

| Key | Purpose | Secret | Notes |
|---|---|---|---|
| `PAYMENT_PRIMARY_PROVIDER` | Current online provider selector | no | set to `shurjopay` later |
| `PAYMENT_OFFLINE_FALLBACK_PROVIDER` | Current manual fallback selector | no | set to `manual_bank` later |
| `PAYMENT_PROVIDER_MODE` | Active gateway mode | no | must stay `sandbox` until explicit live approval |
| `SHURJOPAY_SANDBOX_BASE_URL` | Sandbox API base URL | no | provided |
| `SHURJOPAY_SANDBOX_API_USERNAME` | Sandbox API username | yes | provided out of band |
| `SHURJOPAY_SANDBOX_API_PASSWORD` | Sandbox API password | yes | provided out of band |
| `SHURJOPAY_LIVE_BASE_URL` | Live API base URL | no | provided but inactive |
| `SHURJOPAY_LIVE_API_USERNAME` | Live API username | yes | provided out of band, inactive |
| `SHURJOPAY_LIVE_API_PASSWORD` | Live API password | yes | provided out of band, inactive |
| `SHURJOPAY_ORDER_PREFIX` | Merchant order prefix | no | current value is documented as `HFS` |
| `SHURJOPAY_SUCCESS_URL` | Browser return URL after successful redirect | no | reserved route below |
| `SHURJOPAY_FAIL_URL` | Browser return URL after failure redirect | no | reserved route below |
| `SHURJOPAY_CANCEL_URL` | Browser return URL after cancellation redirect | no | reserved route below |
| `SHURJOPAY_CALLBACK_URL` | Server callback/IPN target URL | no | exact provider contract still unresolved |
| `SHURJOPAY_SIGNATURE_SECRET` | Signature or verification secret if provider requires one | yes | unresolved until provider docs confirm the mechanism |
| `MANUAL_BANK_ENABLED` | Toggle manual-bank fallback | no | current fallback method |
| `MANUAL_BANK_DISPLAY_NAME` | Public label for fallback method | no | e.g. "Bank Transfer" |
| `MANUAL_BANK_ACCOUNT_NAME` | Receiving account name | no | operator-maintained |
| `MANUAL_BANK_ACCOUNT_NUMBER` | Receiving account number | yes | treat as sensitive |
| `MANUAL_BANK_BANK_NAME` | Receiving bank name | no | operator-maintained |
| `MANUAL_BANK_BRANCH_NAME` | Receiving branch name | no | optional |
| `MANUAL_BANK_ROUTING_NUMBER` | Routing information | yes | optional if applicable |
| `MANUAL_BANK_INSTRUCTIONS` | User-facing payment instructions | no | operator-maintained |

## Reserved Project Routes

These are project-side route reservations for later implementation. They are not implemented in this task.

### shurjoPay
- Initiation route: `POST /payments/shurjopay/initiate`
- Success return URL: `GET /payments/shurjopay/return/success`
- Fail return URL: `GET /payments/shurjopay/return/fail`
- Cancel return URL: `GET /payments/shurjopay/return/cancel`
- Callback/IPN route: `POST /payments/shurjopay/ipn`

### Manual Bank
- Request route: `POST /payments/manual-bank/requests`
- User instructions/status page: `GET /payments/manual-bank/{payment}`
- Management review queue: `GET /management/payments/manual-bank`
- Management approval route: `POST /management/payments/manual-bank/{payment}/approve`
- Management rejection route: `POST /management/payments/manual-bank/{payment}/reject`

## Provider Spec: shurjoPay

### Provider Name
- `shurjopay`

### Provider Role In This Project
- Primary online gateway for future portal payments
- Current safe implementation target only for invoice-backed guardian payments until donor payable scope is clarified

### Merchant Endpoints / Portals
- Sandbox merchant portal: `https://sandbox.admin.shurjopayment.com`
- Sandbox API base URL: `https://sandbox.shurjopayment.com`
- Live merchant portal: `https://admin.shurjopayment.com`
- Live API base URL: `https://engine.shurjopayment.com`
- Point of contact: Shahidul Islam Khan, `shahidul.islam@shurjomukhi.com.bd`

### Sandbox vs Live Behavior
- All initial design, test cases, and future implementation must use sandbox credentials and sandbox URLs first
- Live credentials remain documented out of band only and must not be considered active for code or deployment until explicit confirmation is given
- `PAYMENT_PROVIDER_MODE` must remain `sandbox` until the project records a live go/no-go decision

### Merchant-Side Credentials / Config Keys Required
- Sandbox API username and password
- Live API username and password
- Sandbox and live base URLs
- `SHURJOPAY_ORDER_PREFIX`
- Return URLs: success, fail, cancel
- Callback/IPN URL
- Signature secret only if provider documentation confirms such a key exists

### Callback / Webhook / Return Flow
- Browser redirects are non-authoritative user experience events only
- Success redirect must not finalize payment on its own
- Fail redirect must not post ledger entries or issue receipts
- Cancel redirect must not post ledger entries or issue receipts
- Server callback/IPN or provider-side verification must be the authoritative finalization trigger

### Success URL
- Reserved project URL: `/payments/shurjopay/return/success`
- Purpose: show the user a pending or paid status page after a successful browser redirect
- Safety rule: do not mark the payment `paid` here unless the server has separately verified the provider result

### Fail URL
- Reserved project URL: `/payments/shurjopay/return/fail`
- Purpose: show failure messaging and a retry option
- Safety rule: only terminally mark `failed` when the provider result is verified or the callback contract confirms failure

### Cancel URL
- Reserved project URL: `/payments/shurjopay/return/cancel`
- Purpose: show cancellation messaging
- Safety rule: cancellation is non-posting and non-receipting

### Webhook / IPN Flow
- Reserved project endpoint: `/payments/shurjopay/ipn`
- Current requirement: every inbound event must be stored in `payment_gateway_events`
- Current unresolved items:
  - exact HTTP method and headers
  - authoritative provider event identifier
  - exact payload shape
  - whether shurjoPay sends a dedicated IPN, a callback, both, or requires a verify API call after browser return

### Signature Verification Method
- Unresolved
- Do not assume HMAC, shared-secret hashes, signed payload headers, or IP allowlisting until the provider documentation used by the project confirms the exact method
- Until confirmed, Phase 5 code implementation remains blocked

### Secret / Config Keys
- Required now:
  - `SHURJOPAY_SANDBOX_API_USERNAME`
  - `SHURJOPAY_SANDBOX_API_PASSWORD`
  - `SHURJOPAY_LIVE_API_USERNAME`
  - `SHURJOPAY_LIVE_API_PASSWORD`
  - `SHURJOPAY_ORDER_PREFIX`
- Conditionally required:
  - `SHURJOPAY_SIGNATURE_SECRET`

### Idempotency Rules
- One local `payments` row per payment attempt
- `payments.idempotency_key` must remain unique and must be generated before contacting the provider
- `payments.provider_reference` must remain unique once the provider returns or confirms it
- `payment_gateway_events(provider, provider_event_id)` must remain unique
- If shurjoPay does not provide a stable event identifier, Phase 5 code may not proceed until a deterministic local deduplication rule is documented

### Order / Reference Format
- Current project intent: outbound provider order/reference values must begin with `HFS`
- Reserved local format: `HFS-<channel>-<payment_id>`
- Allowed channel values:
  - `INV` for student fee invoices
  - `DON` reserved only for a future dedicated donor payable model
- Unresolved provider confirmation:
  - maximum length
  - allowed characters
  - whether the provider returns this exact reference unchanged

### Receipt Issuance Trigger
- Issue exactly one dedicated `receipts` row after the payment has been authoritatively verified as successful
- Never issue a receipt during initiation
- Never issue a receipt from browser return alone
- Never issue more than one receipt for a single `payments.id`

### Payment Finalization Trigger
- Finalization requires all of the following:
  - matching local `payments` row in a non-terminal state
  - verified successful provider outcome from IPN/callback or explicit provider verification
  - duplicate/idempotency checks passed
  - transactional creation of any receipt and posted accounting side effects
- Guardian invoice payments may use `StudentFeeInvoice` as the payable source
- Donor payments must not finalize against legacy `transactions` rows

### Duplicate-Callback Handling
- Persist every inbound provider event attempt into `payment_gateway_events`
- If a valid duplicate callback arrives after `payments.status = paid`, acknowledge it and no-op the business finalization
- If the callback cannot be matched safely, store it as received and route it to manual review

### Retry Handling
- Retry must reuse the existing local payable resolution rules and create a new payment attempt only if the prior attempt is definitively non-successful
- Do not create a second attempt with the same `idempotency_key`
- If initiation result is ambiguous, reconcile first through provider verification before presenting another pay action

### Failure Handling
- Failure is non-posting
- Mark `failed` only after verified provider failure or explicit manual review
- Store raw failure payload in `payment_gateway_events.payload`
- Write an `audit_logs` entry for the transition

### Cancellation Handling
- Cancellation is non-posting
- Browser-side cancellation must not generate a receipt
- If the provider later reports success after a user-side cancel screen, the system must defer to the authoritative provider verification result and route conflicting signals to manual review

### Manual Reconciliation Requirements
- Daily reconcile local `payments`, `payment_gateway_events`, and `receipts` against the relevant shurjoPay merchant portal
- Reconcile all `paid` rows against merchant settlement/reference data
- Reconcile all `pending`, `manual_review`, `failed`, or unmatched events before end-of-day close
- Keep sandbox reconciliation separate from live reconciliation

### Audit Logging Requirements
- Log payment initiation request creation
- Log provider redirect initiation response metadata
- Log each callback/IPN receipt
- Log verification attempts and results
- Log every terminal state transition
- Log receipt issuance
- Log duplicate callback suppression
- Log any manual override or reconciliation correction

### Transaction Posting Safety Notes
- Use the canonical posting path introduced in Phase 1
- Continue to enforce `credit = inflow`, `debit = outflow`
- Do not use legacy `TransactionsController` or `LenderController` assumptions
- Do not post accounting entries until payment verification succeeds
- Keep payment finalization, receipt issuance, and accounting posting in one transactional unit where practical

### Accounting / Reporting Protection Notes
- Do not mutate legacy `reports.*` semantics during payment integration
- Do not synthesize receipts from legacy `transactions`
- Do not allow browser-submitted free amounts to override server-side invoice totals
- Keep legacy manual financial entry screens separate from the new payment flow

## Provider Spec: Manual Bank

### Provider Name
- `manual_bank`

### Provider Role In This Project
- Current offline/manual fallback when online gateway use is not possible or intentionally bypassed
- Must coexist with shurjoPay without sharing provider references or finalization rules

### Initiation Endpoint / Base URL
- No external provider base URL
- Internal request route reserved: `POST /payments/manual-bank/requests`

### Sandbox vs Live Behavior
- Not provider-hosted
- The same internal flow may be used in test and live environments, but all bank-account details remain environment-managed operational data

### Merchant-Side Credentials / Config Keys Required
- `MANUAL_BANK_ENABLED`
- `MANUAL_BANK_DISPLAY_NAME`
- `MANUAL_BANK_ACCOUNT_NAME`
- `MANUAL_BANK_ACCOUNT_NUMBER`
- `MANUAL_BANK_BANK_NAME`
- `MANUAL_BANK_BRANCH_NAME`
- `MANUAL_BANK_ROUTING_NUMBER`
- `MANUAL_BANK_INSTRUCTIONS`

### Callback / Webhook Return Flow
- No provider callback is assumed
- Finalization occurs only after management review and approval

### Success / Fail / Cancel Handling
- User submission creates a non-posting manual-review payment request
- Approval is the only success trigger
- Rejection is the failure trigger
- User abandonment before submission is a cancel/no-op path

### Signature Verification Method
- Not applicable
- Approval authority must come from authenticated internal management users, not the payer

### Idempotency Rules
- One manual request per intended payment attempt
- Require a unique local `idempotency_key`
- Require a unique payer-submitted bank transfer reference where possible
- Re-submission with the same evidence should reopen review of the existing request, not create duplicate paid rows

### Order / Reference Format
- Use a local reference only: `MB-<channel>-<payment_id>`
- `INV` is approved for student fee invoices
- `DON` remains reserved until donor payable scope is approved

### Receipt Issuance Trigger
- Issue a receipt only after management approval verifies that the funds were received

### Payment Finalization Trigger
- Required approvals:
  - evidence submitted by the payer
  - management verification against bank records
  - audit log of approver identity and decision timestamp
- Finalization must use the same canonical posting path as shurjoPay

### Duplicate Handling
- Duplicate evidence or duplicate bank references must route to manual review
- Only one approved payment row may exist per reconciled bank transfer

### Retry Handling
- Users may resubmit evidence only while the request is pending or rejected
- Approved requests may not be retried or replaced by a new paid row

### Failure Handling
- Rejected or unverifiable requests remain non-posting
- Record rejection reason in `payments.metadata` and `audit_logs.context`

### Cancellation Handling
- Users may cancel only before an approver acts
- Cancelled manual requests remain non-posting and non-receipting

### Manual Approval And Evidence Requirements
- Minimum evidence required:
  - payer name
  - target bank/payment channel
  - transfer or deposit reference
  - transaction date and time
  - amount
  - proof artifact or screenshot if the project enables uploads
- Minimum management approval record:
  - approver user id
  - approval timestamp
  - matched bank/account reference
  - decision note

### Manual Reconciliation Requirements
- Compare each approved request with bank statements or merchant bank logs before receipt issuance
- Keep unresolved requests in a review queue
- Reconcile approved manual-bank rows separately from shurjoPay rows in management reporting

### Audit Logging Requirements
- Log request submission
- Log evidence updates
- Log approval, rejection, and cancellation decisions
- Log receipt issuance and accounting posting after approval

### Transaction Posting Safety Notes
- Same Phase 1 canonical posting rules apply
- No posting before approval
- No receipt before approval

### Accounting / Reporting Protection Notes
- Manual-bank payments must remain distinguishable from shurjoPay in `payments.provider`
- Manual-bank approval must not backfill or overwrite legacy transaction rows implicitly

## Future Provider Expansion Notes

- `bkash` and `nagad` remain future planned providers only
- Do not add current-phase code paths, env keys, routes, or payment finalization logic for them
- Future provider onboarding must follow the same checklist used here:
  - business approval
  - sandbox/live contract
  - callback/IPN verification contract
  - env/config namespace
  - idempotency and receipt rules
  - reconciliation and audit requirements
- Future providers must fit into the existing `payments.provider` and `payment_gateway_events.provider` surfaces without changing current shurjoPay/manual-bank semantics

## Remaining Items Before Live Activation / Hardening Closure

1. Reconfirm any shurjoPay callback signature secret or updated callback contract before live activation if the provider later supplies one beyond the verify-by-order-id flow used in sandbox.
2. Confirm the final live return/cancel behavior and merchant-panel callback configuration before go-live.
3. Confirm the operator-approved account mapping before enabling canonical posting into legacy `transactions`.
4. Confirm the dedicated donor payable surface before donor online payments are attempted.
5. Confirm whether manual bank evidence needs upload storage beyond the non-file sandbox evidence fields used in Phase 5.
6. Confirm the explicit live go/no-go decision before any live credential activation.

## Implementation Readiness Decision

- `PHASE 5 SANDBOX IMPLEMENTATION COMPLETE`
- The Laravel repository now has a safe sandbox-only shurjoPay plus manual-bank scaffold.
- Live activation, live merchant changes, WordPress IPN cutover, and donor online payments remain out of scope until the remaining items above are resolved.
