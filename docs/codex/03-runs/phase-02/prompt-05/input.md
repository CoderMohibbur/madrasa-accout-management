# Input

Use the docs/codex-autopilot workflow from my project.

Interpretation note for this codex run:
- Reuse prompt-01 through prompt-04 outputs as governing context.
- Treat the live repository as authoritative where autopilot `run_state.json` trails the safe-branch head by one autopilot-only handoff commit.
- Treat guest donation, unverified-access redesign, dual verification, and Google sign-in as planned deltas, not already-implemented behavior.
- Prompt-04 was corrected before this run to keep deletion separate from identity-existence state.

This task is PHASE_2_ROLE_PROFILE_PORTAL_LINKAGE_ANALYSIS_ONLY.
Do not implement code yet.

Read:
1) app/Models/User.php
2) app/Models/Donor.php
3) app/Models/Guardian.php
4) routes/donor.php
5) routes/guardian.php
6) relevant donor and guardian controllers, services, helpers, and policies

Additional read set used to adapt prompt-05 from saved prompts 01-04:
7) docs/codex/03-runs/phase-00/prompt-01/report.md
8) docs/codex/03-runs/phase-00/prompt-02/report.md
9) docs/codex/03-runs/phase-01/prompt-03/report.md
10) docs/codex/03-runs/phase-02/prompt-04/report.md
11) docs/codex/05-artifacts/workflow/prompt-01-guardrail-summary.md
12) docs/codex/05-artifacts/route-maps/prompt-02-route-and-portal-inventory.md
13) docs/codex/05-artifacts/business-rules/prompt-03-frozen-business-rules.md
14) docs/codex/05-artifacts/state-models/prompt-04-account-state-model.md
15) app/Models/Concerns/HasRoles.php
16) routes/web.php
17) routes/payments.php
18) bootstrap/app.php
19) app/Http/Controllers/Donor/DonorPortalController.php
20) app/Http/Controllers/Guardian/GuardianPortalController.php
21) app/Services/DonorPortal/DonorPortalData.php
22) app/Services/GuardianPortal/GuardianPortalData.php
23) app/Policies/StudentPolicy.php
24) app/Policies/StudentFeeInvoicePolicy.php
25) app/Policies/ReceiptPolicy.php
26) app/Http/Middleware/EnsureUserHasRole.php
27) app/Http/Middleware/EnsureManagementSurfaceAccess.php
28) app/Http/Middleware/RedirectPortalUsersFromLegacyDashboard.php
29) app/Services/Payments/StudentFeeInvoicePayableResolver.php

Do only this:
1) analyze how current role checks work
2) analyze how current donor and guardian profile existence is used
3) analyze how current portal eligibility is determined
4) analyze how guardian linkage and linked-data ownership are currently enforced
5) define the exact difference between:
   - holding a role
   - having a donor or guardian profile
   - having portal eligibility
   - being active
   - being deleted
   - being linked to student-owned data
6) define final authorization boundary rules for:
   - donor portal
   - guardian informational portal
   - guardian protected portal
   - shared/multi-role home

Do not implement code.

End with:
- current boundary findings
- final state distinctions
- donor boundary rules
- guardian informational boundary rules
- guardian protected boundary rules
- multi-role boundary rules
