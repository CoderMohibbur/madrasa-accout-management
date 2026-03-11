# Risks

- The new `users` account-state columns are dark only until prompt-30 and the later backfill prompts make them authoritative, so any premature manual writes could create temporary drift from current runtime gating.
- `deleted_at` now exists on `users`, but user soft-delete behavior is not yet enforced in queries or auth; later prompts must treat that as deferred adoption rather than already-live behavior.
- Guardian linkage-state schema and donor settlement tables remain intentionally deferred, so prompt-29 does not by itself remove later blockers for guardian protected access or donor checkout settlement.
- Local validation still depends on the Laragon PHP 8.2 runtime because the local PHP 8.4 runtime in this environment does not currently load `mbstring`.
