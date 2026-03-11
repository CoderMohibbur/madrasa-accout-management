# Prompt 02 Route And Portal Inventory

## Route Groups

- `/management`
  - middleware: `auth`, `verified`, `role:management`
  - routes: `management.dashboard`, `management.access-control`, `management.reporting.index`
- `/guardian`
  - middleware: `auth`, `verified`, `role:guardian`
  - routes: dashboard, linked student detail, invoices list/detail, payment history
- `/donor`
  - middleware: `auth`, `verified`, `role:donor`
  - routes: dashboard, donations list, receipts list
- `/payments/...`
  - guardian-only write routes for shurjoPay initiation and manual-bank submission
  - verified-user return/detail routes
  - public CSRF-exempt shurjoPay IPN
  - management-only manual-bank review queue

## Shared Compatibility Middleware

- `portal.home`
  - redirects portal users away from legacy `/dashboard`
  - guardian takes precedence over donor
- `management.surface`
  - keeps legacy management pages available to management-role users and unroled users
  - blocks guardian-only and donor-only users from those legacy surfaces

## Portal UI Surfaces

- Guardian UI
  - `guardian-layout`
  - dashboard
  - student detail
  - invoice list/detail with payment actions
  - payment history
- Donor UI
  - `donor-layout`
  - dashboard
  - donation list
  - receipt list
- Management additive UI
  - access-control foundation page
  - reporting page
  - manual-bank review queue
