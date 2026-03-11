# Prompt 24 Data Backfill Migration

## Backfill Targets

1. `users`
   - `approval_status`
   - `account_status`
   - user-level deletion marker
   - `phone`
   - `phone_verified_at`
2. role/profile consistency
   - donor profile without donor role
   - donor role without donor profile
   - guardian profile without guardian role
   - guardian role without guardian profile
3. guardians
   - profile-level linkage state
   - `guardian_student` object-level linkage state
   - invoice ownership mismatch buckets
4. donors
   - linked portal candidate
   - linked no-portal candidate
   - unlinked management-only donor row
5. donor-domain tables
   - start empty
   - no legacy donor transaction conversion in the first rollout

## Safe Migration Order

1. add new nullable columns and tables
2. generate classification reports and ambiguity buckets
3. backfill `users.approval_status`
4. backfill `users.account_status`
5. backfill `users.phone` only for unambiguous single-source matches
6. backfill guardian linkage states
7. classify role/profile mismatches
8. leave donor-domain tables empty and untouched by legacy transactions
9. switch reads only in later prompts after validation

## Rollback Strategy

- separate schema changes, data backfill, and read-path switches
- write only new columns in the first backfill pass
- keep old columns, pivot rows, and invoice ownership untouched
- make backfill rerunnable and idempotent
- treat ambiguous rows as manual review rather than auto-fix

## Non-Deferable Work

- initial `users.approval_status` backfill
- initial `users.account_status` backfill
- explicit policy for user-level deletion default
- unambiguous `users.phone` policy
- guardian linkage-state backfill
- role/profile mismatch reporting
- explicit decision that new donor tables start empty

## Deferable Work

- legacy donor transaction bridge into new donor history
- auto-linking donor/guardian profiles by contact similarity
- ambiguous phone backfill
- Google external identities
- multi-role context preferences
- claim-audit tables
- destructive cleanup of legacy fields
