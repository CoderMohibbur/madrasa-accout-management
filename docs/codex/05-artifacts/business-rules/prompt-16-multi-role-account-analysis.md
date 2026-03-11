# Prompt 16 Multi-Role Account Analysis

## Target Multi-Role Model

- one shared authenticated `users` account
- additive donor and guardian role membership on that account
- separate derived contexts:
  - donor portal
  - guardian informational portal
  - guardian protected portal
- no blended donor-plus-guardian portal surface

## Role Expansion Rules

- add roles additively on the same account
- do not replace or silently merge existing domain records
- donor role expansion never implies guardian linkage or guardian protected access
- guardian role expansion may begin as unlinked informational-state access only
- guardian protected access arrives only after linkage-controlled authorization
- later optional identity-claim or account-link flows must attach to the existing account without auto-creating ownership or cross-domain entitlements

## Scope Isolation Rules

- donor context shows donor-owned donations and donor-visible receipts only
- guardian informational context stays non-sensitive
- guardian protected context shows only linkage-controlled student, invoice, payment, and receipt data
- shared login does not create shared data scope
- neutral multi-role surfaces must not aggregate donor and guardian records together

## Role Switching Rules

- `/donor` and `/guardian` are explicit context routes
- switching is explicit and eligibility-based
- switching does not grant access by itself
- donor-to-guardian switching lands in informational or protected guardian context based on actual eligibility
- deep links remain domain-local and keep their own authorization checks

## Multi-Role Home Rules

- derive home behavior from eligible contexts, not raw role ordering
- one eligible context: redirect directly
- multiple eligible contexts: show a neutral chooser
- neutral chooser: status and context selection only, with no mixed-scope dashboard data

## Minimal Safe Rollout

- neutral chooser for already multi-eligible users
- explicit donor/guardian switching for already eligible contexts
- no merged dashboard
- no self-service role claiming
- no donor-boundary reopening
- no weakening of protected guardian routes before shared account and verification foundations land
