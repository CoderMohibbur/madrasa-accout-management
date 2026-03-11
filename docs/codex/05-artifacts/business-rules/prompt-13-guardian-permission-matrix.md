# Prompt 13 Guardian Permission Matrix

## Guardian States

- `unauthenticated user`: public visitor only
- `authenticated but unverified user`: logged-in base account that may have guardian intent but no universal verification requirement
- `verified but unlinked guardian`: guardian-domain presence without student linkage
- `guardian-role user without portal eligibility`: guardian role exists but lifecycle or portal gates still block entry
- `guardian-role user with only informational portal eligibility`: active guardian-domain identity allowed into the light portal only
- `linked guardian with protected portal eligibility`: active linked guardian authorized for protected student-owned resources

## Informational Portal Rules

- authenticated only
- allowed for guardian-intent or unlinked guardian users
- non-sensitive institution information only
- admission information plus external application handoff only
- linkage/help/status messaging allowed
- no students, invoices, receipts, payment history, or payment-entry controls

## Protected Portal Rules

- requires guardian-domain eligibility plus explicit linkage and object-level authorization
- may expose linked students, invoices, payment history, receipts, and invoice payment actions
- registration, verification, role assignment, or Google sign-in alone must never grant this access

## Linkage-Controlled Boundaries

- student list and student detail
- student-linked academic/profile data
- invoice list, balances, and invoice detail
- payment history and receipt visibility
- payment initiation, resume, and manual-bank submission
- dashboard summaries derived from linked student financial data

## Minimal Safe Guardian Rollout

- one shared `users` account model
- guardian self-registration or guardian intent capture without protected access
- dedicated guardian informational portal for authenticated unlinked guardians
- separately gated protected guardian portal for linked guardians only
- no donor-boundary reopening and no admission-system expansion
