# Input

Use the docs/codex-autopilot workflow from my project.

Task: Execute the next approved step after prompt-28 completion.

Run:
`docs/codex/01-prompts/prompt-29-account-state-schema-foundation.md`

Important carry-forward constraints from the latest report:
- Phase 12 prompt-28 is complete.
- Prompt-28 completed successfully.
- No blocker is present.
- No correction pass is required.
- Preserve the approved shared UI foundation implementation from prompt-28.
- Reuse the new shared CSS, Blade primitives, public/app/portal shells, and reusable UI component set from prompt-28.
- Respect the scope boundary from prompt-28: routes, controllers, schema, and business-flow logic were intentionally not changed there.
- Treat the PHP 8.4 `mbstring` issue as an environment/runtime quirk already worked around during validation, not as a product blocker.
- Keep this step strictly in account-state schema foundation scope.
- Do not reopen earlier prompts unless a real contradiction is found.

Prompt-29 implementation instructions:
- implement only the approved additive shared-account-state schema slice on `users`
- before coding restate:
  - exact approved schema slice
  - migration safety rules
  - rollback/backfill concerns
  - what must not change yet
- implement only:
  - nullable-first `users.approval_status`
  - nullable-first `users.account_status`
  - nullable-first `users.phone`
  - nullable-first `users.phone_verified_at`
  - one user-level deletion marker on `users`
- do not implement in prompt-29:
  - route behavior changes
  - controller or policy changes
  - auth/read-path cutover
  - guardian linkage-state schema adoption
  - donor settlement tables
  - donor payable redesign

Scope adaptation from approved durable artifacts:
- `docs/codex/05-artifacts/schemas/prompt-23-schema-change-analysis.md` maps prompt-29 specifically to the additive `users` account-state schema foundation.
- Guardian linkage-state schema remains deferred to the guardian rollout prompts.
- `donation_intents` and `donation_records` remain deferred to prompt-34.

Required process:
1. Read the required control/orchestrator docs for the codex-autopilot workflow.
2. Execute prompt-29 exactly within its allowed scope.
3. Save outputs in the correct run folder for prompt-29.
4. Promote durable decisions/artifacts if the workflow requires it.
5. Perform a contradiction / blocker pass before finalizing.
