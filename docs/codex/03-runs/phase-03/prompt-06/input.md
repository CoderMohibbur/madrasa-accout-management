# Input

Use the docs/codex-autopilot workflow from my project.

This task is PHASE_3_OPEN_REGISTRATION_MODEL_ANALYSIS_ONLY.
Do not implement code yet.

Approved adaptations from prompts 01-05 and current corrections:
- treat the live repository plus saved codex outputs as the authoritative baseline
- keep scope strictly limited to open registration model analysis
- prompt-04 deletion-axis duplication was already corrected; use the approved prompt-04 state model as authoritative
- prompt-05 boundary wording was already tightened; treat raw `email_verified_at` and current `verified` middleware as current implementation details, not final target rules
- open registration must not automatically create portal eligibility, guardian linkage, or protected access
- current repo is narrower than the broader target requirements, so guest donation, unverified-access redesign, dual verification, and Google sign-in remain planned deltas rather than already-implemented behavior

Read:
1) app/Http/Controllers/Auth/*
2) app/Http/Requests/Auth/*
3) routes/auth.php
4) app/Models/User.php
5) app/Models/Donor.php
6) app/Models/Guardian.php

Do only this:
1) analyze the current registration assumptions
2) design the safest open registration model for:
   - general/public user
   - donor
   - guardian
3) decide whether separate entry points or a unified entry point are safer
4) define how role selection or role creation should occur without exposing protected data
5) define how a later donor-to-guardian or guardian-to-donor role expansion should work
6) define the smallest safe rollout version of open registration

Additional preservation rules:
- preserve the single authenticated account model
- registration alone creates base identity and optional domain intent, not portal eligibility, linkage, or protected access
- do not claim guest donation, unverified-access redesign, dual verification, or Google sign-in are already implemented
- do not widen scope beyond open registration model analysis

Do not implement code.

End with:
- current registration conflicts
- recommended open registration model
- public registration flow
- donor registration flow
- guardian registration flow
- later role-expansion rules
- minimal safe rollout version
