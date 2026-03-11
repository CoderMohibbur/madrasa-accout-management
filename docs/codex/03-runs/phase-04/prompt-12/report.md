# Report

This prompt completes donor implementation slice planning by combining the approved prompt-09 donor permission matrix, prompt-10 donor payable model, and prompt-11 guest donation onboarding rules without reopening prompt-11. No contradiction was found in prompts 01-11 or the promoted donor decisions/artifacts, so prompt-12 proceeds as a fresh completion of the previously empty run folder.

## Planning Guardrails

- Guest donation remains mandatory and must stay possible without prior registration.
- Guest checkout remains payment-only by default: no silent `users` row, no silent donor-profile creation, no silent donor ownership claim, and no donor portal access side effect.
- Identified donation remains account-linked only and does not require donor portal eligibility.
- `anonymous_display` remains a visibility preference on guest or identified donations, not a third identity system.
- Donor settlement, receipt issuance, donor portal eligibility, and legacy accounting posting remain separate concerns.
- Legacy `transactions` remain unsafe as the live donor payment-finalization source of truth; they may only remain legacy read inputs until an explicit later bridge is added.
- Google sign-in remains a later optional delta and is not part of the donor implementation wave defined here.

## Donor Slice Order

1. `G1` - Public donor entry shell
2. `P1` - Donation domain schema foundation
3. `P2` - Donor payment-domain service foundation
4. `G2` - Live guest checkout activation
5. `A1` - Identified account-linked donation entry
6. `A2` - Donor auth/account behavior separation
7. `O1` - Donor portal boundary adaptation
8. `H1` - Receipt/history eligibility bridge
9. `C1` - Optional guest-claim/account-link flow

## Slice-By-Slice Goals

### `G1` - Public donor entry shell

- Area: guest donation
- Primary type: UI-first
- Goal: add the internal public donor landing and route shell for `donate as guest` vs `sign in/register and donate`, with amount, display mode, and optional name/email/phone capture defined exactly as prompt-11 approved.
- Must preserve: no payment finalization, no legacy `transactions` write, no automatic account creation, no donor-profile creation, no donor portal enablement.
- Dependencies: none beyond additive public route/controller/view work.
- Rollback-safe checkpoint: only public routes, controllers, and views exist; no donor payment-domain records or provider calls exist yet.
- Independently shippable: only as an informational or pre-activation shell.

### `P1` - Donation domain schema foundation

- Area: donor payable
- Primary type: schema-first
- Goal: add the dedicated donor-domain records needed by prompt-10, centered on `donation_intent` as the payable and `donation_record` as the settled-donation truth, including opaque guest retrieval material, donor mode, display mode, nullable `user_id`, nullable `donor_id`, contact snapshots, and posting-state separation.
- Must preserve: guardian invoice payment schema, legacy `transactions` semantics, receipt boundaries, and no assumption that donor settlement happens in legacy accounting tables.
- Dependencies: none.
- Rollback-safe checkpoint: schema and model layer exist but no live donor routes use them.
- Independently shippable: yes, as a dark schema release.

### `P2` - Donor payment-domain service foundation

- Area: donor payable
- Primary type: service-first
- Goal: extend the payment workflow so `PaymentWorkflowService` (or an equivalent additive donor payment service layer) can safely initiate, verify, finalize, retry, cancel, and reconcile `DonationIntent` payables without disturbing the existing `StudentFeeInvoice` path.
- Must preserve: invoice payment behavior stays intact; donor settlement still creates receipts without depending on legacy posting; browser success remains informational only.
- Dependencies: `P1`.
- Rollback-safe checkpoint: donor payment-domain services, policies, and tests exist behind no live donor-facing route activation.
- Independently shippable: yes, as a dark service release.

### `G2` - Live guest checkout activation

- Area: guest donation
- Primary type: integration-first
- Goal: wire the public guest form to `donation_intent` plus `payments`, provider initiation, callback/verify handling, and opaque guest status/receipt retrieval so guest donors can complete a one-time donation without registration.
- Must preserve: guest stays guest by default even when contact fields are present; no account, no donor profile, no donor portal access, and no automatic claim of broader donation history.
- Dependencies: `G1`, `P1`, and `P2`.
- Rollback-safe checkpoint: guest end-to-end checkout works, but access remains transaction-specific through opaque references only.
- Independently shippable: yes. This is the first fully usable donor-payment slice.

### `A1` - Identified account-linked donation entry

- Area: donor account/auth
- Primary type: route-first
- Goal: allow an already authenticated account to start the same donor checkout flow as an identified donor, with `payments.user_id` and `donation_intent.user_id` populated, while keeping donor portal eligibility separate from donation capability.
- Must preserve: no donor portal requirement to donate; no silent donor-profile creation when a logged-in user donates; no fallback to guest ownership rules once the account-linked flow is chosen.
- Dependencies: `P2`.
- Rollback-safe checkpoint: authenticated account-linked donor checkout works, but donor portal routes and donor login rules have not been redesigned yet.
- Independently shippable: yes.

### `A2` - Donor auth/account behavior separation

- Area: donor account/auth
- Primary type: service-first
- Goal: after the shared account-state and verification foundations land, separate donor login, donor no-portal account state, and donor payment-entry eligibility from the current blanket `verified` and `email_verified_at` assumptions.
- Must preserve: one shared `users` account model, email-first login in the smallest safe rollout, no Google sign-in dependency, no donor portal access implied by registration, verification, or payment completion.
- Dependencies: prompt-29 account-state schema foundation, prompt-30 read-path adaptation, prompt-31 open registration foundation, and prompt-32 verification foundation. `A1` is helpful but not required.
- Rollback-safe checkpoint: donor users can hold an account-linked, no-portal state that can donate safely without changing portal read paths yet.
- Independently shippable: yes, but only after the shared auth/account foundations exist.

