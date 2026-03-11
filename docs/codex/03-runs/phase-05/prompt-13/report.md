# Report

This prompt defines the guardian permission matrix on top of the approved prompt-03 through prompt-08 account/boundary rules and the prompt-12 donor handoff constraints. No contradiction was found with prompt-12, and the approved donor slice-planning decisions remain untouched. The live repository is still narrower than the approved guardian target because every current `/guardian` route sits behind `auth` + `verified` + `role:guardian` and then requires an enabled guardian profile plus ownership checks. Prompt-13 therefore defines the target-approved guardian access rules without treating the current protected-only portal as the final model.

## State Interpretations Used In This Matrix

- `unauthenticated user`
  - public visitor with no logged-in account
- `authenticated but unverified user`
  - logged-in base account that may have guardian intent but has not completed email or phone verification; this state must not be blocked from safe guardian informational access merely because verification is incomplete
- `verified but unlinked guardian`
  - authenticated account with a guardian-domain record or guardian intent, at least one verified contact channel if present, but no student linkage or protected entitlement yet
- `guardian-role user without portal eligibility`
  - role assigned but guardian portal entry is still not allowed because the account/profile is inactive, suspended, deleted, portal-disabled, or otherwise lifecycle-ineligible
- `guardian-role user with only informational portal eligibility`
  - active guardian-domain identity allowed into the light guardian surface but still unlinked for student-sensitive access
- `linked guardian with protected portal eligibility`
  - active guardian-domain identity with valid linkage and object-level authorization for the requested student, invoice, receipt, or payment record

## Guardian Permission Matrix

Matrix rule:
- `Allowed` = approved target behavior.
- `Allowed with conditions` = allowed only through the stated non-sensitive or ownership-safe conditions.
- `Blocked` = outside the safe guardian boundary for that state.

| Action | Unauthenticated user | Authenticated but unverified user | Verified but unlinked guardian | Guardian-role user without portal eligibility | Guardian-role user with only informational portal eligibility | Linked guardian with protected portal eligibility |
| --- | --- | --- | --- | --- | --- | --- |
| Public registration / start guardian onboarding | Allowed | Blocked / not applicable once signed in; expansion can happen inside the same account later | Blocked / not applicable | Blocked / not applicable | Blocked / not applicable | Blocked / not applicable |
| Login / establish authenticated session | Allowed with conditions: only if a base account exists and approval/lifecycle rules allow sign-in | Not applicable / already authenticated | Not applicable / already authenticated | Not applicable / already authenticated | Not applicable / already authenticated | Not applicable / already authenticated |
| View public institution and admission information | Allowed | Allowed | Allowed | Allowed | Allowed | Allowed |
| Access guardian informational portal | Blocked until authenticated | Allowed with conditions: only if guardian intent or guardian-domain presence exists; no student-sensitive data | Allowed | Blocked while portal eligibility is not granted | Allowed | Allowed, but this does not replace protected checks |
| View linkage status, onboarding help, support contact, and what-access-next messaging | Blocked unless exposed publicly in a generic way | Allowed with conditions: authenticated self-only messaging | Allowed | Allowed with conditions: self-only status/help | Allowed | Allowed |
| Edit base account profile/contact details | Blocked | Allowed | Allowed | Allowed | Allowed | Allowed |
| View linked students list or student summary | Blocked | Blocked | Blocked | Blocked | Blocked | Allowed with conditions: only for authorized linked students |
| View student detail / student-linked academic information | Blocked | Blocked | Blocked | Blocked | Blocked | Allowed with conditions: only for the specific linked student authorized by policy |
| View invoice list / invoice detail | Blocked | Blocked | Blocked | Blocked | Blocked | Allowed with conditions: only for invoices linked through the guardian-student and invoice-ownership rules |
| View payment history / receipt numbers for student invoices | Blocked | Blocked | Blocked | Blocked | Blocked | Allowed with conditions: only for invoice-derived guardian-visible rows |
| Initiate or resume invoice payment | Blocked | Blocked | Blocked | Blocked | Blocked | Allowed with conditions: only for authorized linked invoice payables with positive balance |
| Submit manual-bank evidence for an invoice | Blocked | Blocked | Blocked | Blocked | Blocked | Allowed with conditions: only for authorized linked invoice payables |
| Access management surfaces | Blocked | Blocked unless separately management-eligible | Blocked unless separately management-eligible | Blocked unless separately management-eligible | Blocked unless separately management-eligible | Blocked unless separately management-eligible |

