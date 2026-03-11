# Input

Use the docs/codex-autopilot workflow from my project.

This task is PHASE_5_GUARDIAN_PERMISSION_MATRIX_ANALYSIS_ONLY.
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
- relevant approved decisions in `docs/codex/04-decisions/approved/`
- relevant promoted artifacts in `docs/codex/05-artifacts/`

Carry forward these confirmed findings:
- Phase 04 prompt-12 is complete.
- No prompt-11 reopen is needed.
- No prompt-12 correction pass is needed.
- There are no blockers.
- Preserve the approved separation boundaries already established:
  - guest payment / guest entry
  - donor payable foundation
  - donor auth/account
  - donor portal/history
- Do not reopen completed donor slice-planning decisions unless a real contradiction is found.
- Keep one shared authenticated `users` account model.
- Guardian informational access and guardian protected access remain separate derived states.
- Verification, approval, role assignment, portal eligibility, and guardian linkage remain separate axes.
- Guardian self-registration may create only an unlinked informational-state domain record; it must not create protected access.
- Guardian login and guardian informational access must not depend on universal email or phone verification.
- Guardian protected access remains linkage- and authorization-controlled at all times.
- Admission scope remains informational only with an external application handoff.
- Google sign-in remains a later planned delta and must not be treated as already implemented behavior.

Issue-handling rules:
- stay strictly inside guardian permission matrix analysis
- do not implement application code
- do not widen scope into guardian implementation slices yet
- if a real contradiction is found against prior approved prompt outputs, correct the minimum necessary prompt-13 docs before continuing
- preserve the already approved donor boundaries and do not collapse donor and guardian scopes together

Run `docs/codex/01-prompts/prompt-13-guardian-permission-matrix.md` with the following approved adaptation:

Read:
1. guardian routes, controllers, services, helpers, and views
2. `app/Models/Guardian.php`
3. `app/Models/Student.php`
4. `app/Models/StudentFeeInvoice.php`
5. relevant guardianship/linkage logic
6. the saved prompt-03 through prompt-08 guardian/account-boundary decisions
7. prompt-12 only as a carry-forward boundary-preservation constraint, not as donor scope to be reopened

Do only this:
1. define exactly which guardian actions are allowed for:
   - unauthenticated user
   - authenticated but unverified user
   - verified but unlinked guardian
   - guardian-role user without portal eligibility
   - guardian-role user with only informational portal eligibility
   - linked guardian with protected portal eligibility
2. separate informational access from protected student-linked access
3. define the minimum safe guardian informational portal
4. define everything that must remain linkage-controlled or authorization-controlled
5. identify the smallest safe guardian rollout scope
6. clearly note where the current repository is narrower than the approved target model

Do not implement code.

End with:
- guardian permission matrix
- informational portal scope
- protected portal scope
- linkage-controlled boundaries
- minimal safe guardian rollout scope
