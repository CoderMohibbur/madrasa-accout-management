# Prompt 03 Frozen Business Rules

## Core Identity

- Public donor and guardian self-registration are required target behaviors.
- One authenticated account may hold donor and guardian roles simultaneously.
- Verification cannot be a universal prerequisite for all allowed donor or guardian entry flows.

## Donation

- Guest donation and direct amount-based donation are mandatory target behaviors.
- Portal access and mandatory account creation must not be required to donate.
- Human identity fields may be optional, but reconciliation and anti-abuse traceability may not be optional.
- Guest donation data must never auto-grant portal access.

## Guardian Access

- Unverified or unlinked guardians may access only light informational surfaces after login.
- Student-linked academic, invoice, receipt, and payment-sensitive surfaces stay linkage- and authorization-controlled.
- Admission scope is informational only with an external application handoff.

## Later-Phase Required Capabilities

- Dual email and phone verification support
- Google sign-in as an optional onboarding/sign-in path
- explicit multi-role context switching without scope leakage
