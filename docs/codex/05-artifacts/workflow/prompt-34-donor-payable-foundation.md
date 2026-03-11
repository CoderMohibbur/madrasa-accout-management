# Prompt 34 Donor Payable Foundation

## Route Surface

- `GET /donate`
- `POST /donate/start`
- `POST /donate/checkout`
- `GET /donate/payments/{publicReference}`
- `GET /donate/return/success`
- `GET /donate/return/fail`
- `GET /donate/return/cancel`
- existing `POST /payments/shurjopay/ipn` now dispatches donor order ids by payable type

## Domain Chain

- `donation_intent`
  - pre-settlement donor payable
  - carries `donor_mode`, `display_mode`, contact snapshots, `public_reference`, and opaque access proof
- `payments`
  - attempt rows against `DonationIntent`
  - `user_id` nullable for guest, populated for identified checkout
- `donation_record`
  - settled donor-domain truth created only after verified payment
- `receipt`
  - payment-specific post-settlement receipt

## Identity Rules

- `guest`
  - no account creation
  - no donor profile creation
  - status access only through `public_reference` plus access key
- `identified`
  - linked to the authenticated `user_id`
  - donor profile linked only if one already exists safely
  - no donor portal eligibility side effect
- `anonymous_display`
  - visibility preference only
  - not a separate identity class

## Settlement Rules

- browser return routes are informational only
- verified settlement is server-side
- success creates:
  - one paid `payment`
  - one `donation_record`
  - one receipt
- mismatch/ambiguity/cancel-conflict routes to manual review
- posting stays `skipped`
- legacy `transactions` are untouched

## Narrow Access Rules

- guest and identified donors can view only the specific donation status page
- public access uses:
  - `public_reference`
  - opaque access key
- authenticated ownership can also view identified donation status
- donor portal history remains deferred to prompt-35

## Deferred Boundaries

- donor auth/account-state separation
- donor portal eligibility and read paths
- donor receipt-history bridge
- donor manual-bank
- guest claim/account-link
- legacy donor-history conversion
