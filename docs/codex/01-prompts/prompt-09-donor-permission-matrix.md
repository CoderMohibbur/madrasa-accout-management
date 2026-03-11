Use the docs/codex-autopilot workflow from my project.

This task is PHASE_4_DONOR_PERMISSION_MATRIX_ANALYSIS_ONLY.
Do not implement code yet.

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

Do not implement code.

End with:
- donor permission matrix
- guest donation permission matrix
- terminology rules
- initial safe rollout scope
- explicitly blocked actions and why
