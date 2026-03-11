# Prompt 37 Guardian Protected Portal Gating

## Protected Route Surface

- `GET /guardian`
- `GET /guardian/students/{student}`
- `GET /guardian/invoices`
- `GET /guardian/invoices/{invoice}`
- `GET /guardian/history`
- `POST /payments/shurjopay/initiate`
- `POST /payments/manual-bank/requests`

## Protected Access States

- `protected_eligible`
  - accessible account state
  - verified email
  - guardian profile present, active, portal-enabled, and not deleted
  - at least one linked student
  - result: protected `/guardian` routes and guardian payment initiation routes are available
- `email_unverified`
  - otherwise protected-ready guardian linkage exists
  - result: protected routes stay blocked; guardian informational routes remain available
- `unlinked`
  - guardian profile is portal-enabled but no linked student exists
  - result: protected routes stay blocked; guardian informational routes remain available
- `role_only` or `none`
  - no protected guardian profile/linkage state exists
  - result: protected routes fail closed

## Redirect Rules

- verified protected-eligible guardians use `/dashboard` -> `/guardian`
- unverified or unlinked guardian-context accounts stay on `/guardian/info*` when they otherwise have safe informational access
- management routing remains unchanged
- donor prompt-35 behavior remains unchanged
- multi-role chooser/switching remains deferred to prompt-39

## Payment Boundary

- guardian payment initiation uses the same protected guardian middleware as `/guardian`
- payment initiation still requires:
  - protected guardian eligibility
  - linked invoice ownership compatibility
  - positive invoice balance
- payment detail/manual-bank detail authorization remains narrower than route membership and stays tied to protected invoice authorization

## Preserved Boundaries

- `/guardian/info*` stays additive and non-sensitive
- protected `/guardian` route names stay unchanged
- donor routes and donor history remain untouched
- admission CTA placement remains limited to approved informational/public surfaces only
