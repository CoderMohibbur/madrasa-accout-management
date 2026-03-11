# Report

This prompt analyzes the live route, middleware, and policy stack against the approved donor, guardian, and multi-role boundaries. No contradiction was found with prompt-12 through prompt-17. The live repository already has separate donor and guardian route prefixes plus guarded payment routes, but the current route edge still overuses Laravel `verified`, collapses guardian informational and protected access into one protected-only `/guardian` group, and routes `/dashboard` by raw role order instead of derived eligibility.

## Current Routing Conflicts

### Blanket `verified` conflicts

- `routes/web.php` wraps `/management`, `/guardian`, `/donor`, and `/dashboard` in `verified`.
- `routes/payments.php` wraps guardian payment initiation and payment status/show routes in `verified`.
- In the current repo, `email_verified_at` is also being used as an admin approval gate in `LoginRequest`, so `verified` is not acting as a narrow email-verification concern. It is blocking approved future behavior in several places:
  - donor login and donor portal eligibility must not remain keyed to raw `email_verified_at`
  - guardian informational access must not depend on universal email or phone verification
  - shared multi-role home must derive from eligible contexts, not from a verified-only subset
  - future guest-donation entry and narrow receipt/status access cannot live behind `auth` + `verified`

### Route-edge role conflicts

- `role:guardian` on the whole `/guardian` group is too strict for the approved target because it collapses:
  - guardian informational access
  - guardian protected portal access
  - protected invoice-payment entry
  into one protected-only route edge.
- `role:donor` on the whole `/donor` group is too coarse to remain the final donor portal gate because approved donor access depends on donor-domain eligibility, not raw role membership plus `verified`.
- `role:guardian` on payment initiation routes is also too coarse. Invoice payment is a protected guardian entitlement tied to linked invoice ownership, not a generic guardian-role permission.

### Shared-home conflicts

- `/dashboard` currently uses `portal.home` plus `management.surface`.
- `RedirectPortalUsersFromLegacyDashboard` redirects guardians before donors based on raw role order.
- `EnsureManagementSurfaceAccess` forbids donor/guardian portal users from entering the legacy dashboard body at all.
- Auth controllers and verification controllers all redirect back to the `dashboard` route name, so the current guardian-first redirect assumption leaks into:
  - login success
  - email verification prompt
  - email verification completion
  - password confirmation
- This means the current `dashboard` route name is a compatibility anchor, but its current middleware behavior is not the approved final model.

### Payment-route conflicts

- Payment initiation routes are currently guardian-role-only and verified-only, which is narrower than the future model and ties route entry to role instead of protected payable eligibility.
- Payment status/show routes are currently `auth` + `verified` only, then rely on controller/service authorization. That is safer than raw role gating, but still too narrow for future guest/donor status access and still assumes authenticated verified users only.
- Payment status views are guardian-skinned and link back to guardian routes, which is correct for the current guardian-only implementation but would be incompatible with later donor or guest payment-status surfaces if reused as-is.

### Missing route surfaces

- There is no dedicated guest-donation route group yet.
- There is no dedicated guardian informational route group yet.
- Public admission information is not expressed as a first-class route boundary; the welcome page contains a hard-coded external admission URL instead of a canonical route/config path.

## Target Route Structure

The safest additive-first target is to add new route spaces instead of repurposing live protected routes early.

### Public Info

- Public routes remain outside donor and guardian portal groups.
- Safe public route buckets:
  - `/` for public landing
  - additive public admission information route(s)
  - additive public donation entry route(s)
- Public admission surfaces remain informational only and hand off externally.
- Public donation entry remains separate from donor portal access and must not create account ownership automatically.

### Auth

- Keep existing auth route names and base paths:
  - `login`
  - `register`
  - `password.*`
  - `verification.*`
- Keep `dashboard` as the post-auth compatibility landing route name because existing controllers and shared navigation depend on it.
- Later auth-state foundations should let portal routes use domain-specific eligibility middleware instead of inheriting blanket `verified`.

### Guest Donation Entry And Checkout

- Add a dedicated public-or-auth-optional donation route space that is separate from `/donor` and separate from guardian payment routes.
- Safe additive structure:
  - public donation entry
  - guest or identified payment-only checkout
  - narrow donation payment/receipt/status lookup surface
  - provider return/webhook callbacks
- Guest donation routes must not reuse donor portal middleware and must not assume registration, donor-role assignment, or portal eligibility.

### Donor Portal

- Keep `/donor` as the donor portal route prefix.
- Treat donor portal routes as donor-domain-only and read-path-oriented.
- Final donor portal route edge should be:
  - `auth`
  - later shared account-state or approval middleware
  - dedicated donor-portal eligibility middleware
- Raw `role:donor` may remain a coarse hint during transition, but it should not be the final donor portal gate once dedicated eligibility middleware exists.

### Guardian Informational Portal

- Add a separate additive guardian informational route space rather than taking over live `/guardian` routes immediately.
- Safest additive structure:
  - authenticated guardian informational prefix distinct from the current protected `/guardian` prefix
  - non-sensitive institution/admission/help/status pages only
- This avoids reopening the live protected `/guardian` pages before prompts 29-32 and prompts 36-37 establish the approved foundations.