## Informational Portal Scope

The minimum safe guardian informational portal is an authenticated light surface for guardian-intent or guardian-domain users who are not yet linked for protected access.

It may include:
- non-sensitive institution information
- admission-related information only
- an external admission application link or button
- guardian onboarding steps, linkage instructions, and support contact details
- self-only status messaging about verification, approval, linkage review, or missing requirements
- a safe account/profile settings link for the current user
- context-switch or return-home links for legitimate multi-role users, provided no donor and guardian data scopes are mixed

It must not include:
- linked students
- student profile or academic records
- invoice summaries, counts, balances, or invoice details
- payment history, payment attempt data, or receipt lists
- payment-entry controls
- management dashboards or management reports
- any shortcut that implies linkage or protected entitlement

## Protected Portal Scope

The guardian protected portal is a separate, stricter boundary. It is the only guardian surface that may expose student-linked, invoice-linked, receipt-linked, or payment-sensitive data.

It may include:
- linked student list and student detail pages
- guardian-visible invoice lists and invoice detail pages
- guardian-visible payment history for linked invoice payables
- guardian-visible receipt numbers or receipt access where invoice ownership authorizes them
- payment initiation, resume, return, and manual-bank evidence flows for authorized invoice payables only

It requires all of the following:
- authenticated base account
- guardian-domain presence
- active and non-deleted guardian profile or equivalent future lifecycle state
- derived guardian protected eligibility, not merely raw role membership
- explicit guardian linkage or object-level authorization for the specific student/invoice/payment/receipt record requested

It must not be granted by:
- registration alone
- role assignment alone
- verification alone
- Google sign-in alone
- donor payment or donor portal activity
- contact-data similarity or inferred family relationship

## Linkage-Controlled Boundaries

The following surfaces must remain linkage-controlled or authorization-controlled at all times:

- any student list or student detail view
- student-linked academic/profile information
- invoice list, invoice summary, invoice detail, and outstanding balance visibility
- payment history, payment attempt details, and receipt visibility for student-linked invoices
- invoice payment initiation, manual-bank submission, and payment-return follow-up
- any dashboard cards or counts derived from linked students, invoices, receipts, or payments

Minimum authorization rules:
- student access requires guardian-student linkage
- invoice access requires guardian-student linkage plus `guardian_id` compatibility when the invoice is assigned
- payment access requires authorization through the linked invoice payable
- receipt access requires either direct issued-user ownership or invoice-derived guardian authorization; it must not widen beyond the linked invoice boundary

## Current Repository Gap Notes

The live repo is narrower than the approved target model in these ways:

- there is no separate guardian informational portal yet
- every current guardian route is protected by `auth` + `verified` + `role:guardian`
- `GuardianPortalData::requireGuardian()` requires portal-enabled, active, non-deleted guardian profiles before any guardian portal entry
- `StudentPolicy`, `StudentFeeInvoicePolicy`, and the invoice payable resolver already enforce linked protected access, but there is no equivalent light authenticated informational surface for unlinked guardians
- the current guardian dashboard, invoice, and history views are all protected-scope pages, not informational-scope pages
- the current repo therefore blocks guardian access earlier and more narrowly than the approved target for unverified or unlinked guardian users

## Minimal Safe Guardian Rollout Scope

The smallest safe guardian rollout that matches the approved target is:

1. Keep one shared `users` account model and one guardian-domain profile model.
2. Allow guardian self-registration or guardian intent capture without auto-granting protected access.
3. Add a dedicated guardian informational portal for authenticated guardian-intent or unlinked guardian users.
4. Keep that informational portal non-sensitive and admission-only plus onboarding/help/status messaging.
5. Preserve the protected guardian portal as a separate boundary for linked guardians only.
6. Keep all student, invoice, receipt, and payment-sensitive surfaces behind linkage and object-level authorization.
7. Do not let verification or Google sign-in substitute for linkage, protected entitlement, or portal eligibility.
8. Do not change donor payment, donor portal, or donor slice-planning decisions as part of guardian scope.

## Completion Status

- No contradiction with prompt-12 or earlier approved boundary decisions was found.
- No donor-scope correction was needed.
- Prompt-13 is complete.
- No hard blocker prevents prompt-14.
