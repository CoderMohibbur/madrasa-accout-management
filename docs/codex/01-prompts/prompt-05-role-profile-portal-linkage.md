Use the docs/codex-autopilot workflow from my project.

This task is PHASE_2_ROLE_PROFILE_PORTAL_LINKAGE_ANALYSIS_ONLY.
Do not implement code yet.

Read:
1) app/Models/User.php
2) app/Models/Donor.php
3) app/Models/Guardian.php
4) routes/donor.php
5) routes/guardian.php
6) relevant donor and guardian controllers, services, helpers, and policies

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
