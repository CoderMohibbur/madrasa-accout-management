# Decisions

- Backfill must be classification-first: add new fields dark, report ambiguity buckets, and only then populate new columns without mutating legacy source fields.
- `email_verified_at` should seed legacy approval compatibility only; it must remain untouched during the first backfill pass and must not be reinterpreted as strong modern email-proof for every row.
- Donor and guardian profile flags remain domain-local during backfill; they must not be copied directly into account-wide deletion or suspension state.
- Guardian linkage backfill must compare `guardian_student`, guardian profile lifecycle, and `student_fee_invoices.guardian_id` together, with conflicts routed to review buckets.
- New donor-domain tables should start empty for the first rollout; legacy donor `transactions` are not a mandatory pre-rollout backfill source.
