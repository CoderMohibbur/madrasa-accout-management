# Decisions

- Prompt-29 was executed as the additive `users` account-state schema foundation only, following the durable prompt-23 schema artifact mapping rather than widening into guardian linkage or donor settlement schema.
- The new `users` schema fields are `approval_status`, `account_status`, `phone`, `phone_verified_at`, and `deleted_at`, all added nullable-first with no first-pass uniqueness or default-driven interpretation.
- `deleted_at` was chosen as the approved account-level deletion marker, but model/query enforcement was intentionally deferred so prompt-29 stays schema-only.
- `email_verified_at` was preserved unchanged and remains the current email-verification field until later read-path/auth prompts explicitly separate legacy approval behavior.
- Guardian linkage-state schema adoption remains deferred to the later guardian prompts, and donor-domain `donation_intents` plus `donation_records` remain deferred to prompt-34.
- Validation used the local Laragon PHP 8.2 runtime because the local 8.4 runtime lacked `mbstring`; the resulting full-suite failure list still matched the pre-existing auth/profile baseline manifest exactly.
