Use the docs/codex-autopilot workflow from my project.

This task is PHASE_3_EMAIL_PHONE_VERIFICATION_COHABITATION_ANALYSIS_ONLY.
Do not implement code yet.

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

Do not implement code.

End with:
- verification coexistence model
- identifier rules
- duplicate rules
- anti-abuse rules
- donor/guardian implications
- unresolved risks if any
