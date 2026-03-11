# Risks

- `email_verified_at` still mixes approval and verification semantics, while the Laravel verification scaffold remains present in routes and profile UI.
- Donor portal history is still coupled to the legacy `transactions` table and the `transactions_types.key = donation` convention rather than a dedicated donor payment/payable model.
- Portal access depends on linked profile status flags as well as roles, so incomplete backfill of `user_id`, `portal_enabled`, `isActived`, or `isDeleted` would silently break portal access.
- `/dashboard` redirect behavior is order-dependent for multi-role users because guardian precedence is hard-coded ahead of donor precedence.
- Payment return and review behavior is centralized in `PaymentWorkflowService`, making later changes to payment flow high-impact across controllers, routes, receipts, and optional posting.
- Legacy management pages still admit unroled users through `management.surface`, so any future management-role tightening needs explicit migration and regression planning.
