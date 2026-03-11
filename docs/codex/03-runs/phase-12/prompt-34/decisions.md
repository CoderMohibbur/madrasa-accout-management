# Decisions

- Prompt-34 implements donor slices `P1` -> `P2` -> `G2` -> `A1` only; donor auth/account separation, donor portal adaptation, and donor history/receipt bridge remain deferred to prompt-35.
- `donation_intent` is the live donor-side payable for `payments`, and `donation_record` is created only after authoritative settlement.
- Prompt-34 reuses the prompt-33 `/donate` session draft handoff instead of inventing a second donor entry surface.
- Public guest checkout keeps `user_id` null, creates no `users` row, creates no donor profile, and exposes only transaction-specific status/receipt access through `public_reference` plus access key.
- Authenticated checkout becomes an identified donation by populating `donation_intent.user_id` plus `payments.user_id`; it does not require donor role or donor portal eligibility.
- Link `donor_id` only when an existing donor profile already exists; do not auto-create or auto-link donor profiles in prompt-34.
- Verified donor settlement creates exactly one `donation_record` and one receipt, records posting as `skipped`, and does not create a legacy `transactions` row.
- Mismatch, ambiguity, or post-cancel success routes the donor payment to manual review and creates no `donation_record` or receipt.
- Retry reuses the same open `donation_intent` when the session-draft checkout restarts after a failed attempt.
- The existing shurjoPay IPN entry point now dispatches donor payables by `payable_type` without changing guardian invoice flow behavior.
