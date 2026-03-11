# Prompt 18 Route Middleware Policy

## Target Route Buckets

- Public info:
  - `/`
  - additive admission information route(s)
  - additive public donation entry route(s)
- Auth:
  - keep existing `login`, `register`, `password.*`, and `verification.*`
  - keep `dashboard` as the shared landing route name
- Guest donation:
  - separate public-or-auth-optional donation route space
  - separate narrow status/receipt lookup surface
  - separate provider return/webhook callbacks
- Donor portal:
  - keep `/donor`
  - later gate with donor-portal eligibility middleware
- Guardian informational:
  - add separate authenticated informational prefix
  - keep it distinct from live protected `/guardian`
- Guardian protected:
  - keep live `/guardian`
  - later gate with guardian-protected eligibility middleware plus object policies
- Multi-role home:
  - keep `/dashboard`
  - use eligibility-based redirect or neutral chooser behavior

## Middleware Direction

- keep `role:management` and management-only route boundaries
- keep `management.surface` away from the final neutral chooser path
- replace blanket portal `verified` dependence after prompts 29-32 with:
  - base account-state or approval middleware
  - donor portal eligibility middleware
  - guardian informational eligibility middleware
  - guardian protected eligibility middleware
  - shared-home eligible-context middleware
- move payment access toward dedicated payment ownership checks instead of raw role-only route gates

## Policy Direction

- keep `StudentPolicy` protected-only
- keep `StudentFeeInvoicePolicy` protected-only
- keep `ReceiptPolicy` domain-aware across donor and guardian receipt cases
- add reusable payment authorization rules for initiation and detail/status access as later implementation formalizes route cleanup

## Compatibility Rules

- preserve `dashboard` route name
- preserve current management route names
- preserve current `guardian.*`, `donor.*`, `payments.*`, and `management.*` names unless a later phase explicitly introduces shims
- keep live `/guardian` links protected during additive rollout
