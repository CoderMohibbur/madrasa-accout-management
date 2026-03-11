Use the docs/codex-autopilot workflow from my project.

This task is PHASE_3_OPEN_REGISTRATION_MODEL_ANALYSIS_ONLY.
Do not implement code yet.

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

Do not implement code.

End with:
- current registration conflicts
- recommended open registration model
- public registration flow
- donor registration flow
- guardian registration flow
- later role-expansion rules
- minimal safe rollout version
