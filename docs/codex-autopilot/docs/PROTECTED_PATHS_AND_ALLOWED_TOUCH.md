# PROTECTED PATHS AND ALLOWED TOUCH — v4 PROJECT-HARDENED

## Intent

This file defines the default posture toward the existing codebase: protect first, touch only as needed.

## Global Protected-By-Default Areas

Treat these as protected unless the current phase explicitly requires integration there:

- `app/Http/Controllers/**`
- `app/Models/**`
- `routes/**`
- `resources/views/layouts/**`
- `resources/views/**` existing production pages
- `database/migrations/**` historical migrations
- `tests/**` existing stable suites

## Project-Specific Critical Paths

These are high-risk files/modules. Touch only with explicit justification:

### Auth / Identity
- `app/Http/Controllers/Auth/RegisteredUserController.php`
- `app/Http/Requests/Auth/LoginRequest.php`
- `app/Models/User.php`
- `routes/auth.php`

### Financial Write Paths
- `app/Http/Controllers/TransactionCenterController.php`
- `app/Http/Controllers/TransactionsController.php`
- `app/Http/Controllers/LenderController.php`
- `app/Support/TransactionLedger.php`
- `app/Models/Transactions.php`
- `database/migrations/2024_10_09_132953_create_table_transactions_table.php`
- `database/seeders/TransactionsTypeSeeder.php`

### Reporting / Dashboard
- `app/Http/Controllers/DashboardController.php`
- `app/Http/Controllers/ReportController.php`
- `app/Http/Controllers/Reports/**`
- `resources/views/reports/**`
- `resources/views/dashboard/**`

### Student / Shared Navigation / Route Sensitivity
- `app/Models/Student.php`
- `app/Http/Controllers/StudentController.php`
- `resources/views/layouts/navigation.blade.php`
- `routes/web.php`

## Explicitly Frozen Behaviors

Until a phase explicitly authorizes otherwise, preserve:
- existing route names used in shared navigation
- existing auth approval/login semantics
- existing report totals semantics
- student legacy aliases and compatibility accessors
- historical migration contents

## Allowed Touch Without Special Escalation

Generally safe additions:
- new migrations
- new request classes
- new middleware
- new policy classes
- new service classes
- new portal controllers
- new dedicated Blade views for new portals
- new tests for the current phase
- docs/state/handoff/report files
- new route groups that do not rename existing route names

## Phase-Specific Allowed Touch Guidance

### Phase 1
Allowed:
- additive schema for roles/permissions, guardians, guardian_student, donors extension, invoices, payments, receipts, audits
- new middleware/policies/services
- controlled route grouping additions without renaming existing routes
- minimal auth integration needed for role boundaries
- minimal management UI for linking roles/profiles

Discouraged unless proven necessary:
- edits to legacy transaction write controllers
- edits to report controllers/views
- edits to shared navigation beyond additive portal links

### Phase 2 / Phase 3
Allowed:
- new guardian/donor controllers, policies, queries, views
- ownership checks
- read-only portal pages
- minimal route additions

Not allowed:
- payment gateway logic
- route-name rewrites
- broad management UI rewrites

### Phase 4
Allowed:
- reporting services and report screens using new safe data boundaries
- additive indexes

Not allowed:
- silent changes to legacy report totals without documented reasoning

### Phase 5
Allowed:
- payment services, callback/webhook controllers, state transitions, idempotency
- receipt generation linked to safe posting path

Not allowed:
- bypassing server-side payable resolution
- introducing gateway logic before Phase 1 safety outputs exist

### Phase 6
Allowed:
- controlled restriction/retirement of unsafe legacy write paths
- final cleanup after regression verification

## Escalation Rule

If a current phase would require touching multiple critical paths at once, stop and re-evaluate scope before continuing.
