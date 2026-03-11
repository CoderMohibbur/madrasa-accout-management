# Risks

- The final packet still depends on prompt-24 review buckets being honored in implementation; if later prompts silently auto-resolve ambiguous rows, the entire state model becomes unsafe.
- The known auth/profile baseline failures in `docs/codex-autopilot/state/validation_manifest.json` remain a standing validation risk until implementation prompts either preserve or intentionally redesign those behaviors.
- Donor payment rollout remains the most settlement-sensitive public surface, so any attempt to shortcut prompt-34 or mix in legacy donor `transactions` will undermine the final plan immediately.
- Guardian protected rollout remains the most sensitive ownership boundary, and multi-guardian edge cases will stay risky unless object-scoped tests remain mandatory.
- Route/middleware finalization and Google sign-in remain late-risk identity changes; if they are pulled forward, the approved packet loses its additive-first safety posture.
- The placeholder `docs/codex/07-final/final-go-live-checklist.md` now captures current no-go warnings and implementation entry conditions, but later prompts 28 through 43 must keep it aligned as validation evidence evolves.
