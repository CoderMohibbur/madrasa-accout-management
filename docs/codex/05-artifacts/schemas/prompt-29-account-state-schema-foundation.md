# Prompt 29 Account-State Schema Foundation

## Implemented Schema Slice

- new migration: `database/migrations/2026_03_09_010000_add_account_state_columns_to_users_table.php`
- added nullable-first `users.approval_status`
- added nullable-first `users.account_status`
- added nullable-first `users.phone`
- added nullable-first `users.phone_verified_at`
- added nullable-first `users.deleted_at`
- added non-unique lookup indexes for `approval_status`, `account_status`, and `phone`

## Preservation Rules Followed

- `email_verified_at` remained unchanged
- no historical migrations were edited
- no backfill ran in this prompt
- no auth or route read-path behavior changed in this prompt
- guardian linkage-state schema and donor settlement tables remained deferred

## Why This Artifact Matters

- prompt-29 creates the dark-schema landing zone required by prompt-24's classification-first backfill plan
- prompt-30 can now adapt account-state reads without reopening migration structure
- later prompts can use `deleted_at` as the account-level deletion axis without reusing donor/guardian profile flags as account-wide state

## Validation Snapshot

- targeted schema tests passed: `tests/Feature/Phase1/FoundationSchemaTest.php` and `tests/Feature/Phase12/AccountStateSchemaFoundationTest.php`
- testing migration status confirmed the new prompt-29 migration ran
- full-suite failures remained limited to the documented auth/profile baseline set
