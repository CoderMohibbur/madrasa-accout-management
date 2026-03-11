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
