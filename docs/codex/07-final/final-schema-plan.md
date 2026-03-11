# Final Schema Plan

## Mandatory Schema Surface

- Shared account-state columns on `users`
- Explicit guardian linkage-state fields
- Donor-domain `donation_intents`
- Donor-domain `donation_records`
- Existing `payments` and `receipts` reused for the minimal donor rollout
- No mandatory multi-role table

## Optional Later Schema

- Dedicated external-identity linkage record
- Guest-claim audit persistence
- Profile-flag normalization

## Migration Posture

- Add schema dark and nullable-first.
- Produce read-only classification reports before any mutating backfill step.
- Backfill conservatively and idempotently.
- Switch read paths only after classification/backfill evidence is accepted.
- Defer destructive cleanup until after implementation validation.

## Backfill Rules

- Use `email_verified_at` only to seed legacy approval compatibility.
- Do not rewrite `email_verified_at`.
- Default account-level lifecycle conservatively.
- Populate `users.phone` only from unambiguous sources.
- Keep ambiguous identity, linkage, and phone rows in review buckets.
- Compare guardian profile state, `guardian_student`, and invoice ownership together.
- Start new donor-domain tables empty for the first rollout.
- Do not auto-create missing profiles from role-only rows.
