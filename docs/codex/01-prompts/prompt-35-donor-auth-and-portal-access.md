Use the docs/codex-autopilot workflow from my project.

This task is PHASE_12_DONOR_AUTH_AND_PORTAL_ACCESS_IMPLEMENTATION.
Implement only the approved donor auth and portal-access slice.

Before coding:
1) restate the exact approved slice
2) restate donor permission matrix rules
3) restate what guest-donation behavior must remain intact

Implement only:
- donor registration/login access behavior as approved
- donor portal access gating as approved
- no guardian changes
- no Google sign-in yet unless explicitly approved for this slice
- no unrelated payment refactor

End with:
- files changed
- donor auth behavior implemented
- donor portal gating implemented
- guest-donation compatibility notes
- next safe slice
