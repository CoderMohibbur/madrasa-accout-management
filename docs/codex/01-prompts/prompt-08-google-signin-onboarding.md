Use the docs/codex-autopilot workflow from my project.

This task is PHASE_3_GOOGLE_SIGNIN_ONBOARDING_ANALYSIS_ONLY.
Do not implement code yet.

Read:
1) composer.json
2) config/services.php
3) auth routes and controllers
4) app/Models/User.php
5) app/Models/Donor.php
6) app/Models/Guardian.php

Do only this:
1) analyze current Google sign-in readiness
2) identify missing dependency, package, config, and environment requirements
3) design first-time Google sign-in behavior
4) design Google account linking behavior when a local account already exists
5) define duplicate-email handling rules
6) define how donor-only, guardian-only, and dual-role expansion should work
7) define what should happen when the matched local account is:
   - unverified
   - unapproved
   - inactive
   - donor-only
   - guardian-only
   - dual-role
8) define the safest minimal rollout scope for Google sign-in

Do not implement code.

End with:
- readiness gap list
- target Google sign-in model
- account-linking rules
- risk areas
- minimal safe rollout scope
