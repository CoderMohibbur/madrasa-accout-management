# Report

This prompt re-runs donor permission analysis against the live repository plus approved prompt-01 to prompt-08 outputs. The current repo already has a read-only donor portal, but it still gates that portal through `auth` + `verified` + `role:donor` plus donor profile flags, and it still has no donor-side payment initiation or guest donation flow. Prompt-09 therefore defines the target donor permission matrix without treating current donor portal behavior or legacy `transactions` writes as the final donor payment model.

## Current-State Donor Caveats

- The live donor portal is read-only and route-gated by `auth`, `verified`, `role:donor`, and donor profile lifecycle flags.
- Donor portal visibility currently exposes only:
  - donor dashboard
  - donation history from legacy `transactions` rows tied to `doner_id`
  - receipts tied through `issued_to_user_id` or `payment.user_id`
- There is no donor-side payment initiation route in `routes/payments.php`; write-capable payment routes are guardian-invoice-only.
- The donor UI itself states that online donation initiation remains blocked until a later payment phase.
- The public site links to an external donation URL, but the repo does not currently implement the target guest-donation or identified donor donation flow internally.
- Because prompt-10 has not yet defined the donor payable model, donor donation completion must not be assumed safe on top of legacy `transactions` finalization.

## Donor Permission Matrix

Legend:
- `Allowed` means target-allowed under the frozen business rules.
- `Allowed with conditions` means verification is not the gate, but separate approval/activity/payment-model constraints still apply.
- `Blocked` means outside the safe donor scope or deferred from the initial rollout.

Public registration note:
- Public donor registration remains allowed from the no-account state under prompt-06.
- The matrix below starts once an identified donor account already exists.

| Action | Registered donor, not verified, not portal-eligible | Registered donor, verified, not portal-eligible | Registered donor, portal-eligible (verification optional) |
| --- | --- | --- | --- |
| Registration | Blocked / not applicable once account already exists | Blocked / not applicable once account already exists | Blocked / not applicable once account already exists |
| Login | Allowed with conditions: verification is not required, but approval and active account state may still be required | Allowed with conditions: same approval/activity checks still apply | Allowed with conditions: same approval/activity checks still apply |
| Guest donation initiation | Blocked in the initial safe rollout once logged in; use identified donation flow instead of mixing guest and account flows | Blocked in the initial safe rollout once logged in; use identified donation flow instead of mixing guest and account flows | Blocked in the initial safe rollout once logged in; use identified donation flow instead of mixing guest and account flows |
| Guest donation completion | Blocked for the same reason; anonymous-display options should live inside identified donation settings later, not in a second guest checkout path | Blocked for the same reason | Blocked for the same reason |
| Identified donation initiation | Allowed with conditions: must use a dedicated donor payable flow, not legacy transaction posting shortcuts, and does not require portal eligibility | Allowed with conditions: same dedicated donor payable requirement; verification does not add new donor-payment permission by itself | Allowed with conditions: same dedicated donor payable requirement; portal eligibility still is not the payment gate |
| Identified donation completion | Allowed with conditions: completion must come from the future donor payable model with server-side verification/reconciliation; not from raw legacy donation rows | Allowed with conditions: same rule | Allowed with conditions: same rule |
| Receipt access | Allowed only for transaction-specific receipts or payment-result pages for the donor's own donation attempts; no account-wide receipt history yet | Allowed only for transaction-specific receipts or payment-result pages for the donor's own donation attempts; no account-wide receipt history yet | Allowed for donor-owned receipt history plus individual receipt visibility tied to that donor/account |
| Donation history | Blocked until donor portal eligibility exists | Blocked until donor portal eligibility exists | Allowed for donor-owned donation history only |
| Profile edit | Allowed for base account profile after login; donor-domain profile editing stays limited until a donor settings surface exists | Allowed for base account profile after login; donor-domain profile editing stays limited until a donor settings surface exists | Allowed for base account profile; donor-domain profile settings may be enabled later inside the portal, but are not required for the initial rollout |
| Payment status page access | Allowed for the donor's own donation attempt via authenticated access or a secure opaque reference; portal history is not required | Allowed for the donor's own donation attempt via authenticated access or a secure opaque reference | Allowed for the donor's own donation attempt and may also appear in portal-linked receipt/history surfaces |
| Recurring donation | Blocked in the initial safe rollout; requires a dedicated donor payable model, recurrence rules, failure handling, and consent controls | Blocked in the initial safe rollout | Blocked in the initial safe rollout |
| Saved payment methods | Blocked in the initial safe rollout; requires tokenized provider support and a stronger risk model | Blocked in the initial safe rollout | Blocked in the initial safe rollout |

## Guest Donation Permission Matrix

Guest identity-capture note:
- `name` is optional metadata in every guest row below.
- Supplying a name does not change permission class by itself.
- Human identity fields remain optional, but operational traceability remains mandatory in every case.

