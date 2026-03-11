# Prompt 40 Route Middleware Policy Finalization

## Final Route Buckets

- `/dashboard`
  - keeps the `dashboard` route name
  - stays on `auth + portal.home`
  - preserves prompt-39's direct redirect, chooser, onboarding fallback, and management-compatible split
- `/donor*`
  - keeps `donor.*` route names
  - now runs on `auth + donor.access`
- `/guardian/info*`
  - keeps `guardian.info.*` route names
  - now runs on `auth + guardian.info.access`
- `/guardian*`
  - keeps `guardian.*` route names
  - stays on `auth + guardian.protected`
- `payments.manual-bank.show`
  - keeps the existing `payments.*` route name
  - now runs on `auth + can:view,payment`
- shurjoPay browser returns
  - keep the existing `payments.shurjopay.return.*` names
  - now run on `auth` and reusable payment ownership authorization instead of blanket `verified`

## Reusable Authorization Rules

- invoice payment initiation
  - `StudentFeeInvoicePolicy::pay()`
  - reused by both payment form requests and `StudentFeeInvoicePayableResolver`
- payment detail/status viewing
  - `PaymentPolicy::view()`
  - allows only:
    - management
    - exact payer user
    - protected guardian invoice owner
- protected student and invoice reads
  - `StudentPolicy::view()`
  - `StudentFeeInvoicePolicy::view()`
  - both now require real protected guardian eligibility, not only profile flags
- receipt viewing
  - `ReceiptPolicy::view()`
  - keeps donor exact-user access and protected guardian invoice-derived access separated

## Boundary Preservation

- no prompt-39 chooser or switcher rollback
- no mixed donor-plus-guardian records on `/dashboard`
- no reopening of donor payable expansion or broader identity merge work
- no renaming of `dashboard`, `donor.*`, `guardian.*`, `guardian.info.*`, `payments.*`, or `management.*`
- legacy management dashboard behavior remains intact while donor-profile fallthrough is closed
