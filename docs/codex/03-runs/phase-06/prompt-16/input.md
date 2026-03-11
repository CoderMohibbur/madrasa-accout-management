# Input

Use the docs/codex-autopilot workflow from my project.

Task: Execute the next approved step after prompt-15 completion.

Run:
`docs/codex/01-prompts/prompt-16-multi-role-account-analysis.md`

Important carry-forward constraints from the latest report:
- Phase 05 prompt-15 is complete.
- Prompt-15 completed successfully.
- No blocker is present.
- No correction pass is required.
- Preserve the approved guardian implementation slice plan from prompt-15.
- Keep the future light guardian portal additive and non-sensitive.
- Keep already-live protected `/guardian` pages protected until shared account and verification foundations are in place.
- Do not reopen donor planning, prompt-13 guardian permission decisions, or prompt-14 admission-information boundary decisions unless a real contradiction is found.

Required process:
1. Read the required control/orchestrator docs for the codex-autopilot workflow.
2. Execute prompt-16 exactly within its allowed scope.
3. Save outputs in the correct run folder for prompt-16.
4. Promote durable decisions/artifacts if the workflow requires it.
5. Perform a contradiction / blocker pass before finalizing.

Prompt file executed:

Use the docs/codex-autopilot workflow from my project.

This task is PHASE_6_MULTI_ROLE_ACCOUNT_ANALYSIS_ONLY.
Do not implement code yet.

Read:
1) app/Models/User.php
2) app/Models/Donor.php
3) app/Models/Guardian.php
4) auth-related controllers/routes
5) donor routes
6) guardian routes
7) relevant dashboard/home routes/controllers/views

Do only this:
1) define how one authenticated user may safely hold both donor and guardian roles
2) define how role expansion should happen over time
3) define how donor-owned data and guardian-owned data must remain isolated
4) define how navigation and switching between /donor and /guardian should behave
5) define the safest multi-role dashboard/home behavior
6) identify the smallest safe rollout version of multi-role access

Do not implement code.

End with:
- target multi-role model
- role-expansion rules
- scope-isolation rules
- role-switching behavior
- multi-role home rules
- minimal safe rollout version
