# Input

Base prompt file:

`docs/codex/01-prompts/prompt-32-email-phone-verification-foundation.md`

Prompt text used for this run:

Use the docs/codex-autopilot workflow from my project.

This task is PHASE_12_EMAIL_PHONE_VERIFICATION_FOUNDATION_IMPLEMENTATION.
Implement only the approved email-and-phone verification foundation slice.

Before coding:
1) restate the exact approved slice
2) restate phone/email identity rules
3) restate anti-abuse requirements
4) restate what must remain deferred

Implement only:
- minimum verification foundation behavior
- approved phone/email coexistence support
- throttling/resend/cooldown behavior as approved
- no donor payable redesign
- no guest donation finalization changes beyond this exact slice
- no Google sign-in yet unless explicitly included in the approved slice

End with:
- files changed
- verification foundation implemented
- anti-abuse notes
- deferred items
- next safe slice

Approved carry-forward constraints applied before implementation:

- Phase 12 prompt-31 is complete.
- Prompt-31 completed successfully.
- No blocker is present.
- No correction pass is required.
- Preserve the approved open-registration foundation from prompt-31.
- Reuse the unified registration backend/request flow and the neutral authenticated onboarding handoff added in prompt-31.
- Keep donor and guardian registration intent as non-portal / inactive-unlinked intent only unless prompt-32 explicitly expands that behavior.
- Preserve the current boundary that legacy verified routes still keep their existing verification gate.
- Do not pull broader donor/guardian portal rollout or legacy routing cleanup forward in this step.
- Keep this step strictly in email/phone verification foundation scope.
- Do not reopen earlier prompts unless a real contradiction is found.

Scope restatement used before coding:

- exact approved slice:
  - add the minimum email/phone verification foundation on top of prompt-31's shared registration flow
  - support optional account-level phone coexistence with the existing email-verification stack
  - keep verification separate from approval, role assignment, portal eligibility, guardian linkage, and donor activation
- phone/email identity rules:
  - email remains the canonical login identifier in this rollout
  - phone remains optional and verification-first
  - changing email resets only email verification
  - changing phone resets only phone verification
  - verified phone conflicts must fail closed rather than silently reassigning ownership
- anti-abuse requirements:
  - email resend cooldown 60 seconds, hard cap 6 sends per hour per account/email
  - phone resend cooldown 60 seconds, hard cap 5 sends per hour per normalized phone and IP
  - phone code expiry 10 minutes
  - repeated invalid phone-code attempts trigger temporary cooldown
  - verification requests, success events, channel changes, conflicts, and lockouts must be auditable
- deferred items that remained out of scope:
  - donor payable redesign
  - guest donation finalization changes
  - donor and guardian portal-role rollout
  - legacy verified-route boundary removal
  - Google sign-in
