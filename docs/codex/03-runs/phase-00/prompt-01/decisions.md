# Decisions

- Treat the `docs/codex` prompt pack as a retrospective analysis/documentation run over the current repository state, not as a restart of the already-completed autopilot implementation program.
- When autopilot commit metadata lags the live branch, use the live repository contents together with the latest autopilot handoff/report artifacts as the authoritative current state for later prompt analysis.
- Preserve the existing single `users` plus `web` auth model, frozen route names, historical migrations, and protected financial/reporting semantics as default constraints for all later prompts unless a prompt explicitly narrows that surface for analysis.
- Stop later prompt execution if continuation would require real secrets, broad protected-path refactors, route-name rewrites, or unsafe payment/finalization assumptions.
