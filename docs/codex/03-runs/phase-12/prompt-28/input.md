# Input

Use the docs/codex-autopilot workflow from my project.

Task: Execute the next approved step after prompt-27 completion.

Run:
`docs/codex/01-prompts/prompt-28-shared-ui-foundation-implementation.md`

Important carry-forward constraints from the latest report:
- Phase 11 prompt-27 is complete.
- Prompt-27 completed successfully.
- No blocker is present.
- No correction pass is required.
- Prompt-27 is the final implementation planning packet; implementation may now begin in the tightly-scoped sense approved by the packet.
- Start at prompt-28 only.
- Keep the approved rollout waves intact.
- Stop on any no-go warning.
- Reuse the consolidated final artifacts promoted by prompt-27, including:
  - `docs/codex/07-final/final-architecture.md`
  - `docs/codex/07-final/final-business-rules.md`
  - `docs/codex/07-final/final-go-live-checklist.md`
  - `docs/codex/07-final/final-rollout-plan.md`
  - `docs/codex/07-final/final-schema-plan.md`
  - `docs/codex/07-final/final-ui-ux-direction.md`
- Do not reopen earlier prompts unless a real contradiction is found.

Prompt-28 implementation instructions:
- implement only the approved shared UI foundation slice
- before coding restate:
  - exact approved slice
  - allowed touch paths
  - what must not be changed
  - approved global UI/UX rules
- implement only:
  - shared layout updates
  - shared typography/spacing/button/form primitives
  - shared alert/feedback/empty/loading/no-access patterns
  - shared card/list/table baseline
  - shared auth/public/portal visual consistency foundation
- do not implement:
  - donor business logic changes
  - guardian business logic changes
  - payment-domain redesign
  - schema changes unless explicitly required by this exact slice

Required process:
1. Read the required control/orchestrator docs for the codex-autopilot workflow.
2. Execute prompt-28 exactly within its allowed scope.
3. Save outputs in the correct run folder for prompt-28.
4. Promote durable decisions/artifacts if the workflow requires it.
5. Perform a contradiction / blocker pass before finalizing.
