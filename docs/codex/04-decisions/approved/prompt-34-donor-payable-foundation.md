# Prompt 34 Donor Payable Foundation

Approved implementation decisions from prompt-34:

- Implement only donor slices `P1`, `P2`, `G2`, and `A1` in this run.
- Use `donation_intent` as the donor-side payable for `payments`, and create `donation_record` only after authoritative settlement.
- Reuse the prompt-33 `/donate` session draft handoff for both guest and identified donor checkout.
- Guest checkout keeps `user_id` null, creates no `users` row, creates no donor profile, and exposes only transaction-specific status/receipt access through `public_reference` plus access key.
- Authenticated checkout becomes an identified donation by populating `donation_intent.user_id` plus `payments.user_id`; it does not require donor role or donor portal eligibility.
- Link `donor_id` only when an existing donor profile already exists; do not auto-create or auto-link donor profiles in prompt-34.
- Verified donor settlement creates exactly one `donation_record` and one receipt, records posting as `skipped`, and does not create a legacy `transactions` row.
- Retry reuses the same open `donation_intent` when the session-draft checkout restarts after a failed attempt.
- Ambiguous verification, mismatch, or post-cancel success routes the donor payment to manual review and creates no `donation_record` or receipt.
- Donor auth/account separation, donor portal gating, donor history/receipt bridging, donor manual-bank, and guest claim/account-link remain deferred to later prompts.
