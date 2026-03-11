# Input

Use the docs/codex-autopilot workflow from my project.

Interpretation note for this codex run:
- Apply prompt-01 guardrails, prompt-02 live inventory, and prompt-03 frozen business rules.
- Analyze the target state model without assuming the current repository already has the necessary field separation.
- Focus on state separation and transition safety only; do not propose migrations or code changes yet.

This task is PHASE_2_ACCOUNT_STATE_MODEL_ANALYSIS_ONLY.
Do not implement code yet.

Read:
1) app/Http/Requests/Auth/LoginRequest.php
2) app/Models/User.php
3) app/Models/Donor.php
4) app/Models/Guardian.php
5) docs/codex-autopilot/docs/STATE_MACHINE_SPEC.md
6) docs/codex-autopilot/state/risk_register.md

Additional read set required from prior codex outputs and live routing/middleware behavior:
7) docs/codex/03-runs/phase-00/prompt-02/report.md
8) docs/codex/03-runs/phase-01/prompt-03/report.md
9) routes/web.php
10) routes/auth.php
11) routes/guardian.php
12) routes/donor.php
13) routes/payments.php
14) app/Http/Controllers/Auth/RegisteredUserController.php
15) app/Http/Controllers/Auth/VerifyEmailController.php
16) app/Models/Concerns/HasRoles.php
17) app/Http/Middleware/EnsureUserHasRole.php
18) app/Http/Middleware/EnsureManagementSurfaceAccess.php
19) app/Http/Middleware/RedirectPortalUsersFromLegacyDashboard.php
20) app/Services/GuardianPortal/GuardianPortalData.php
21) app/Services/DonorPortal/DonorPortalData.php
22) app/Policies/StudentPolicy.php
23) app/Policies/StudentFeeInvoicePolicy.php

Do only this:
1) analyze how the current system conflates or overloads verification, approval, activation, role access, portal access, and linkage
2) analyze how email_verified_at or related fields are currently used and why that is unsafe or limiting
3) design the target account-state model separating at minimum:
   - identity existence
   - email verification
   - phone verification
   - admin approval
   - role assignment
   - donor profile existence
   - guardian profile existence
   - portal eligibility
   - guardian linkage or authorization
   - active or inactive status
   - deleted or soft-deleted state
4) define allowed transitions between those states
5) identify which current code assumptions would break under the new model
6) identify which separation rules are mandatory before new auth or portal behavior is implemented

Do not implement code.
Do not propose migrations yet.

End with:
- current state-model problems
- target state model
- state transition rules
- breaking assumptions
- mandatory separation rules before implementation
