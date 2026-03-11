Use the docs/codex-autopilot workflow from my project.

This task is PHASE_9_DATA_BACKFILL_AND_MIGRATION_ANALYSIS_ONLY.
Do not implement code yet.

Using approved schema and state-model outputs, do only this:
1) identify which existing records require interpretation or backfill
2) identify risks around overloaded email_verified_at semantics
3) identify risks around donor/guardian portal flags and profile state
4) define the safest backfill order
5) define rollback-safe migration strategy
6) define what must be migrated before rollout vs what can be deferred

Do not implement code.

End with:
- backfill targets
- interpretation risks
- safe migration order
- rollback-safe migration strategy
- deferable vs non-deferable migration work
