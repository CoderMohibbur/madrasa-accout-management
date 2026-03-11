# Report

## Auth Inventory

- The application still uses a single authenticatable identity model: `App\Models\User`.
- `User` implements `MustVerifyEmail`, uses the `HasRoles` trait, and remains the only provider-backed auth model in `config/auth.php`.
- `config/auth.php` defines one session guard (`web`), one provider (`users`), and one password broker (`users`); there is no separate donor or guardian guard.
- Registration remains Breeze-shaped but custom-behaved:
  - `RegisteredUserController::store()` creates the user with `email_verified_at = null`
  - it does not dispatch `Registered`
  - it does not auto-login
  - it redirects to `login` with an admin-approval message
- Login is customized in `LoginRequest`:
  - credentials are checked manually against `users`
  - a null `email_verified_at` blocks login with an admin-approval error
  - successful login uses `Auth::login($user, $remember)`
- `AuthenticatedSessionController` still regenerates the session on login and logs out through the `web` guard.
- `composer.json` shows a Laravel 11 + Breeze stack on PHP 8.2, which is consistent with the single-session-auth architecture.

## Verification Inventory

- Verification and activation are currently coupled to the same field: `users.email_verified_at`.
- The standard Breeze verification routes still exist in `routes/auth.php`:
  - `verification.notice`
  - `verification.verify`
  - `verification.send`
- `VerifyEmailController` still marks the user as verified and emits the `Verified` event.
- The registration flow suppresses the `Registered` event, so the usual registration-time verification email is not automatically sent.
- The `verified` middleware now protects the management namespace, guardian namespace, donor namespace, `/dashboard`, and the payment routes.
- The profile UI still exposes resend-verification affordances, which means the repository keeps the Laravel verification scaffold even though login approval is effectively admin-gated.

## Role And Portal Inventory

- Role support is additive on the existing `users` table through `App\Models\Concerns\HasRoles`.
- `HasRoles` provides:
  - `roles()` many-to-many
  - `guardianProfile()` one-to-one
  - `donorProfile()` one-to-one
  - `hasRole()`, `hasAnyRole()`, `hasPermissionTo()`, and `assignRole()`
- `Role` exists as a first-class model with a many-to-many relationship to `Permission` and `User`.
- Route middleware aliases are registered in `bootstrap/app.php`:
  - `role`
  - `management.surface`
  - `portal.home`
- `EnsureUserHasRole` gates the dedicated `/management`, `/guardian`, and `/donor` route groups.
- `EnsureManagementSurfaceAccess` is a compatibility bridge:
  - management-role users are allowed
  - unroled users are still allowed into legacy management surfaces
  - guardian-only and donor-only users are blocked from those legacy surfaces
- `RedirectPortalUsersFromLegacyDashboard` rewrites `/dashboard` to portal homes for non-management role holders:
  - guardian users are redirected first
  - donor users are redirected second
  - management users stay on the legacy dashboard route

## Donor Inventory

- `App\Models\Donor` is now linked to `users` through `user_id` and guarded with `portal_enabled`, `isActived`, and `isDeleted`.
- The donor portal is read-only and lives under the dedicated `/donor` route group:
  - `donor.dashboard`
  - `donor.donations.index`
  - `donor.receipts.index`
- `DonorPortalData::requireDonor()` requires a linked donor profile that is enabled, active, and not deleted.
- Donation history is still derived from legacy `transactions` rows:
  - it filters on `doner_id`
  - it depends on the related `transactions_types.key = donation`
  - it sums `credit`, so donor history is still coupled to the older financial table
- Donor receipt visibility is read from explicit `receipts` records tied to the user, either via `issued_to_user_id` or `payment.user_id`.
- There are donor dashboard, donations, and receipts Blade pages plus a dedicated `donor-layout` component.
- The donor dashboard text explicitly says online donation initiation is still blocked.

## Guardian Inventory

- `App\Models\Guardian` is linked to `users`, carries `portal_enabled` and status flags, and links to students through a many-to-many pivot with relationship metadata.
- `GuardianPortalData::requireGuardian()` enforces the enabled/active/not-deleted guardian profile requirement.
- Guardian ownership is enforced in multiple layers:
  - role middleware on the route group
  - `GuardianPortalData` ownership scoping
  - `StudentPolicy`
  - `StudentFeeInvoicePolicy`
- Guardian routes currently provide:
  - `guardian.dashboard`
  - `guardian.students.show`
  - `guardian.invoices.index`
  - `guardian.invoices.show`
  - `guardian.history.index`
- `StudentFeeInvoice` is the guardian-facing billing model:
  - belongs to `student`
  - belongs to `guardian`
  - has many invoice items
  - has many morph payments
- The guardian invoice detail UI is the live payment-entry surface:
  - it can initiate sandbox shurjoPay for an invoice balance
  - it can submit manual-bank evidence
  - it shows active attempts, payment history, and issued receipts for that invoice

## Payment Inventory

