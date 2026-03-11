# Prompt 24 Data Backfill Migration

Approved baseline decisions from prompt-24:

- Backfill must be classification-first: add new fields dark, produce ambiguity buckets, and only then populate new columns without mutating legacy source fields.
- `email_verified_at` should seed legacy approval compatibility only; it remains untouched during the first backfill pass and must not be treated as universal strong email-proof.
- Donor and guardian profile flags remain domain-local during backfill and must not be copied directly into account-wide deletion or suspension state.
- Guardian linkage backfill must compare guardian profile state, `guardian_student`, and invoice ownership together, with conflicts routed to review buckets.
- New donor-domain tables should begin empty for the first rollout; legacy donor `transactions` are not a mandatory pre-rollout backfill source.
- Role/profile mismatches should be surfaced as review buckets or only resolved when unambiguous; role presence alone must not auto-create profiles.
