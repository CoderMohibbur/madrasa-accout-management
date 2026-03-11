# Prompt 04 Account State Model

## State Axes

- Identity existence
- Email verification
- Phone verification
- Admin approval
- Role assignment
- Donor profile state
- Guardian profile state
- Portal eligibility
- Guardian linkage / authorization
- Account activity
- Deletion

## Derived Eligibility Rules

- Donor portal eligibility is not the same as donor role assignment.
- Guardian informational eligibility is not the same as guardian protected eligibility.
- Multi-role eligibility requires explicit context separation.
- Suspension or soft deletion overrides all portal access.

## Mandatory Guardrails

- Do not keep using `email_verified_at` as approval plus verification.
- Do not derive protected guardian access from role membership alone.
- Do not treat guest donation as implicit account creation.
