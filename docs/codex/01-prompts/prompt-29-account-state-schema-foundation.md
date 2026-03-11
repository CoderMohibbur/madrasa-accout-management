Use the docs/codex-autopilot workflow from my project.

This task is PHASE_12_ACCOUNT_STATE_SCHEMA_FOUNDATION_IMPLEMENTATION.
Implement only the approved additive schema slice for account-state separation.

Before coding:
1) restate the exact approved schema slice
2) restate migration safety rules
3) restate rollback/backfill concerns
4) restate what must not change yet

Implement only:
- minimum approved schema changes required to separate account-state concerns
- no route behavior changes yet
- no UI changes yet
- no donor payable redesign yet

End with:
- files changed
- schema changes implemented
- backward-compatibility notes
- backfill impact notes
- next safe slice
