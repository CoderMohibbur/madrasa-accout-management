# Prompt 11 Guest Donation Onboarding

## Core Flow Rules

- Public donors may donate without prior registration
- Guest donation and identified donation must remain distinct entry paths
- `anonymous-display` is a visibility preference inside either path, not a separate identity path
- Guest checkout uses the prompt-10 donor payable model: `donation_intent` -> `payments` -> `donation_record` -> `receipt`

## Identity-Capture Rules

- `amount` is required
- `name`, `email`, and `phone` are optional in guest checkout
- Optional guest contact fields are unverified by default
- Supplying guest contact data does not itself create an account, donor profile, or portal eligibility

## Account-Creation Rules

- Default guest checkout creates no `users` row
- Default guest checkout creates no `donors` row
- Contact snapshots remain payment-side data unless the donor later enters an explicit claim or conversion flow
- Lightweight account creation belongs to explicit post-payment or later claim flow, not automatic guest checkout

## Claim Rules

- Later claim requires donation-specific proof plus authenticated intent or explicit account creation
- Loose name, email, or phone matching alone is not enough for self-service claim
- First safe self-service claim should attach only the specifically proved donation
- Donor portal eligibility remains separate even after successful claim

## Receipt And Safety Rules

- Guest receipt access is transaction-specific and opaque-reference-based
- Receipt delivery does not equal account verification
- Reconciliation acts on donor payment-domain records, not legacy `transactions`
- Public `anonymous-display` must never remove internal traceability