| Action | Guest donor: no identity data | Guest donor: phone only | Guest donor: email only | Guest donor: phone plus email |
| --- | --- | --- | --- | --- |
| Registration | Allowed but optional; guest donation must not require prior registration | Allowed but optional | Allowed but optional | Allowed but optional |
| Login | Blocked: no account exists yet | Blocked: no account exists yet | Blocked: no account exists yet | Blocked: no account exists yet |
| Guest donation initiation | Allowed with conditions: operational traceability fields must still be captured even if name/phone/email are omitted | Allowed with conditions: phone is unverified operational contact only | Allowed with conditions: email is unverified operational contact only | Allowed with conditions: both channels are unverified operational contact only |
| Guest donation completion | Allowed with conditions: must use a future dedicated donor payable model with reconciliation-safe completion; not legacy transaction shortcuts | Allowed with conditions: same dedicated donor payable rule | Allowed with conditions: same dedicated donor payable rule | Allowed with conditions: same dedicated donor payable rule |
| Identified donation initiation | Blocked: optional contact data does not itself create an identified donor or authenticated donor payment flow | Blocked: phone alone does not create an identified donor | Blocked: email alone does not create an identified donor | Blocked: contact data alone still does not create an identified donor |
| Identified donation completion | Blocked for the same reason | Blocked for the same reason | Blocked for the same reason | Blocked for the same reason |
| Receipt access | Allowed only through a transaction-specific opaque status/receipt reference shown at completion; no recovery path exists if the reference is lost and no contact was supplied | Allowed only through a transaction-specific opaque status/receipt reference; later SMS delivery may be offered, but phone is still unverified | Allowed only through a transaction-specific opaque status/receipt reference; later email delivery may be offered, but email is still unverified | Allowed only through a transaction-specific opaque status/receipt reference; later delivery may use either contact channel, both still unverified until explicitly verified |
| Donation history | Blocked: no account-wide history or donor portal history | Blocked | Blocked | Blocked |
| Profile edit | Blocked: there is no account or donor portal profile | Blocked | Blocked | Blocked |
| Payment status page access | Allowed through a secure opaque reference tied to that payment attempt only | Allowed through a secure opaque reference tied to that payment attempt only | Allowed through a secure opaque reference tied to that payment attempt only | Allowed through a secure opaque reference tied to that payment attempt only |
| Recurring donation | Blocked in the initial safe rollout | Blocked in the initial safe rollout | Blocked in the initial safe rollout | Blocked in the initial safe rollout |
| Saved payment methods | Blocked in the initial safe rollout | Blocked in the initial safe rollout | Blocked in the initial safe rollout | Blocked in the initial safe rollout |

## Terminology Rules

- `guest donor`
  - no authenticated account is required
  - no donor portal access is implied
  - may donate with no identity data, phone only, email only, or both
  - any supplied contact data is operational and unverified by default
- `identified donor`
  - a donor identity linked to a registered account or another explicitly known internal donor record
  - may log in, manage a base profile, and later access donor history/receipts only if donor portal eligibility exists
  - may donate without completed verification under the frozen donor rules
- `anonymous-display donor`
  - an internally traceable donation that is intentionally hidden in public display contexts
  - this is a visibility/reporting preference, not a separate authorization class
  - it can apply to either guest or identified donations as long as internal traceability is preserved
- `hidden donor`
  - do not treat this as a separate permission bucket
  - use it only as a legacy synonym or UI alias for `anonymous-display donor`
  - the canonical term for permissions and reporting rules should remain `anonymous-display donor`

## Initial Safe Rollout Scope

- Keep donor registration and donor login open without universal verification requirements.
- Keep donor payment ability separate from donor portal eligibility.
- Support direct amount-based donor donation for:
  - guest donors
  - identified logged-in donors
- Require a dedicated donor payable and reconciliation model before enabling donor donation completion in code.
- Allow narrow transaction-specific payment status and receipt access without requiring donor portal eligibility.
- Keep donor portal history and receipt lists behind separate donor portal eligibility.
- Keep donor portal read-only in the initial rollout.
- Defer recurring donation and saved payment methods.
- Preserve guest-donation optional identity capture without auto-creating portal eligibility or auto-linking to privileged records.

## Explicitly Blocked Actions And Why

- Using legacy `transactions` rows as the canonical live donor payment finalization model is blocked because prompt-03 froze that as unsafe without a dedicated donor payable design.
- Treating donor portal eligibility as a prerequisite for making a donation is blocked because donor payment ability and portal access are separate business rules.
- Treating verification as a universal donor donation or donor login gate is blocked because prompt-03 and prompt-07 froze donor login and donation without universal verification.
- Auto-converting guest contact data into an identified donor, linked portal account, or donor portal history is blocked because guest identity capture remains optional, duplicate-sensitive, and non-privilege-escalating.
- Recurring donation is blocked in the initial rollout because it requires repeat-charge authority, failure recovery, opt-out controls, and accounting/reconciliation rules not yet designed.
- Saved payment methods are blocked in the initial rollout because they require tokenized provider support and a stronger donor-payment risk model than the repo currently has.
