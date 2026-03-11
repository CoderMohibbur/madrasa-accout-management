# Decisions

- The prompt-25 test strategy must be phase-aware, covering both transition-safety regression checks and final approved end-state acceptance behavior.
- Migration and backfill safety tests are rollout blockers, especially classification-first reporting, column-only writes, explicit review buckets, and idempotent reruns.
- Donor coverage must keep payment ability, narrow receipt access, and full donor portal eligibility as separate boundaries, while excluding legacy donor `transactions` from new settled history.
- Guardian coverage must separate informational eligibility from protected ownership, and protected access must require both profile eligibility and object-level authorization.
- Google sign-in and multi-role coverage must fail closed on ambiguous identity or linkage rows and must prove eligibility-based switching instead of raw role-order routing.
- Minimum rollout packs should accumulate across five implementation clusters: shared identity foundation, donor rollout, guardian rollout, Google plus multi-role finalization, and final readiness.
