Use the docs/codex-autopilot workflow from my project.

This task is PHASE_2_ACCOUNT_STATE_MODEL_ANALYSIS_ONLY.
Do not implement code yet.

Read:
1) app/Http/Requests/Auth/LoginRequest.php
2) app/Models/User.php
3) app/Models/Donor.php
4) app/Models/Guardian.php
5) docs/codex-autopilot/docs/STATE_MACHINE_SPEC.md
6) docs/codex-autopilot/state/risk_register.md

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