### `O1` - Donor portal boundary adaptation

- Area: donor portal
- Primary type: service-first
- Goal: move donor portal gating from the current raw `auth` + `verified` + `role:donor` coupling toward derived donor portal eligibility, while keeping the portal read-only and account-linked only.
- Must preserve: guest donors never enter the donor portal; identified payment capability stays broader than donor portal access; donor portal access still requires explicit donor-domain eligibility and not merely a donation record.
- Dependencies: `A2`.
- Rollback-safe checkpoint: donor portal gate changes, but the portal still exposes only donor-owned read data and does not alter payment settlement.
- Independently shippable: yes.

### `H1` - Receipt/history eligibility bridge

- Area: receipt/history eligibility
- Primary type: service-first
- Goal: keep transaction-specific receipt/status access narrow for guest and identified checkout while separately extending donor portal history and receipt lists to include new `donation_record` and payment-linked receipt data for portal-eligible donors.
- Must preserve: no account-wide history leak for guests, no portal-wide browsing for non-portal donors, no double-counting between `donation_record` and legacy `transactions`, and no coupling of receipt issuance to legacy posting.
- Dependencies: `P2` and `O1`.
- Rollback-safe checkpoint: portal-eligible donors can see new donor-domain history and receipts, while guest and identified non-portal access remains transaction-specific only.
- Independently shippable: yes.

### `C1` - Optional guest-claim/account-link flow

- Area: later optional identity-claim/account-link behavior
- Primary type: route-first
- Goal: allow a guest donor to explicitly claim one proved settled donation into a new or existing account by using donation-specific proof, then optionally create or attach donor-domain records without auto-enabling the portal.
- Must preserve: no loose email/phone/name matching, no bulk auto-claim in the first release, no silent donor-profile creation during checkout, no automatic portal eligibility, and no Google-sign-in shortcut.
- Dependencies: `G2` and `A2`. `H1` is useful but not required for the first specific-donation claim.
- Rollback-safe checkpoint: only one specifically proved donation can be claimed; broader grouping, bulk claim, or automatic linking remains off.
- Independently shippable: only if explicitly approved as a later donor slice.

## Dependency Notes

- Prompt-09, prompt-10, and prompt-11 must remain preserved together. The implementation order cannot flatten guest donation, donor payable, and donor portal access into one generalized donor identity flow.
- `G1` is intentionally smaller than a live donor checkout. Because prompt-33 comes before prompt-34 in the implementation phase order, prompt-33 should stop at the public donor entry shell and must not invent a legacy `transactions` shortcut just to make the form submit.
- `P1` and `P2` unlock both `G2` and `A1`. Prompt-34 should therefore implement the donor-domain foundation before any end-to-end donor payment activation.
- `A2` and `O1` must wait for the shared account-state and verification work. Doing donor auth/portal changes before prompts 29-32 would simply re-embed the same overloaded `email_verified_at` and `verified` coupling that earlier analysis rejected.
- `H1` should be delayed until the new donor-domain settlement records exist. Otherwise the donor portal would continue to read only legacy `transactions` and would miss the first safe donor payments.
- `C1` is deliberately last and optional. The initial donor wave remains safe without claim/link behavior because guest donation is already allowed without registration.

## Implementation Prompt Mapping

- Prompt-33 should implement `G1` only.
- Prompt-34 should implement `P1` -> `P2` -> `G2` -> `A1`, with a rollback-safe checkpoint after each sub-slice.
- Prompt-35 should implement `A2` -> `O1` -> `H1`, with a rollback-safe checkpoint after each sub-slice.
- `C1` should stay out of prompt-35 unless a later approved decision explicitly adds that scope.

## Rollback Checkpoints

1. After `G1`: public donor entry exists but does not write donor payment-domain data or call a provider.
2. After `P1`: donor-domain schema is present, but no live routes depend on it.
3. After `P2`: donor payment-domain services are present, but no donor-facing route is active yet.
4. After `G2`: guest checkout is live with opaque transaction-specific status/receipt access only.
5. After `A1`: authenticated identified checkout is live without any portal-gating redesign.
6. After `A2`: donor no-portal account behavior is separated from blanket verification assumptions.
7. After `O1`: donor portal eligibility is derived correctly, but the portal is still read-only.
8. After `H1`: portal-eligible donors can see new donor-domain history and receipts without changing guest or identified transaction-specific boundaries.
9. After `C1`: one-by-one proven claim is live, but bulk grouping and automatic linking remain disabled.

## Independently Shippable Slices

- `P1` can ship independently as dark schema groundwork.
- `P2` can ship independently as dark service groundwork.
- `G2` can ship independently as the first public donor-payment release.
- `A1` can ship independently as account-linked identified donation without donor portal changes.
- `A2` can ship independently once prompts 29-32 have already landed.
- `O1` can ship independently as read-only donor portal gating cleanup after `A2`.
- `H1` can ship independently after `P2` and `O1`.
- `C1` is not part of the initial independently required donor wave; it is a later approval-gated option.

## Completion Status

- No contradiction required a prompt-11 correction-pass reopen.
- No prompt-11 or prompt-12 assumption conflict was found in the reviewed saved reports.
- Prompt-12 is complete.
- There is no hard blocker to proceeding to prompt-13.
