Use the docs/codex-autopilot workflow from my project.

This task is PHASE_4_GUEST_DONATION_ONBOARDING_ANALYSIS_ONLY.
Do not implement code yet.

Read:
1) donor routes/controllers/views
2) payment routes/controllers/views
3) app/Models/User.php
4) app/Models/Donor.php
5) app/Models/Payment.php

Do only this:
1) define the exact guest-donation flow from entry to completion
2) define which donor identity fields are optional, required, or conditionally required
3) define when the system should:
   - create no account
   - create a lightweight user
   - create or attach a donor profile
   - only store payment-side contact information
4) define what should happen when phone or email is present but unverified
5) define whether and how a guest donor can later claim or convert donation history into a portal account
6) define the safest anti-abuse and traceability rules for guest donation

Do not implement code.

End with:
- guest donation flow
- identity-capture rules
- lightweight account-creation rules
- later account-claim rules
- anti-abuse and traceability rules