- Payments are first-class records in `App\Models\Payment` with:
  - polymorphic `payable`
  - `user_id` and `reviewed_by_user_id`
  - `provider` and `provider_mode`
  - `idempotency_key` and `provider_reference`
  - `verification_status`
  - optional `posted_transaction_id`
  - linked `receipt` and `gatewayEvents`
- Current payment statuses include:
  - `pending`
  - `redirect_pending`
  - `pending_verification`
  - `awaiting_manual_payment`
  - `manual_review`
  - `paid`
  - `failed`
  - `cancelled`
- `StudentFeeInvoicePayableResolver` restricts payment initiation to management users or the guardian linked to the invoice and only for invoices with a positive `balance_amount`.
- `routes/payments.php` currently exposes:
  - guardian-only shurjoPay initiation
  - guardian-only manual-bank evidence submission
  - verified-user shurjoPay success/fail/cancel return routes
  - verified-user manual-bank detail route
  - CSRF-exempt shurjoPay IPN
  - management-only manual-bank review queue, approve, and reject actions
- `PaymentWorkflowService` is the central payment orchestrator. It handles:
  - shurjoPay initiation
  - browser return handling
  - IPN handling
  - manual-bank submission
  - manual-bank approval and rejection
  - server-side verification and payment finalization
  - optional legacy transaction posting through `posted_transaction_id`
- `config/payments.php` is the active payment config surface, not `config/services.php`.
  - provider mode defaults to `sandbox`
  - receipt prefixes are environment-driven
  - canonical student-fee posting is feature-flagged and off by default
  - shurjoPay credentials/URLs are env-backed
  - manual-bank display/instruction fields are env-backed
- `config/services.php` does not currently define payment provider keys or Google OAuth settings.
- The current write-capable payment flow is still limited to guardian-backed `StudentFeeInvoice` payables; donor portal payments are not implemented.

## Route And Middleware Inventory

- `routes/web.php` now mixes new portal namespaces with preserved legacy management routes.
- Dedicated namespaced route groups:
  - `/management` with `auth`, `verified`, `role:management`
  - `/guardian` with `auth`, `verified`, `role:guardian`
  - `/donor` with `auth`, `verified`, `role:donor`
- `/dashboard` remains the shared landing route name and is wrapped by `auth`, `verified`, `portal.home`, and `management.surface`.
- Most legacy CRUD, reporting, transaction, donor, lender, account, and settings routes remain in `routes/web.php` under `auth` plus `management.surface`.
- `routes/management.php` adds an additive management namespace:
  - `management.dashboard` redirects to `management.access-control`
  - `management.access-control` renders the foundation view
  - `management.reporting.index` renders the new additive reporting page
- `routes/auth.php` keeps the Breeze guest/auth separation for register, login, password reset, verification, password confirm, and logout.
- `routes/payments.php` is included at the root level without an extra prefix group; its individual routes own their own `/payments/...` paths and middleware.
- Legacy management donor routes under `/donors` remain separate from donor portal routes under `/donor`.

## UI Inventory

- Guardian and donor portals use dedicated layout components instead of the shared management layout:
  - `resources/views/components/guardian-layout.blade.php`
  - `resources/views/components/donor-layout.blade.php`
- Both layouts use full-screen dark treatments, portal-specific gradient accents, pill navigation, profile-settings links, and a dedicated logout action.
- Guardian portal views currently include:
  - dashboard
  - linked student detail
  - invoice list
  - invoice detail with payment actions
  - payment history
- Donor portal views currently include:
  - dashboard
  - donation list
  - receipt list
- Payment-specific views currently include:
  - `payments/shurjopay/status`
  - `payments/manual-bank/show`
- Management-specific additive views currently include:
  - `management/reporting`
  - `management/manual-bank-payments/index`
  - `portals/foundation`
- The portal UI direction is already more deliberate than the older management surfaces, but it still coexists beside the legacy shared navigation and Breeze auth/profile views.

## Top Coupling Risks

- `email_verified_at` is carrying both email-verification meaning and admin-approval meaning, while the Breeze verification routes and profile UI still exist.
- Portal access is not role-only; it also depends on linked profile records and status flags (`portal_enabled`, `isActived`, `isDeleted`) checked in services and policies.
- Multi-role landing behavior is order-sensitive: `/dashboard` redirects guardians before donors, so any user with both roles will currently land in the guardian portal first.
- Legacy management access is only partially role-hardened because `management.surface` still allows unroled users through for backward compatibility.
- Donor portal history still depends on legacy `transactions` semantics and the donation transaction-type key instead of a dedicated donor payable domain.
- Guardian online payments are tightly coupled to `StudentFeeInvoice`, `PaymentWorkflowService`, and the payment config flags; donor payments remain intentionally excluded.
- Payment return routes are broader than payment initiation routes (`auth` + `verified` rather than guardian-only), so correct access depends on downstream authorization and payable ownership checks.
- Payment configuration lives in `config/payments.php`, while `config/services.php` lacks Google OAuth and provider settings, which matters for later prompts that analyze external auth or provider setup.
