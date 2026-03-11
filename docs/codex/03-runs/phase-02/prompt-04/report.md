# Report

## Current State-Model Problems

- `users.email_verified_at` is overloaded. It currently acts as:
  - the Laravel email-verification field
  - the login approval gate in `LoginRequest`
  - the registration-time “inactive until approved” marker in `RegisteredUserController`
- Route-level `verified` middleware currently stands in for several different concerns that should be separate:
  - email verification
  - basic account activation
  - donor/guardian portal eligibility
  - payment-entry eligibility
- Role assignment is separate from profile existence, but access still depends on a loose combination of:
  - `role:*` middleware
  - guardian or donor profile existence
  - `portal_enabled`
  - `isActived`
  - `isDeleted`
  - guardian linkage or invoice ownership
- Donor and guardian profiles each carry lifecycle-like flags (`portal_enabled`, `isActived`, `isDeleted`), but the base user account has no parallel explicit lifecycle or approval state.
- Guardian linkage is a real state in practice, but it is only implied today by:
  - the guardian-student pivot
  - invoice guardian ownership
  - policy/service query scoping
  There is no explicit linkage status such as unlinked, pending, linked, or revoked.
- Portal eligibility is derived inconsistently:
  - route middleware assumes role membership
  - portal services assume profile flags
  - policies assume guardian linkage
  - `/dashboard` redirect assumes role order
- Legacy management compatibility is also a hidden state: `management.surface` lets unroled users into legacy management pages, which means “management eligibility” is not modeled purely as a role.

## Target State Model

Treat account state as a set of orthogonal axes, not one overloaded field.

### 1. Identity Existence

- `no_identity`
- `registered_identity`

This axis answers only whether a base authenticated account exists. Deletion is handled separately by the deletion-state axis.

### 2. Email Verification State

- `email_not_provided`
- `email_unverified`
- `email_verified`

This axis must no longer double as approval or portal activation.

### 3. Phone Verification State

- `phone_not_provided`
- `phone_unverified`
- `phone_verified`

This is independent of email verification and should coexist with it.

### 4. Admin Approval State

- `approval_not_required`
- `approval_pending`
- `approval_approved`
- `approval_suspended`

This axis is separate from both verification channels. It may apply differently by surface, but it cannot keep sharing `email_verified_at`.

### 5. Role Assignment State

Role assignment is a set, not a scalar:
- `management`
- `guardian`
- `donor`
- other internal roles as needed later

Role presence alone must not imply portal eligibility.

### 6. Donor Profile State

- `donor_profile_absent`
- `donor_profile_present_inactive`
- `donor_profile_present_active`
- `donor_profile_soft_deleted`

This captures whether a donor-facing identity exists apart from the base user.

### 7. Guardian Profile State

- `guardian_profile_absent`
- `guardian_profile_present_unlinked`
- `guardian_profile_present_link_pending`
- `guardian_profile_present_linked`
- `guardian_profile_present_inactive`
- `guardian_profile_soft_deleted`

Guardian linkage is part of guardian profile state because it directly changes protected access eligibility.

### 8. Portal Eligibility State

Portal eligibility should be derived from the other axes, not reused as a proxy for them:
- `public_only`
- `authenticated_no_portal`
- `donor_portal_eligible`
- `guardian_info_portal_eligible`
- `guardian_protected_portal_eligible`
- `multi_role_portal_eligible`
- `management_surface_eligible`
- `disabled`

### 9. Guardian Linkage / Authorization State

For protected guardian surfaces, keep an explicit derived linkage state:
- `not_applicable`
- `unlinked`
- `pending_review`
- `linked`
- `revoked`

### 10. Account Activity State

- `active`
- `inactive`
- `suspended`

This is separate from approval, role, and deletion state.

### 11. Deletion State

- `not_deleted`
- `soft_deleted`

Soft deletion must override portal eligibility and protected access regardless of other flags.

## State Transition Rules

- `no_identity -> registered_identity`
  - allowed through self-registration or a future social-sign-in bootstrap
- `registered_identity -> email_verified`
  - allowed independently of phone verification
- `registered_identity -> phone_verified`
  - allowed independently of email verification
- `registered_identity -> approval_pending/approved/suspended`
  - admin approval transitions must not overwrite verification data
- `registered_identity + donor role assigned`
  - may occur before or after donor profile creation
- `registered_identity + donor profile present_active`
  - can make the user donor-portal-eligible if activity and deletion states permit
- `registered_identity + guardian role assigned + guardian profile present_unlinked`
  - makes the user eligible only for guardian informational access, not protected student/invoice/payment access
- `guardian_profile_present_unlinked -> guardian_profile_present_link_pending -> guardian_profile_present_linked`
  - linkage is an explicit progression, not a side effect of role assignment
- `guardian_profile_present_linked -> guardian_protected_portal_eligible`
  - only when the account is active, not deleted, and otherwise allowed by approval/lifecycle policy
- `guest donation`
  - may remain outside registered identity entirely
  - may later attach to a donor identity only through safe matching rules
- `active -> suspended` or `not_deleted -> soft_deleted`
  - immediately disables portal eligibility regardless of verification or role state
- `multi-role_portal_eligible`
  - is a derived state when donor and guardian protected/info states are both legitimately available
  - must expose context switching without cross-scope access bleed
- `management_surface_eligible`
  - should eventually derive from explicit management eligibility rules rather than hidden legacy compatibility behavior

## Breaking Assumptions

- `LoginRequest` assumes `email_verified_at` is the single source of truth for whether login is allowed.
- `RegisteredUserController` assumes setting `email_verified_at = null` is a safe stand-in for pending approval or inactive state.
- `VerifyEmailController` assumes verifying email can safely mutate the same field used by login approval.
- All route groups using `verified` middleware assume email verification is the right gate for donor, guardian, dashboard, and payment surfaces.
- `RedirectPortalUsersFromLegacyDashboard` assumes role precedence is enough to choose the correct home surface.
- `EnsureUserHasRole` assumes role assignment alone is the main portal gate.
- `EnsureManagementSurfaceAccess` assumes “not guardian and not donor” is a valid proxy for legacy management access.
- `GuardianPortalData`, `DonorPortalData`, `StudentPolicy`, and `StudentFeeInvoicePolicy` assume profile flags plus current linkage checks are sufficient, even though they do not model approval or info-only guardian states explicitly.
- The current donor portal assumes donor identity is a registered user-linked profile; it has no state for guest donor identity without portal access.
- The current payment flow assumes guardian-linked invoice ownership and a verified session, which conflicts with the broader frozen rules for guest donation and unverified access flexibility.

## Mandatory Separation Rules Before Implementation

- Separate admin approval from email verification immediately at the model/state level. They cannot continue sharing `email_verified_at`.
- Separate portal eligibility from raw role membership. Roles assign potential access domains; they do not alone grant portal entry.
- Separate guardian informational access from guardian protected access. “Guardian exists” and “guardian linked to student-sensitive data” are not the same state.
- Separate guest donor identity from registered donor identity. Guest donation must be first-class without implying a user account.
- Separate account lifecycle (`active`, `inactive`, `suspended`) from profile lifecycle (`portal_enabled`, `isActived`, `isDeleted`) so account-wide shutdown does not depend on profile-specific flags alone.
- Make guardian linkage an explicit state transition rather than an inferred side effect of pivot rows or invoice ownership.
- Make multi-role home behavior derive from portal eligibility plus explicit context switching, not hard-coded role ordering alone.
- Make deletion override every access state consistently.
