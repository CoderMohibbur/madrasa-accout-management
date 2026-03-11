# Prompt 30 Account-State Read-Path Adaptation

Approved baseline decisions from prompt-30:

- Prompt-30 is limited to shared account-state read logic plus the smallest existing login/role/management/dashboard/portal-entry checks that depend on it.
- The shared read order is explicit prompt-29 columns first, then legacy fallback: `approval_status` falls back to `email_verified_at`, `account_status` falls back to `active`, and `deleted_at` overrides access when set.
- Login now reads approval, lifecycle, and deletion separately instead of treating `email_verified_at` as the sole approval gate.
- Existing `verified` middleware remains in place in prompt-30; this prompt does not perform the final route/middleware cutover.
- Dashboard redirect logic must not guess a donor or guardian landing from role membership alone; it requires a matching eligible portal profile before redirecting.
