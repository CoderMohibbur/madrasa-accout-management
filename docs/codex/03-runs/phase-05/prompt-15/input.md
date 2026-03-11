# Input

Use the docs/codex-autopilot workflow from my project.

This task is PHASE_5_GUARDIAN_IMPLEMENTATION_SLICE_PLANNING_ONLY.
Do not implement code yet.

Read and follow these first:
1. `docs/codex/00-control/master-orchestrator.md`
2. `docs/codex/00-control/phase-order.md`
3. `docs/codex/00-control/reporting-structure.md`
4. `docs/codex/00-control/dummy-secrets-and-replacements.md`
5. `docs/codex-autopilot/docs/MASTER_ORCHESTRATOR_PROMPT.md`

Before starting, review the saved outputs from:
- `docs/codex/03-runs/phase-00/prompt-01/`
- `docs/codex/03-runs/phase-00/prompt-02/`
- `docs/codex/03-runs/phase-01/prompt-03/`
- `docs/codex/03-runs/phase-02/prompt-04/`
- `docs/codex/03-runs/phase-02/prompt-05/`
- `docs/codex/03-runs/phase-03/prompt-06/`
- `docs/codex/03-runs/phase-03/prompt-07/`
- `docs/codex/03-runs/phase-03/prompt-08/`
- `docs/codex/03-runs/phase-05/prompt-13/`
- `docs/codex/03-runs/phase-05/prompt-14/`
- relevant approved decisions in `docs/codex/04-decisions/approved/`
- relevant promoted artifacts in `docs/codex/05-artifacts/`

Carry forward these confirmed findings:
- Phase 05 prompt-14 is complete.
- Prompt-14 completed successfully.
- No blocker is present.
- No correction pass is required.
- Preserve the approved admission-information boundary from prompt-14.
- Keep guardian informational access distinct from protected student/invoice/payment-sensitive access.
- Do not turn this system into a full internal admission application system.
- Preserve the external admission application handoff/link model.
- Do not reopen donor planning or prompt-13 guardian permission decisions unless a real contradiction is found.
- Keep one shared authenticated `users` account model.
- Guardian self-registration may create only an unlinked informational-state guardian record.
- Guardian login and guardian informational access must not depend on universal email or phone verification.
- Protected guardian data access remains linkage- and authorization-controlled.
- Current live `/guardian` routes remain narrower than the approved target because they are protected-only today.

Issue-handling rules:
- stay strictly inside guardian implementation slice planning
- do not implement code
- do not widen scope into multi-role analysis, route-policy finalization, or external admission URL configuration implementation
- if a real contradiction is found, correct the minimum necessary prompt-15 docs before continuing

Run `docs/codex/01-prompts/prompt-15-guardian-implementation-slices.md` with the following approved adaptation:

Using approved guardian analysis outputs, do only this:
1. break guardian work into the smallest safe implementation slices
2. clearly separate:
   - informational portal slices
   - linkage-sensitive protected portal slices
   - guardian auth or onboarding slices
3. identify the safest early-delivery guardian slices
4. identify the highest-risk guardian slices
5. identify rollback-safe checkpoints
6. identify dependencies on earlier shared account/verification foundations and later external-admission configuration where relevant
7. keep current protected guardian routes distinct from the future informational portal slices so the current narrower repo behavior is not accidentally treated as the final target

Do not implement code.

End with:
- guardian slice order
- informational slices
- protected slices
- high-risk slices
- rollback checkpoints
