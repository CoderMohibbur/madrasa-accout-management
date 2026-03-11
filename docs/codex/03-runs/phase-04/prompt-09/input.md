# Input

Use the docs/codex-autopilot workflow from my project.

This task is PHASE_4_DONOR_PERMISSION_MATRIX_ANALYSIS_ONLY.
Do not implement code yet.

Approved adaptations from prompts 01-08 and current confirmed findings:
- treat the live repository plus saved codex outputs as the authoritative baseline
- keep scope strictly limited to donor permission matrix analysis
- prompt-08 is complete and does not need reopening unless a true contradiction is discovered
- Google sign-in is an alternate identity bootstrap and linking mechanism, not a shortcut around approval, verification, portal gating, or guardian linkage
- donor login without verification, donor donation without verification, guardian login without verification, guest donation with optional identity capture, and open registration without automatic portal eligibility remain frozen target rules
- verification, approval, role assignment, portal eligibility, and guardian linkage must stay separated
- current repo behavior is still narrower than the target requirement set
- do not assume donor portal access and donation capability are the same thing
- do not silently assume legacy transactions are safe for donor live-payment finalization

Read:
1) donor routes, controllers, services, helpers, and views
2) payment-related routes, controllers, and views
3) app/Models/Donor.php
4) app/Models/Payment.php
5) app/Models/Transactions.php

Do only this:
1) define exactly which donor actions are allowed before verification
2) define exactly which donor actions are allowed after verification
3) define exactly which guest-donor actions are allowed with:
   - no identity data
   - phone only
   - email only
   - phone plus email
4) define whether the following are allowed or blocked in each donor state:
   - registration
   - login
   - guest donation initiation
   - guest donation completion
   - identified donation initiation
   - identified donation completion
   - receipt access
   - donation history
   - profile edit
   - payment status page access
   - recurring donation
   - saved payment methods
5) define the canonical terminology difference between:
   - guest donor
   - anonymous-display donor
   - hidden donor
   - identified donor
6) recommend the smallest safe donor feature set for initial rollout

Additional preservation rules:
- explicitly account for guest donation without prior registration
- explicitly account for optional phone/email/name during donation
- explicitly account for portal eligibility being separate from payment ability
- explicitly account for receipt/history/profile boundaries
- do not widen scope into donor payable implementation, guest donation implementation, or Google auth implementation

Do not implement code.

End with:
- donor permission matrix
- guest donation permission matrix
- terminology rules
- initial safe rollout scope
- explicitly blocked actions and why
