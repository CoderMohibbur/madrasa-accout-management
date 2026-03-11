# Risks

- `docs/codex-autopilot/state/run_state.json` is one commit behind the live safe branch head (`12c96a0...` recorded vs `a3f048c...` in `.git/refs/heads/...`), so later prompts could inherit stale commit assumptions if they rely only on machine-readable state.
- Protected auth, route, financial, reporting, and shared-navigation files remain high-risk touch points; widening scope inside later prompts could create accidental contradictions with the autopilot safety rules.
- The broader test suite still has 14 documented auth/profile baseline failures, so future prompt conclusions must keep baseline-versus-regression separation explicit.
- The current shell does not expose the `git` CLI, limiting direct runtime Git hygiene verification during this documentation pass.
