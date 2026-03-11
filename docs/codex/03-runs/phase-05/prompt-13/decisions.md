# Decisions

- Guardian informational access and guardian protected access are approved as separate boundaries; the informational surface is authenticated, light, and non-sensitive, while the protected surface remains linkage-controlled.
- Guardian login and guardian informational access must not depend on universal email or phone verification.
- Registration or guardian intent capture may create only an unlinked informational-state guardian record; it must not create protected student, invoice, receipt, or payment access.
- Role assignment alone does not grant guardian portal entry, and linkage alone remains the additional requirement for protected student-owned data access.
- Student lists, student details, invoice visibility, payment history, receipt visibility, and invoice payment actions all remain linkage-controlled or authorization-controlled surfaces.
- The smallest safe guardian rollout is: base account plus guardian intent, a separate guardian informational portal, and a separately gated protected guardian portal for linked guardians only.
- The current repository is narrower than the approved target because all live `/guardian` routes are currently protected-scope routes behind `auth`, `verified`, `role:guardian`, profile flags, and ownership checks.
- Prompt-12 donor slice-planning decisions remain preserved and were not reopened by prompt-13.
