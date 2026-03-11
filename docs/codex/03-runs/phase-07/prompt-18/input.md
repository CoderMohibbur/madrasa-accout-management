# Input

Use the docs/codex-autopilot workflow from my project.

Task: Execute the next approved step after prompt-17 completion.

Run:
`docs/codex/01-prompts/prompt-18-route-middleware-policy.md`

Important carry-forward constraints from the latest report:
- Phase 06 prompt-17 is complete.
- Prompt-17 completed successfully.
- No blocker is present.
- No correction pass is required.
- Preserve the approved multi-role implementation slice plan from prompt-17.
- Keep multi-role work out of donor and guardian implementation phases except where explicitly approved by the slice plan.
- Do not reopen donor or guardian scope unless a real contradiction is found.
- Preserve the narrow slice order already approved:
  - shared eligibility foundation
  - neutral chooser
  - in-portal switching
  - final redirect/middleware cleanup

Required process:
1. Read the required control/orchestrator docs for the codex-autopilot workflow.
2. Execute prompt-18 exactly within its allowed scope.
3. Save outputs in the correct run folder for prompt-18.
4. Promote durable decisions/artifacts if the workflow requires it.
5. Perform a contradiction / blocker pass before finalizing.

Prompt file executed:

Use the docs/codex-autopilot workflow from my project.

This task is PHASE_7_ROUTE_MIDDLEWARE_POLICY_ANALYSIS_ONLY.
Do not implement code yet.

Read:
1) routes/auth.php
2) routes/web.php
3) routes/donor.php
4) routes/guardian.php
5) routes/payments.php
6) relevant auth, role, portal, and policy middleware
7) protected path docs and implementation guardrail docs

Do only this:
1) analyze where current verified middleware blocks required new behavior
2) analyze where current role middleware or policy assumptions are too broad or too strict
3) design the safest additive-first route structure for:
   - public info
   - auth
   - guest donation entry and checkout
   - donor portal
   - guardian informational portal
   - guardian protected portal
   - multi-role home
4) define which middleware or policy changes are required
5) preserve legacy management routes, names, and behavior unless absolutely necessary
6) identify route-name or middleware migration risks

Do not implement code.

End with:
- current routing conflicts
- target route structure
- required middleware changes
- required policy changes
- compatibility warnings
- migration risks
