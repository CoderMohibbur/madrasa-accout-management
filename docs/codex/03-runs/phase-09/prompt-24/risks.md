# Risks

- If prompt-29 or later backfill makes new account-state columns non-null or authoritative before ambiguity buckets are classified, existing users will be misclassified.
- If donor or guardian contact data is used as automatic ownership proof, the rollout can create wrong `user` links or wrong `users.phone` values.
- If guardian linkage is inferred from only pivot rows or only invoice ownership, protected guardian access can be broadened incorrectly.
- If legacy donor `transactions` are converted into new donor-domain settlement rows too early, donor history can double count or claim settlement facts the old data never stored.
- If role/profile mismatches are ignored while current role-based redirects and middleware remain live, temporary route behavior can stay misleading until later prompts consume the new state model.
