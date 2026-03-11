# Input

Use the docs/codex-autopilot workflow from my project.

This task is PHASE_5_ADMISSION_INFORMATION_BOUNDARY_ANALYSIS_ONLY.
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
- `docs/codex/03-runs/phase-04/prompt-09/`
- `docs/codex/03-runs/phase-04/prompt-10/`
- `docs/codex/03-runs/phase-04/prompt-11/`
- `docs/codex/03-runs/phase-04/prompt-12/`
- `docs/codex/03-runs/phase-05/prompt-13/`
- relevant approved decisions in `docs/codex/04-decisions/approved/`
- relevant promoted artifacts in `docs/codex/05-artifacts/`

Carry forward these confirmed findings:
- Phase 05 prompt-13 is complete.
- Prompt-13 completed successfully.
- No blocker is present.
- No correction pass is required.
- Preserve the approved guardian boundary established by prompt-13:
  - informational access remains distinct
  - unlinked guardians may log in
  - student/invoice/payment-sensitive access remains linkage-controlled
- Keep current-repo narrower guardian portal behavior distinct from the approved target boundary.
- Do not reopen prompt-13 unless a real contradiction is found.
- Admission scope remains informational only with an external application handoff.
- This project must not become a full admission application system.
- Prompt-12 donor boundaries remain preserved and are out of scope for prompt-14.

Issue-handling rules:
- stay strictly inside admission-information boundary analysis
- do not implement code
- do not widen scope into guardian implementation slices, external admission URL configuration, or UI implementation yet
- if a real contradiction is found, correct the minimum necessary prompt-14 docs before continuing

Run `docs/codex/01-prompts/prompt-14-admission-information-boundary.md` with the following approved adaptation:

Read:
1. relevant public routes/views
2. relevant guardian routes/views
3. current page/content structures touching institution or admission information
4. prompt-13 guardian informational portal decisions
5. current public welcome-page admission link behavior

Do only this:
1. define what admission-related information may be shown publicly
2. define what admission-related information may be shown only after login
3. define how the external admission application button/link should behave
4. define what this project must not do so it does not become a full admission application system
5. define the safest, lowest-complexity content approach for institution and admission information
6. clearly distinguish current repository behavior from the approved target boundary

Do not implement code.

End with:
- public admission-information scope
- authenticated admission-information scope
- external application link rules
- hard non-goals
- content-approach recommendation
