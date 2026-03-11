# Prompt 25 Test Matrix

Approved baseline decisions from prompt-25:

- The prompt-25 test strategy is phase-aware and must cover both transition-safety regression checks and final approved end-state acceptance behavior.
- Migration and backfill safety tests are rollout blockers, especially classification-first reporting, column-only writes, explicit review buckets, and idempotent reruns.
- Donor tests must keep payment ability, narrow receipt access, and full donor portal eligibility as separate boundaries, while keeping legacy donor `transactions` out of new settled history.
- Guardian tests must separate informational eligibility from protected ownership, and protected access must require both profile eligibility and object-level authorization.
- Google sign-in and multi-role tests must fail closed on ambiguous identity or linkage rows and must prove eligibility-based switching instead of raw role-order routing.
- Minimum rollout packs should accumulate across five implementation clusters: shared identity foundation, donor rollout, guardian rollout, Google plus multi-role finalization, and final readiness.
