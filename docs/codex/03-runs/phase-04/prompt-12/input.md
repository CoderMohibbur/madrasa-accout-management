# Input

Use the docs/codex-autopilot workflow from my project.

This task is PHASE_4_DONOR_IMPLEMENTATION_SLICE_PLANNING_ONLY.
Do not implement code yet.

Read and follow these first:
1. `docs/codex/00-control/master-orchestrator.md`
2. `docs/codex/00-control/phase-order.md`
3. `docs/codex/00-control/reporting-structure.md`
4. `docs/codex/00-control/dummy-secrets-and-replacements.md`

Before planning, review the saved outputs from:
- `docs/codex/03-runs/phase-00/prompt-01/`
- `docs/codex/03-runs/phase-00/prompt-02/`
- `docs/codex/03-runs/phase-01/prompt-03/`
- `docs/codex/03-runs/phase-02/prompt-04/`
- `docs/codex/03-runs/phase-02/prompt-05/`
- `docs/codex/03-runs/phase-03/prompt-06/`
- `docs/codex/03-runs/phase-03/prompt-07/`
- `docs/codex/03-runs/phase-03/prompt-08/`
- `docs/codex/03-runs/phase-04/prompt-09/`
- `docs/codex/03-runs/phase-04/prompt-10/`
- `docs/codex/03-runs/phase-04/prompt-11/`
- relevant approved decisions in `docs/codex/04-decisions/approved/`
- relevant promoted artifacts in `docs/codex/05-artifacts/`
- relevant replace-later notes in `docs/codex/06-production-replace/`

Carry forward these confirmed findings:
- prompt-11 is complete and did not require a correction-pass reopen
- guest donation must remain possible without prior registration
- guest checkout stays payment-only by default unless explicitly approved rules say otherwise
- identified donor checkout stays account-linked only under the approved identity-capture rules
- anonymous-display is only a visibility/display preference, not a third identity system
- guest donation must not silently create donor profile ownership, portal access, or auto-account creation
- donor portal access and donation capability remain separate boundaries
- donor payable safety, receipt boundaries, and separation from legacy accounting posting must remain intact
- legacy transactions must not be assumed safe for direct donor live-payment finalization
- verification, approval, role assignment, portal eligibility, and guardian linkage must stay separated
- current repo behavior is still narrower than the target requirement set
- Google sign-in remains a planned delta, not already-implemented behavior

Issue-handling rules:
- do not reopen prompt-11 unless a true contradiction is discovered
- do not widen scope beyond donor implementation slice planning
- do not implement application code yet
- if any saved report conflicts with prompt-12 assumptions, correct the codex docs for prompt-11/12 first, then continue
- preserve the prompt-09 donor permission matrix, prompt-10 donor payable model, and prompt-11 guest onboarding rules together
- keep guest donation slices separate from donor portal/account slices where helpful

Run `docs/codex/01-prompts/prompt-12-donor-implementation-slices.md` with the following approved adaptation:

1. Break donor work into the smallest safe implementation slices.
2. Order the slices by dependency, while also mapping them to the later implementation prompts where possible.
3. Clearly separate:
   - guest donation slices
   - donor payable slices
   - donor account/auth slices
   - donor portal slices
   - receipt/history eligibility slices
   - later optional identity-claim/account-link slices
4. Identify which slices are schema-first, route-first, UI-first, service-first, or integration-first.
5. Identify rollback-safe checkpoints.
6. Identify which donor slices can ship independently.
7. Keep prompt-33, prompt-34, and prompt-35 from collapsing into one donor implementation wave.
8. Explicitly note which slices depend on earlier shared foundations such as the account-state and verification work.

Use these hard constraints while planning:
- prompt-33 may cover only the smallest approved guest-donation-entry slice and must not rely on unsafe direct finalization against legacy `transactions`
- prompt-34 must carry the dedicated donor payable foundation and any donor checkout activation that depends on that foundation
- prompt-35 must remain limited to donor auth/account behavior, donor portal gating, and donor receipt/history eligibility adaptation
- transaction-specific guest or identified receipt/status access is narrower than donor portal history access
- guest claim/account-link behavior remains later optional work unless explicitly approved

End with:
- donor slice order
- slice-by-slice goals
- dependency notes
- rollback checkpoints
- independently shippable slices
