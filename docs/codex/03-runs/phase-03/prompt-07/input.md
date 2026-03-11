# Input

Use the docs/codex-autopilot workflow from my project.

This task is PHASE_3_EMAIL_PHONE_VERIFICATION_COHABITATION_ANALYSIS_ONLY.
Do not implement code yet.

Approved adaptations from prompts 01-06 and current confirmed findings:
- treat the live repository plus saved codex outputs as the authoritative baseline
- keep scope strictly limited to email and phone verification coexistence analysis
- prompt-06 is complete and does not need reopening unless a true contradiction is discovered
- verification state must remain separate from approval, role assignment, portal eligibility, and guardian linkage
- open registration may create only a base identity or capture role intent; it must not auto-grant donor portal eligibility, guardian portal eligibility, guardian linkage, or protected access
- donor and guardian access goals frozen in prompt-03 remain binding
- current repo behavior is still narrower than the target requirement set
- guest donation, dual verification, and Google sign-in remain planned deltas rather than already-implemented behavior

Read:
1) app/Models/User.php
2) auth controllers, requests, middleware, and verification views
3) routes/auth.php
4) config/auth.php

Do only this:
1) design how email verification and phone verification should coexist
2) define whether phone is optional, required, or role-specific
3) define whether phone can be a login identifier or verification-only
4) define duplicate handling rules for email and phone
5) define resend, cooldown, throttling, anti-abuse, and retry rules
6) define the exact behavior when a user verifies:
   - neither
   - email only
   - phone only
   - both
7) define how this interacts with donor access, guardian access, and guest donation identity capture

Additional preservation rules:
- explicitly account for donor login without verification
- explicitly account for donor donation without verification
- explicitly account for guardian login without verification
- explicitly account for guest donation with optional identity capture
- do not widen scope into Google sign-in, guest donation implementation, or route refactoring

Do not implement code.

End with:
- verification coexistence model
- identifier rules
- duplicate rules
- anti-abuse rules
- donor/guardian implications
- unresolved risks if any
