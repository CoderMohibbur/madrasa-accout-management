MADRASA ACCOUNT MANAGEMENT — CODEX AUTOPILOT KIT v4 (PROJECT-HARDENED)

Drop this folder tree into the project exactly as-is so the files land under:

  docs/implementation-analysis-report.md
  docs/codex-autopilot/

This package has already been hardened against the actual uploaded Laravel codebase.

What is already baked in:
- single `users` + single `web` guard reality
- existing admin approval login nuance (`email_verified_at` is not ordinary self-serve verification here)
- route-name freeze for existing management UI
- protected-path controls for accounting, reports, auth, shared navigation, and legacy-sensitive models
- baseline failing auth/profile tests captured as pre-existing risk
- explicit phase gate that blocks portal/payment work until canonical posting safety is in place
- phase-end commit checkpoint and commit-SHA handoff requirements
- fresh-thread continuation requirements

What this kit does NOT assume:
- it does not assume current Git branch or HEAD SHA
- it does not assume clean working tree until runtime preflight confirms it
- it does not assume any unsafe broad refactor is allowed

First runtime action:
1. Read `docs/codex-autopilot/docs/MASTER_ORCHESTRATOR_PROMPT.md`
2. Run preflight only
3. If preflight dirties only `docs/codex-autopilot/state/*`, `handoff/*`, `reports/*`, `docs/*`, or `templates/*`, normalize and checkpoint those autopilot-only changes, then rerun preflight
4. Create/switch to the safe branch required in `state/run_state.json`
5. Do not start coding until a final preflight pass is recorded

Status of this package:
- documentation/state package: ready
- live implementation: not started
- current approved next work: `PHASE_1_FOUNDATION`
