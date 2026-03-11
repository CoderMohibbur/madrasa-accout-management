# Input

Use the docs/codex-autopilot workflow from my project.

This task is PHASE_12_GUEST_DONATION_ENTRY_IMPLEMENTATION.
Implement only the approved guest donation entry slice.

Carry forward constraints:
- phase-12 prompt-32 is complete
- prompt-32 completed successfully
- no blocker is present
- no correction pass is required
- preserve the approved email/phone verification foundation from prompt-32
- reuse the separate email and phone verification handling already introduced
- keep verification scope narrowly on the approved foundation; do not reopen broader auth, portal, or routing decisions unless a real contradiction is found
- respect the prompt-31 open-registration foundation and prompt-32 verification coexistence behavior
- keep this step strictly in guest donation entry scope

Before coding:
1. restate the exact approved slice
2. restate allowed touch paths
3. restate identity-capture rules
4. restate what must not be changed

Implement only:
- guest donation entry route/controller/view behavior
- direct amount-based donation start
- optional name/phone/email capture as approved
- no mandatory registration
- no donor portal redesign
- no guardian changes
- no unsafe payment finalization shortcuts

End with:
- files changed
- guest donation entry behavior implemented
- intentionally deferred items
- risk notes
- next safe slice
