Use the docs/codex-autopilot workflow from my project.

This task is PHASE_5_GUARDIAN_PERMISSION_MATRIX_ANALYSIS_ONLY.
Do not implement code yet.

Read:
1) guardian routes, controllers, services, helpers, and views
2) app/Models/Guardian.php
3) app/Models/Student.php
4) app/Models/StudentFeeInvoice.php
5) relevant guardianship/linkage logic

Do only this:
1) define exactly which guardian actions are allowed for:
   - unauthenticated user
   - authenticated but unverified user
   - verified but unlinked guardian
   - guardian-role user without portal eligibility
   - guardian-role user with only informational portal eligibility
   - linked guardian with protected portal eligibility
2) separate informational access from protected student-linked access
3) define the minimum safe guardian informational portal
4) define everything that must remain linkage-controlled or authorization-controlled
5) identify the smallest safe guardian rollout scope

Do not implement code.

End with:
- guardian permission matrix
- informational portal scope
- protected portal scope
- linkage-controlled boundaries
- minimal safe guardian rollout scope
