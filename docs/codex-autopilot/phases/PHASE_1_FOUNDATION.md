# PHASE 1 — FOUNDATION

## Objective

Prepare the new schema, role/auth boundaries, guardian/donor identity linking groundwork, canonical posting safety, and safe integration scaffolding required for later phases.

## Must Deliver

### Schema and State
- roles/permissions tables or equivalent RBAC foundation
- `guardians`
- `guardian_student`
- donor extension capable of `user_id` linkage
- `student_fee_invoices`
- `student_fee_invoice_items`
- `payments`
- `payment_gateway_events`
- `receipts`
- `audit_logs`

### Auth / Access
- keep single `users` model and single `web` guard
- add role/permission boundaries
- add ownership-safe middleware/policy scaffolding
- preserve current admin-approval login semantics unless a tightly scoped and documented alternative is explicitly approved

### Financial Safety
- introduce one canonical posting service or posting boundary for all new financial writes
- lock all new work to `credit = inflow`, `debit = outflow`
- document how legacy conflicting write paths will remain untouched or be isolated until later hardening
- do not build live gateway flow yet

### UI / Management
- minimal management UI only for role assignment and guardian/donor linking if needed
- no full portal UI yet

## Allowed Touch

Preferred:
- new migrations
- new models/services/policies/middleware/requests/tests
- narrow route additions
- minimal existing-file integration

Possible but sensitive:
- `routes/web.php`
- auth files if and only if role boundary integration truly requires it

Avoid:
- broad edits to `TransactionsController`
- broad edits to `LenderController`
- broad edits to report controllers/views
- route-name renames
- historical migration edits

## Validation Focus

- schema correctness
- auth boundary correctness
- policy/ownership correctness
- canonical posting rule correctness
- no route-name breakage
- compatibility with existing application
- no broad damage to current accounting or reporting logic

## Required Phase Gate Before Completion

Phase 1 is not complete unless:
- new schema exists
- posting safety for all future new flows is defined in code
- later portal/payment phases can avoid legacy conflicting write paths
- baseline test status is documented
- change manifest + validation manifest are updated
