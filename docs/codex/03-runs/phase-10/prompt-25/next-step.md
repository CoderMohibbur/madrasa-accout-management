# Next Step

Run `docs/codex/01-prompts/prompt-26-rollout-and-risk-plan.md` next.

Carry forward these prompt-25 decisions:

- keep the test strategy phase-aware so rollout planning distinguishes transition-safety checks from final-state acceptance checks
- treat migration and backfill safety tests as rollout blockers, especially classification-first output, no-guess linkage handling, and idempotent reruns
- keep donor payment ability, donor portal eligibility, guardian informational eligibility, and guardian protected ownership as separate rollout gates
- keep Google and multi-role rollout fail-closed on ambiguous identity or linkage and on raw role-order redirects
- require the minimum phase packs to accumulate rather than reset as later rollout phases begin
