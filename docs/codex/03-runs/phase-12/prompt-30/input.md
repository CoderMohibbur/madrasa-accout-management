# Input

Use the docs/codex-autopilot workflow from my project.

Task: Execute the next approved step after prompt-29 completion.

Run:
`docs/codex/01-prompts/prompt-30-account-state-read-path-adaptation.md`

Important carry-forward constraints from the latest report:
- Phase 12 prompt-29 is complete.
- Prompt-29 completed successfully.
- No blocker is present.
- No correction pass is required.
- Preserve the approved account-state schema foundation from prompt-29.
- Reuse the additive, nullable-first `users` account-state columns introduced in prompt-29.
- Do not pull guardian linkage adoption, donor settlement tables, or payment-domain schema forward in this step.
- Keep this step strictly in account-state read-path adaptation scope.
- Respect the prompt-28 shared UI foundation and prompt-29 schema-only boundary.
- Do not reopen earlier prompts unless a real contradiction is found.

Prompt-30 implementation instructions:
- implement only the approved account-state read-path adaptation slice
- before coding restate:
  - exact approved slice
  - which account-state distinctions must now be respected
  - what must remain backward-compatible
- implement only:
  - minimum shared read helpers needed to interpret `approval_status`, `account_status`, `deleted_at`, and the prompt-29 nullable-first fallback posture
  - minimum login/read/check adaptations needed to stop using raw `email_verified_at` as the sole approval gate
  - minimum dashboard / role / management / portal-entry read checks needed to fail closed on pending, inactive, suspended, or deleted account state
- do not implement in prompt-30:
  - broad route redesign
  - guardian linkage-state adoption
  - donor settlement tables
  - donor or guardian no-portal rollout
  - Google sign-in
  - prompt-31 registration changes
  - prompt-32 verification flow redesign

Scope adaptation from approved durable artifacts:
- keep `verified` middleware in place where it already exists; prompt-30 may not do the final blanket-`verified` removal because prompt-18/prompt-25/prompt-27 defer that until explicit eligibility middleware is ready
- use prompt-29 `users` account-state columns with legacy-compatible fallback reads until prompt-24 style backfill and later registration/verification prompts land
- fail closed on ambiguous role-only guardian/donor dashboard redirects instead of guessing a portal landing

Required process:
1. Read the required control/orchestrator docs for the codex-autopilot workflow.
2. Execute prompt-30 exactly within its allowed scope.
3. Save outputs in the correct run folder for prompt-30.
4. Promote durable decisions/artifacts if the workflow requires it.
5. Perform a contradiction / blocker pass before finalizing.
