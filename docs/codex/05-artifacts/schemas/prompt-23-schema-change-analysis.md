# Prompt 23 Schema Change Analysis

## Mandatory Schema Changes

1. Shared account-state fields on `users`
   - `approval_status`
   - `account_status`
   - `phone`
   - `phone_verified_at`
   - user-level deletion marker
   - keep `email_verified_at` as email-only
2. Explicit guardian linkage-state fields
   - profile-level linkage state on `guardians`
   - object-level linkage state on `guardian_student`
   - linkage timestamps as needed
3. Donor-domain settlement tables
   - `donation_intents`
   - `donation_records`
   - supporting indexes and unique public-reference / guest-proof constraints

## Optional Schema Changes

- `external_identities` for later Google sign-in
- context-preference persistence for later multi-role polish
- `donation_claims` or equivalent later claim-audit persistence
- approval governance and later profile-flag normalization fields

## Prompt Mapping

- prompt-24: backfill and legacy-data interpretation for the new account-state and guardian-linkage fields
- prompt-29: additive `users` account-state schema foundation
- prompt-34: donor-domain schema foundation for `donation_intents` and `donation_records`
- prompt-36 and prompt-37: adoption of explicit guardian linkage state
- prompt-38: optional external identity linkage
- prompt-39: no mandatory new schema; context preferences remain optional

## Migration Safety Notes

- add new schema first; do not edit historical migrations
- do not drop or repurpose `email_verified_at` in the first schema pass
- keep donor and guardian profile flags for compatibility until backfill and read-path work are complete
- keep donor settlement isolated from legacy `transactions`
- reuse existing `payments` plus `receipts`
- store guest retrieval proof as opaque hash material, not raw reusable secrets
- avoid first-pass phone uniqueness or legacy-data-dependent non-null constraints

## Default / Nullability Strategy

- `users` account-state fields: nullable-first for existing rows, then backfill in prompt-24
- `guardians` and `guardian_student` linkage-state fields: nullable-first for existing rows, explicit on new writes
- `donation_intents.user_id` and `donation_intents.donor_id`: nullable
- `donation_intents.public_reference`: required and unique
- `donation_intents.guest_access_token_hash`: required for guest flows, nullable for identified-only flows if needed
- `donation_records.user_id` and `donation_records.donor_id`: nullable
- `donation_records.posting_status`: required with a safe default
