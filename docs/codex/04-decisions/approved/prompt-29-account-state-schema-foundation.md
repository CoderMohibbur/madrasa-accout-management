# Prompt 29 Account-State Schema Foundation

Approved baseline decisions from prompt-29:

- Prompt-29 is limited to the additive shared account-state schema slice on `users`; it does not adopt guardian linkage-state schema or donor settlement tables.
- The implemented `users` columns are `approval_status`, `account_status`, `phone`, `phone_verified_at`, and `deleted_at`, all added nullable-first so prompt-24 backfill can classify before mutating.
- `deleted_at` is the approved account-level deletion marker, but prompt-29 leaves model/query enforcement deferred to later read-path work.
- `email_verified_at` remains intact and unchanged in prompt-29; the overloaded approval behavior is not cut over in this schema-only step.
- No first-pass phone uniqueness, default-driven status interpretation, route changes, controller changes, or auth behavior changes are allowed in this prompt.
