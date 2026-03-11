# Report

## Scope Lock Before Coding

### Exact Approved Slice

- prompt-29 was executed as the additive shared account-state schema foundation on `users` only
- approved prompt-29 columns:
  - `approval_status`
  - `account_status`
  - `phone`
  - `phone_verified_at`
  - one user-level deletion marker
- `email_verified_at` had to remain intact as the existing email-verification field

### Migration Safety Rules Reused

- additive migration only
- nullable-first for all new account-state columns on existing rows
- no historical migration edits
- no first-pass phone uniqueness
- no backfill or legacy-field mutation in this prompt
- no auth, routing, or portal read-path cutover in this prompt

### Rollback / Backfill Concerns Reused

- prompt-29 is the dark-schema checkpoint from the rollout plan: schema may land before any authoritative read switch
- prompt-24 remains the source of truth for classification-first backfill
- existing rows must keep `approval_status`, `account_status`, `phone`, `phone_verified_at`, and `deleted_at` nullable until conservative classification/backfill runs later
- `email_verified_at`, donor/guardian profile flags, guardian pivots, invoice ownership, and donor financial history must remain untouched here

### What Explicitly Did Not Change

- no routes
- no controllers
- no middleware or policies
- no `LoginRequest`, `RegisteredUserController`, or verification behavior
- no guardian linkage-state schema adoption
- no donor `donation_intents` or `donation_records` tables
- no payment, donor, guardian, or management business-flow changes

## Implementation Result

Prompt-29 completed inside the approved users-only schema scope.

### Schema Changes Implemented

- Added `database/migrations/2026_03_09_010000_add_account_state_columns_to_users_table.php`
- Added nullable-first `users.approval_status`
- Added nullable-first `users.account_status`
- Added nullable-first `users.phone`
- Added nullable-first `users.phone_verified_at`
- Added nullable-first `users.deleted_at` as the approved account-level deletion marker
- Added non-unique lookup indexes for `approval_status`, `account_status`, and `phone`

### Validation Coverage Added

- Added `tests/Feature/Phase12/AccountStateSchemaFoundationTest.php`
- Confirmed the new `users` account-state columns exist
- Confirmed legacy-compatible user creation still leaves the new columns unset by default

### Files Changed

- `database/migrations/2026_03_09_010000_add_account_state_columns_to_users_table.php`
- `tests/Feature/Phase12/AccountStateSchemaFoundationTest.php`

## Backward-Compatibility Notes

- `email_verified_at` was preserved without renaming, dropping, or semantic rewiring
- the new `users` columns are dark only; current auth and route gating still read legacy behavior until prompt-30 and later prompts explicitly adapt them
- `deleted_at` was added as schema only; user model/query behavior was intentionally not changed in this prompt
- guardian profile flags, donor profile flags, guardian pivots, invoice ownership, payment tables, and legacy transactions were not touched

## Backfill Impact Notes

- prompt-29 created the nullable-first landing zone required by prompt-24's classification-first backfill plan
- no backfill ran in this prompt
- later backfill must still:
  - seed `approval_status` from current login semantics
  - seed `account_status` conservatively
  - leave `phone_verified_at` null absent authoritative proof
  - keep `deleted_at` null unless true account-level deletion evidence exists

## Durable Artifact Promotion

- promoted approved decisions to `docs/codex/04-decisions/approved/prompt-29-account-state-schema-foundation.md`
- promoted the implemented account-state schema summary to `docs/codex/05-artifacts/schemas/prompt-29-account-state-schema-foundation.md`

## Validation

- `D:\laragon\bin\php\php-8.2.9-Win32-vs16-x64\php.exe artisan test --env=testing tests/Feature/Phase1/FoundationSchemaTest.php tests/Feature/Phase12/AccountStateSchemaFoundationTest.php`
  - result: `pass`
  - summary: `2 passed (20 assertions)`
- `D:\laragon\bin\php\php-8.2.9-Win32-vs16-x64\php.exe artisan migrate:status --env=testing`
  - result: `pass`
  - summary: new migration `2026_03_09_010000_add_account_state_columns_to_users_table` is present and ran in the testing database
- `D:\laragon\bin\php\php-8.2.9-Win32-vs16-x64\php.exe artisan test --env=testing`
  - result: `expected baseline failure set only`
  - summary: `14 failed, 32 passed`
  - classification: failure list still matches the existing auth/profile baseline manifest exactly; the extra passing test count comes from the new phase-12 schema test
  - unexpected regressions: `none`

## Contradiction / Blocker Pass

- No contradiction was found with prompt-23's prompt mapping, prompt-24's classify-first migration posture, prompt-25's Phase A blocker pack, prompt-26's rollback point B, or prompt-27's implementation order.
- The durable schema artifact for prompt-23 narrowed prompt-29 to the `users` account-state schema slice, so guardian linkage-state adoption and donor settlement tables were intentionally left for later prompts.
- No route rename, auth cutover, guardian-protected entitlement change, donor payment-model change, or historical migration edit occurred.
- Validation again required the local PHP 8.2 runtime because the local PHP 8.4 runtime lacks `mbstring`; this remained an environment/runtime quirk, not a product blocker.

## Risks

- Until prompt-30 adapts read paths, the new `users` account-state columns are non-authoritative and can drift from current runtime behavior if someone starts writing them manually.
- `deleted_at` now exists on `users`, but deletion enforcement is intentionally deferred; prompt-30 and later prompts must avoid assuming the column is already part of active query behavior.
- Guardian linkage-state schema and donor settlement schema remain pending later prompts, so prompt-29 alone does not unblock guardian protected routing or donor payment-domain rollout.

## Next Safe Slice

- `docs/codex/01-prompts/prompt-30-account-state-read-path-adaptation.md`