### Guardian Protected Portal

- Keep current live `/guardian` routes as the protected guardian route space during the additive rollout.
- Final protected guardian route edge should become:
  - `auth`
  - later shared account-state or approval middleware
  - dedicated guardian-protected eligibility middleware
  - object-level policies for students, invoices, receipts, and payments
- Guardian protected routes must stay separate from informational routes even if both belong to the guardian domain.

### Multi-Role Home

- Keep the `dashboard` route name as the shared authenticated landing route.
- Target behavior for that route:
  - management-compatible behavior preserved
  - one eligible portal context -> direct redirect
  - multiple eligible contexts -> neutral chooser
  - no eligible portal contexts -> non-portal fallback
- The chooser must show only context availability and non-sensitive gating/status messaging.
- It must not show donor totals, linked students, invoice balances, payment history, or any mixed-scope records.

### Payment Routes

- Keep provider webhook routes public and CSRF-exempt where required.
- Separate future payment route concerns into:
  - initiation routes tied to a protected payable context
  - browser return/status routes tied to narrow payment-access rules
  - management review routes under management-only access
- Guardian payment initiation should remain grouped with protected guardian behavior until any later approved donor payable surface introduces its own dedicated payment entry path.

## Required Middleware Changes

### Keep

- Keep `role:management` for legacy management routes unless later auth-state foundations require a safe internal replacement.
- Keep `management.surface` for legacy management pages, not for the final shared-home chooser.

### Replace Or Narrow

- Remove blanket `verified` from these future target surfaces once prompts 29-32 land:
  - donor portal
  - guardian informational portal
  - shared multi-role home
  - guest donation entry and narrow status access
- Do not remove `verified` early without a replacement account-state or approval middleware, because current login behavior still overloads `email_verified_at`.

### Add

- Add a shared base middleware for account-state or approval eligibility after prompts 29-32 separate approval from email verification.
- Add dedicated context middleware:
  - donor portal eligibility
  - guardian informational eligibility
  - guardian protected eligibility
  - shared-home eligible-context resolver
- Add dedicated payment access middleware or equivalent controller-level authorization for:
  - payment initiation against protected guardian invoices
  - payment status/detail viewing
  - any future donor or guest narrow payment-status access
- Limit raw role middleware to coarse legacy or management boundaries rather than final portal authorization.

### Shared-Home Middleware Adjustment

- `portal.home` must stop using raw guardian-first role ordering.
- `management.surface` must stop being part of the final `dashboard` chooser path because it blocks donor/guardian users from the neutral home body entirely.

## Required Policy Changes

- `StudentPolicy` should remain a guardian protected-data policy only. Do not broaden it for informational guardian access.
- `StudentFeeInvoicePolicy` should remain a protected invoice ownership policy and must keep:
  - guardian linkage
  - `guardian_id` compatibility
  - management override behavior
- `ReceiptPolicy` must remain domain-aware:
  - donor/user-bound receipts stay donor-domain readable only when user ownership matches
  - guardian receipt visibility remains invoice-derived
- Payment access should move toward explicit policy-style ownership checks instead of scattered inline assumptions:
  - payment initiation must remain payable-owner-controlled
  - payment detail/status viewing should have one reusable authorization rule for management, payer user, or linked protected guardian owner
- No policy broadening is needed for public info, guest donation entry, or guardian informational routes because those should be route/middleware separated before object policies are even reached.

## Compatibility Warnings

- Preserve the `dashboard` route name. It is used by:
  - shared navigation
  - login redirect
  - verify-email redirect flow
  - password confirmation redirect flow
- Preserve current management route names and grouping. Shared navigation and existing management pages depend on them.
- Do not rename current `guardian.*`, `donor.*`, `payments.*`, or `management.*` route names during the analysis-to-implementation transition unless a later phase explicitly approves a compatibility shim.
- Keep current live `/guardian` protected routes intact during additive rollout. Reusing that prefix for informational access too early would collide with the approved guardian boundary and the current live payment/invoice links.
- Keep payment views context-correct. Current guardian payment status/detail views should not be reused blindly for donor or guest status routes later.

## Migration Risks

- Removing `verified` before prompts 29-32 separate approval and verification semantics could weaken the current admin-approval gate or lock users into inconsistent portal access states.
- Leaving `verified` in place too long on donor portal, guardian informational, and shared-home routes would block approved target behavior even after the schema/read-path foundations land.
- If guardian informational routes are introduced under the live `/guardian` prefix too early, protected student/invoice/payment links may become ambiguous or regress.
- If multi-role home work reuses the current guardian-first redirect logic, prompt-39 will not be able to produce the approved neutral chooser behavior for multi-eligible users.
- If payment access continues to be split between coarse route middleware and scattered inline checks, later donor or guest payment-status additions may inherit the wrong guard assumptions.
- If public admission and donation surfaces continue to rely on hard-coded or inconsistent placements, prompt-19 and later UI phases will have to correct duplicated route/view behavior instead of simply wiring one canonical configuration.

## Completion Status

- No contradiction with prompt-12 through prompt-17 was found.
- No correction pass was required.
- Prompt-18 is complete.
- No hard blocker prevents prompt-19.
