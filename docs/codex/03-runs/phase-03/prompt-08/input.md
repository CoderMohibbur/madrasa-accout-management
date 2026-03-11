# Input

Use the docs/codex-autopilot workflow from my project.

This task is PHASE_3_GOOGLE_SIGNIN_ONBOARDING_ANALYSIS_ONLY.
Do not implement code yet.

Approved adaptations from prompts 01-07 and current confirmed findings:
- treat the live repository plus saved codex outputs as the authoritative baseline
- keep scope strictly limited to Google sign-in onboarding analysis
- prompt-07 is complete and does not need reopening unless a true contradiction is discovered
- the current verification stack is still email-centric in the repo, but target design must keep verification separate from approval, role assignment, portal eligibility, and guardian linkage
- donor login without verification, donor donation without verification, guardian login without verification, and guest donation with optional identity capture remain frozen target rules
- open registration alone must not grant portal eligibility, guardian linkage, or protected access
- current repo behavior is still narrower than the target requirements
- Google sign-in remains a planned delta, not already-implemented behavior
- where real Google credentials or provider settings are required, use dummy placeholders and record replace-later items under `docs/codex/06-production-replace/`

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

Additional preservation rules:
- provider-asserted email verification may apply only to the email channel, not to phone verification, approval, portal eligibility, guardian linkage, or protected access
- Google sign-in must reuse the single-account model and the prompt-06 registration model
- do not widen scope into implementation, route refactors, or final donor permission design

Do not implement code.

End with:
- readiness gap list
- target Google sign-in model
- account-linking rules
- risk areas
- minimal safe rollout scope
