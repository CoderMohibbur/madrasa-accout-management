Use the docs/codex-autopilot workflow from my project.

This task is PHASE_12_ACCOUNT_STATE_READ_PATH_ADAPTATION_IMPLEMENTATION.
Implement only the approved read-path adaptation slice.

Before coding:
1) restate the exact approved slice
2) restate which account-state distinctions must now be respected
3) restate what must remain backward-compatible

Implement only:
- minimum code-path updates needed so reads/checks use separated account-state logic
- no broad UI redesign
- no donor payable redesign
- no Google sign-in yet

End with:
- files changed
- read-path behavior adapted
- compatibility notes
- next safe slice
