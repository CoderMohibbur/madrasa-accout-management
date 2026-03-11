# Final Architecture

## Core Architecture

- Keep a single `users` table, one `web` guard, one password broker, and one session-auth system.
- Add shared account-state fields on `users` instead of separate authenticatable donor or guardian tables.
- Keep donor and guardian as domain profiles linked to the shared user account.
- Reuse existing `payments` and `receipts` for the minimal donor rollout.
- Preserve `transactions` as the legacy posted-money/reporting layer rather than the donor online source of truth.

## Payment And Ownership Architecture

- Model donor payments with `donation_intents` as pre-settlement payables, `payments` as attempts, `donation_records` as settled donor outcomes, and `receipts` as payment-specific post-settlement artifacts.
- Model guardian access with both route-level guardian profile state and object-level guardian-student/invoice ownership checks.
- Keep student, invoice, receipt, and payment authorization domain-aware and object-scoped.

## Routing And Policy Architecture

- Split routes into public, auth, guest-donation, donor, guardian informational, guardian protected, shared-home, and management buckets.
- Preserve existing route names: `dashboard`, `guardian.*`, `donor.*`, `payments.*`, and `management.*`.
- Replace blanket `verified` and raw `role:*` checks with explicit donor, guardian-informational, guardian-protected, and shared-home eligibility middleware plus object policies.
- Keep live protected `/guardian` routes protected while adding a separate guardian informational route space.
- Keep `management.surface` compatibility in place until later implementation safely replaces it.

## UI Architecture

- Use one shared light-first UI foundation, one shared shell system, and one shared component library across public, auth, donor, guardian, management, and multi-role surfaces.
- Build from the approved shared template families: auth form, informational content, portal overview, portal list, portal detail, payment outcome, donation entry, account state, and context chooser.
