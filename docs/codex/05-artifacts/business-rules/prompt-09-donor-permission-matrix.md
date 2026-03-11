# Prompt 09 Donor Permission Matrix

## Core Donor Rules

- Donor payment ability is separate from donor portal eligibility
- Donor login does not require universal verification
- Donor donation does not require universal verification
- Guest donation does not require prior registration
- Guest identity fields may be optional, but operational traceability is mandatory

## Access Boundaries

- Transaction-specific payment status and receipt access may be narrower than portal eligibility
- Full donation history and receipt-history access require donor portal eligibility
- Base account profile edit is broader than donor portal history access
- Verification, approval, and portal eligibility remain separate axes

## Terminology

- `guest donor`: no required account, no implied portal access
- `identified donor`: linked or known donor/account identity
- `anonymous-display donor`: internally traceable but hidden in public display contexts
- `hidden donor`: legacy synonym only, not a separate permission class

## Initial Safe Rollout

- Guest donation
- Identified donor donation
- Narrow payment-status and receipt access
- Read-only donor portal history only for portal-eligible donors
- No recurring donation
- No saved payment methods
- No legacy-transaction shortcut for donor live-payment finalization
