# Risks

- If prompt-29 repurposes, drops, or semantically rewrites `email_verified_at` too early, the current approval-gated auth flow can break before the replacement read path is ready.
- If guardian linkage remains inferred from raw pivot presence or invoice ownership, prompt-36 and prompt-37 will blur informational and protected guardian boundaries again.
- If donor checkout is implemented against legacy `transactions` instead of new donor-domain tables, prompt-10 and prompt-12's safe-settlement guardrails will be broken.
- If new account-state or linkage columns are made non-null or uniquely constrained before prompt-24 backfill classifies legacy rows, rollout will misclassify existing users and profiles.
- If optional Google or later claim-link schema is mixed into the mandatory first migration wave, rollback surface area will widen and the approved implementation order will drift.
