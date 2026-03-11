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
