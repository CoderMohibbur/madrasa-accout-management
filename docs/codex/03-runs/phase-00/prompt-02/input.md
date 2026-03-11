# Input

Use the docs/codex-autopilot workflow from my project.

Interpretation note for this codex run:
- Apply the prompt-01 workflow guardrails and use the live repository state as the source of truth.
- `run_state.json` commit metadata trails the live safe branch by one autopilot-only handoff commit, so read current code and latest handoff/report artifacts instead of assuming a blank pre-implementation baseline.

This task is PHASE_0_CURRENT_SYSTEM_INVENTORY_ONLY.
Do not implement code yet.

Read:
1) app/Http/Controllers/Auth/*
2) app/Http/Requests/Auth/LoginRequest.php
3) app/Models/User.php
4) app/Models/Donor.php
5) app/Models/Guardian.php
6) app/Models/Student.php
7) app/Models/StudentFeeInvoice.php
8) app/Models/Payment.php
9) app/Models/Transactions.php
10) routes/auth.php
11) routes/web.php
12) routes/donor.php
13) routes/guardian.php
14) routes/payments.php
15) config/auth.php
16) config/services.php
17) composer.json
18) resources/views/guardian/*
19) resources/views/donor/*

Additional read set required to inventory the live implemented state safely:
20) app/Models/Concerns/HasRoles.php
21) app/Models/Role.php
22) app/Models/Receipt.php
23) app/Models/PaymentGatewayEvent.php
24) app/Policies/StudentPolicy.php
25) app/Policies/StudentFeeInvoicePolicy.php
26) app/Policies/ReceiptPolicy.php
27) app/Services/GuardianPortal/GuardianPortalData.php
28) app/Services/DonorPortal/DonorPortalData.php
29) app/Services/Payments/StudentFeeInvoicePayableResolver.php
30) app/Services/Payments/PaymentWorkflowService.php
31) app/Http/Middleware/EnsureUserHasRole.php
32) app/Http/Middleware/EnsureManagementSurfaceAccess.php
33) app/Http/Middleware/RedirectPortalUsersFromLegacyDashboard.php
34) bootstrap/app.php
35) routes/management.php
36) config/payments.php
37) resources/views/components/guardian-layout.blade.php
38) resources/views/components/donor-layout.blade.php
39) resources/views/payments/*
40) resources/views/management/*

Do only this:
1) inventory the current auth model
2) inventory the current verification model
3) inventory the current role model
4) inventory the current donor flow
5) inventory the current guardian flow
6) inventory the current payment flow
7) inventory the current routing and middleware structure
8) inventory the current donor and guardian UI/view structure
9) identify the most important current coupling points and assumptions

Do not redesign yet.
Do not implement code.

End with:
- auth inventory
- verification inventory
- role and portal inventory
- donor inventory
- guardian inventory
- payment inventory
- route and middleware inventory
- UI inventory
- top coupling risks
