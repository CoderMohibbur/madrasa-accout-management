# Report

## Current Boundary Findings

- Current role checks start at the `User` model through `HasRoles`, which only expresses role membership (`hasRole`, `hasAnyRole`, `assignRole`) and one-to-one donor/guardian profile relations.
- Route entry is currently enforced by stacked gates, not by one clean boundary:
  - `auth`
  - `verified`
  - `role:guardian` or `role:donor`
  - profile existence plus profile flags inside portal services
  - linkage/ownership policies for guardian protected resources
- Donor role alone does not grant donor portal access. `DonorPortalData::requireDonor()` additionally requires:
  - a linked donor profile
  - `portal_enabled = true`
  - `isActived = true`
  - `isDeleted = false`
- Guardian role alone does not grant protected guardian access. `GuardianPortalData::requireGuardian()` additionally requires:
  - a linked guardian profile
  - `portal_enabled = true`
  - `isActived = true`
  - `isDeleted = false`
  Protected resources then add student/invoice ownership checks on top.
- Guardian linkage is enforced indirectly in three places:
  - `guardian->students()` pivot membership for student access
  - invoice scoping in `GuardianPortalData::applyInvoiceOwnershipScope()`
  - object policies (`StudentPolicy`, `StudentFeeInvoicePolicy`) and the invoice payable resolver
- Receipt access is mixed-domain:
  - donor receipts are user-bound through `issued_to_user_id` or `payment.user_id`
  - guardian-sensitive receipts fall back through `ReceiptPolicy` into invoice ownership checks
- `/dashboard` is not a neutral shared home today. `RedirectPortalUsersFromLegacyDashboard` sends guardians first, then donors, which means multi-role routing is currently role-order-driven rather than eligibility-driven.
- The current repository is narrower than the codex target boundary model:
  - no separate guardian informational portal exists yet
  - donor portal is read-only
  - guest donation is not a portal boundary yet
  - unverified-access redesign is not implemented because `verified` middleware and overloaded `email_verified_at` still sit in the access chain
- The `run_state.json` trailing-commit issue is doc-state drift only. The live code and saved codex outputs remain consistent enough for prompt-05 analysis.

## Final State Distinctions

- Holding a role:
  - means the account is assigned to a potential access domain such as donor or guardian
  - does not by itself prove profile existence, portal eligibility, activity, or data ownership

- Having a donor or guardian profile:
  - means the domain record exists and can carry lifecycle flags and domain metadata
  - still does not by itself grant portal access or protected data access

- Having portal eligibility:
  - is a derived authorization state produced from account state, role membership, profile lifecycle, verification/approval policy, and domain-specific linkage rules
  - must be treated separately for donor portal, guardian informational portal, guardian protected portal, and shared multi-role home

- Being active:
  - means the relevant account/profile lifecycle state currently permits use of that surface
  - in the live repo this is approximated only through profile flags (`isActived`, `portal_enabled`)
  - in the target model it must remain distinct from role, profile existence, and linkage

- Being deleted:
  - means the relevant identity or domain record is no longer eligible for surface access
  - in the live repo this is approximated through profile-level `isDeleted`
  - in the target model deletion must override portal eligibility consistently

- Being linked to student-owned data:
  - is a guardian-only protected-data concept
  - requires explicit guardian-student linkage and, where applicable, invoice ownership compatibility
  - is not implied by holding the guardian role or merely having a guardian profile

## Donor Boundary Rules

- Final donor portal boundary:
  - requires an authenticated account
  - requires donor-domain eligibility, not just a donor role
  - requires a donor profile that is present, active, and not deleted
  - must not be keyed directly to raw `email_verified_at` or the current `verified` middleware once later auth redesign separates verification from eligibility
  - must remain isolated from guardian-linked student, invoice, receipt, and payment-sensitive data
- Donor portal access must be based on donor-owned data only:
  - donor profile record
  - donor-owned donation history
  - donor-visible receipts bound to that donor/account
- Donor portal boundary must not assume:
  - student linkage
  - guardian invoice ownership
  - management-surface access
  - guest-donation visibility by default
- Guest donors, anonymous-display donors, and lightweight donor identities remain outside donor portal access unless and until they become an explicitly eligible registered donor identity.
- Because the current repo is narrower than the target rule set, donor portal boundary analysis must treat donor write flows, guest donation entry, and unverified donor access redesign as planned deltas rather than current behavior.

## Guardian Informational Boundary Rules

- Final guardian informational portal is a distinct surface and must not be collapsed into the guardian protected portal.
- Minimum boundary for guardian informational access:
  - authenticated account
  - guardian-domain intent or guardian-domain presence
  - active and not deleted at the applicable account/profile layer
  - no requirement for completed student linkage
  - no requirement for completed email or phone verification
- Guardian informational access must not require:
  - student linkage
  - invoice ownership
  - payment eligibility
  - receipt entitlement
- Guardian informational access may include only:
  - non-sensitive institution information
  - admission-related information
  - a link/button to the external admission application
  - status/help messaging about linkage or verification state
- Guardian informational access must never expose:
  - linked students
  - invoices
  - payment history
  - receipts
  - management surfaces
- The current codebase does not implement this separate boundary yet; all existing `/guardian` routes are protected-portal routes.

## Guardian Protected Boundary Rules

- Final guardian protected portal boundary requires all of the following:
  - authenticated account
  - guardian-domain eligibility
  - guardian profile present, active, and not deleted
  - explicit guardian linkage or ownership authorization for the requested student-owned resource
  - derived protected eligibility that is stricter than informational access and not reducible to raw `verified` middleware alone
- Guardian protected access must be enforced at both levels:
  - surface-level eligibility before entering the protected portal
  - object-level ownership checks for students, invoices, receipts, and payments
- Linkage rules for protected guardian access:
  - a student page requires guardian-student linkage
  - an invoice page requires guardian linkage to the student plus compatibility with `guardian_id` when one is set
  - a receipt or payment view requires either invoice-derived guardian ownership or other explicitly approved guardian entitlement
- Payment initiation remains a protected guardian boundary, not a generic guardian or donor boundary:
  - only authorized guardian-linked invoice payables qualify under the current safe model
  - donor or guest payment flows must not borrow this guardian invoice boundary as a shortcut
- Management overrides in current policies are administrative authority, not proof that a user belongs inside the guardian protected portal as a guardian.

## Multi-Role Boundary Rules

- A single account may legitimately hold donor and guardian roles at the same time, but each portal surface must remain separately derived and separately scoped.
- Shared or multi-role home must be driven by eligible contexts, not by raw role order alone.
- Final multi-role home rules:
  - if only one portal context is eligible, direct to that one
  - if multiple contexts are eligible, show a neutral switching surface or equivalent explicit context choice
  - do not mix donor and guardian data on the neutral home
  - do not let donor eligibility imply guardian protected eligibility, or vice versa
- Multi-role examples that must remain valid:
  - donor portal eligible + guardian informational only
  - donor portal eligible + guardian protected portal eligible
  - management or legacy-compatible access existing alongside donor/guardian contexts without collapsing their data boundaries
- The current guardian-first `/dashboard` redirect is a temporary implementation detail, not the final boundary rule for multi-role home behavior.
