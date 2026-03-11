# Input

Use the docs/codex-autopilot workflow from my project.

This task is PHASE_12_MULTI_ROLE_HOME_AND_SWITCHING_IMPLEMENTATION.
Implement only the approved multi-role home/switching slice.

Before coding:
1) restate the exact approved slice
2) restate multi-role isolation rules
3) restate what data-scope mixing must never happen

Implement only:
- multi-role home/dashboard behavior
- approved donor/guardian switching affordances
- scope-safe navigation behavior
- no weakening of donor-owned or guardian-owned data boundaries

Carry forward for this run:
- phase-12 prompt-38 is complete, successful, and not blocked
- preserve prompt-38's external identity foundation, Google redirect/callback flow, and authenticated Google link support
- keep first-time guardian Google onboarding deferred
- keep prompt-35 donor access/history behavior unchanged outside chooser-aware landing logic
- keep prompt-36 guardian informational route space and prompt-37 guardian protected gating unchanged outside chooser-aware landing logic
- keep post-auth routing aligned with the existing donor/guardian/account-state services
- do not let Google sign-in, chooser behavior, or switching bypass donor eligibility, guardian linkage, or protected portal gating
- implement only `MR1` through `MR3`; keep `MR4` final route/middleware cleanup deferred to prompt-40
- keep route names unchanged and do not reopen earlier prompts unless a real contradiction is found

End with:
- files changed
- multi-role behavior implemented
- scope-isolation protections preserved
- next safe slice
