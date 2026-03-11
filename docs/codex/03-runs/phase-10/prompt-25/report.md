# Report

This prompt converts the approved prompt-04 through prompt-24 analysis into a rollout-gating test strategy. No contradiction was found with the approved prompt-23 schema plan or prompt-24 migration/backfill posture. The matrix is intentionally phase-aware: early implementation prompts need regression checks that keep current legacy behavior stable while new schema lands dark, and later prompts need acceptance tests that prove the final approved behavior has replaced those temporary boundaries safely.

The matrix carries forward three cross-cutting rules from the prior analysis:

- classify first, mutate later
- fail closed on ambiguous identity, linkage, and ownership rows
- preserve route-name, legacy-field, and legacy-transaction compatibility until the specific cutover prompt says otherwise

## Full Test Matrix

### Auth

- `AUTH-01` Feature: before account-state cutover, legacy approved users can still log in while pending rows remain blocked; after cutover, `approval_status` and `account_status` replace raw `email_verified_at` gating without widening access.
- `AUTH-02` Feature: inactive, suspended, deleted, or approval-pending accounts fail closed for shared home, donor, guardian informational, and guardian protected entry points.
- `AUTH-03` Feature: rate limiting, remember-me/session persistence, logout, password confirmation, password reset, and profile self-service reflect the approved approval model rather than stale Breeze defaults.
- `AUTH-04` Feature: `dashboard` route name remains stable, management users bypass portal redirection, and ambiguous role/profile rows do not gain donor or guardian landings before eligibility logic is ready.

### Registration

- `REG-01` Feature: public registration creates one base `users` identity and no donor or guardian protected entitlement.
- `REG-02` Feature: donor-intent and guardian-intent entry variants attach intent to the same account model, never create second identities, and never auto-link old donor or guardian rows from loose name, email, or phone similarity.
- `REG-03` Feature: registration landing behavior stays phase-correct; legacy no-auto-login behavior remains intact until the approved open-registration cutover, then the new flow lands only on a neutral or shared state and never directly on donor or guardian protected surfaces.
- `REG-04` Feature: duplicate normalized email is rejected, and guest donation contact data does not reserve, claim, or backfill an account automatically.

### Email Verification

- `EMAIL-01` Feature: email verification changes only email trust state and never by itself grants approval, donor portal access, guardian informational access, guardian protected access, or multi-role eligibility.
- `EMAIL-02` Migration/feature: first backfill uses existing `email_verified_at` only to seed legacy approval compatibility, leaves the field untouched, and does not reinterpret every non-null value as modern high-assurance proof.
- `EMAIL-03` Feature: email-change, resend, and verification-notice flows preserve the approved separation between login approval and verified-contact state.
- `EMAIL-04` Regression: verified email remains unique at the account layer and is the only safe auto-link input for first-time Google linking when conflict-free.

### Phone Verification

- `PHONE-01` Feature: phone verification remains optional and separate from email verification, approval, donor eligibility, and guardian linkage.
- `PHONE-02` Data/feature: one normalized verified phone belongs to one active account-level owner by default; conflicts fail closed or go to review rather than silently reassigning ownership.
- `PHONE-03` Migration/feature: donor or guardian profile `mobile` values and guest donation phone capture never auto-populate `users.phone` unless the source is explicit and unambiguous.
- `PHONE-04` Regression: lack of phone verification never blocks guest donation, donor payment, or guardian informational access in the minimal safe rollout.

### Guest Donation

- `GUEST-01` Feature: guest donors can start checkout without registration; only amount is required and optional identity or contact fields remain unverified snapshots.
- `GUEST-02` Integration: guest checkout follows `donation_intent -> payments -> donation_record -> receipt`; browser return pages are informational only and do not settle the donation on their own.
- `GUEST-03` Security: opaque public references expose only narrow transaction-specific status or receipt access and never expand into donor portal history.
- `GUEST-04` Regression: guest contact similarity does not create `users`, donor profiles, verification, or self-service claims without a later proof-based claim flow.

### Donor Portal

- `DONOR-01` Feature: donor payment ability is tested separately from donor portal eligibility; non-eligible identified donors can pay and see narrow receipt or status views without being treated as portal users.
- `DONOR-02` Feature: portal-eligible donors can view only their own donation history and receipt history, with anonymous-display preference preserved without losing internal traceability.
- `DONOR-03` Regression: payment completion never auto-grants donor role, donor profile linkage, or donor portal eligibility.
- `DONOR-04` Data regression: legacy donor `transactions` stay out of the new settled-donation history until a later explicit bridge is approved and tested.

### Guardian Informational Portal

- `GINFO-01` Feature: authenticated guardian-intent or unlinked guardian users can access institutional information, curated admission content, external handoff, and self-only linkage or help messaging.
- `GINFO-02` Security: informational surfaces never expose students, invoices, receipts, payment history, or payment-entry controls.
- `GINFO-03` Route/content smoke: external admission CTA placement is limited to approved public and guardian-informational screens and never appears on auth pages or the protected guardian portal.

### Guardian Protected Portal

- `GPROT-01` Feature/policy: linked eligible guardians can view only student records that are linked to their own guardian profile.
- `GPROT-02` Feature/policy: invoice, payment, and receipt access requires both guardian eligibility and object ownership; `guardian_student` and `student_fee_invoices.guardian_id` mismatches fail closed or land in `pending_review`.
- `GPROT-03` Security: unlinked, pending-review, inactive, deleted, or role-only guardians cannot access protected guardian routes even if they can log in or have informational access.
- `GPROT-04` Regression: multi-guardian families stay object-scoped; one guardian's invoice or receipt visibility never broadens another guardian's protected scope.

### Multi-Role Behavior

- `MULTI-01` Feature: one eligible context redirects directly, multiple eligible contexts show a neutral chooser, and zero eligible contexts show a safe fallback with no mixed donor-plus-guardian data.
- `MULTI-02` Feature: explicit context switching preserves deep-link authorization and never mixes donor-owned records with guardian-owned records on one surface.
- `MULTI-03` Regression: adding donor role or context never grants guardian linkage or protected access, and guardian intent never grants donor portal history.
- `MULTI-04` Cutover regression: final shared-home behavior is eligibility-driven rather than raw role order; until that cutover ships, ambiguous role or profile combinations must fail closed rather than guessing a destination.

### Payment Flow

- `PAY-01` Integration: payable resolution and ownership validation happen server-side; tampered, expired, cancelled, or unauthorized payment attempts fail without creating settled records or receipts.
- `PAY-02` Integration: retry creates a new payment attempt under the same still-open intent or invoice flow, while failed or ambiguous attempts remain non-settled.
- `PAY-03` Security: receipt access is narrower than portal membership for both donor and guardian cases, and public receipt or status lookup never leaks cross-user data.
- `PAY-04` Regression: donor settlement or posting remains separated from legacy donor `transactions` and from guardian fee-payment posting semantics until later explicit integration is approved.

### Google Sign-In

- `GOOG-01` Feature: Google sign-in uses the same shared account model for first-time onboarding, repeat sign-in, and authenticated linking.
- `GOOG-02` Security: verified normalized `users.email` is the only safe first-time auto-link input; provider subject conflicts, duplicate-email conflicts, and contact-field-only matches fail closed.
- `GOOG-03` Boundary: provider-asserted verification marks email only and never implies phone verification, approval, donor portal eligibility, or guardian linkage.
- `GOOG-04` Regression: Google onboarding never auto-creates protected guardian access in the minimal first rollout.

### Route And Middleware Behavior

- `ROUTE-01` Regression: existing route names `dashboard`, `guardian.*`, `donor.*`, `payments.*`, and `management.*` remain stable.
- `ROUTE-02` Feature: public info, guest donation, donor, guardian informational, guardian protected, and management route buckets enforce the approved middleware boundary for that rollout stage.
- `ROUTE-03` Cutover regression: blanket `verified` dependence is removed only when explicit account-state or eligibility middleware is ready; interim releases do not broaden access during the transition.
- `ROUTE-04` Regression: shared navigation, management-surface protection, and additive portal route registration keep functioning together.

### Policy / Authorization Boundaries

- `POL-01` Unit/policy: `StudentPolicy` stays protected-only and returns true only for management or a linked eligible guardian.
- `POL-02` Unit/policy: `StudentFeeInvoicePolicy` denies inactive, deleted, or disabled guardians and denies pivot or invoice ownership mismatches.
- `POL-03` Unit/policy: receipt, payment initiation, payment detail, and status authorization are domain-aware and narrower than raw route membership.
- `POL-04` Regression: role-only users and ambiguous linkage rows cannot pass object policies without explicit authorized profile linkage.

### UI Consistency Smoke Checks

- `UI-01` Smoke: public, auth, donor, guardian, and multi-role surfaces all render on the shared shells, headers, alerts, cards, and action patterns from prompts 20 and 21.
- `UI-02` Smoke: no protected guardian or auth view becomes an admission CTA destination; approved external-handoff blocks appear only where prompt-19 and prompt-14 allow them.
- `UI-03` Responsive smoke: list, detail, payment, and receipt views retain mobile card fallback, empty or loading or no-access states, and consistent status or outcome patterns.

### Legacy-Data Classification And Migration Safety

- `MIG-01` Migration: additive schema rollout must produce read-only classification output before any mutating backfill step.
- `MIG-02` Migration: first backfill writes only approved new columns and leaves `email_verified_at`, donor or guardian profile flags, pivot rows, invoice ownership, and legacy donor transactions untouched.
- `MIG-03` Migration/data: ambiguous user, profile, linkage, and phone cases go to explicit review buckets and never auto-link or auto-guess identity or ownership.
- `MIG-04` Migration/idempotency: classification and backfill steps are rerunnable without duplicate role grants, phone assignments, donor-domain records, or guardian linkage changes.

## High-Risk Regression List

- `HR-01` `AUTH-01` plus `MIG-02`: account-state cutover accidentally locks approved legacy users out or admits pending legacy users too early.
- `HR-02` `MIG-03` plus `REG-02` plus `GOOG-02`: loose donor, guardian, or guest contact matching creates incorrect user linkage or portal ownership.
- `HR-03` `GPROT-02` plus `POL-02`: guardian protected access is inferred from only pivot rows or only invoice ownership and leaks student or invoice data.
- `HR-04` `DONOR-04` plus `PAY-04`: legacy donor transactions are shown as settled new donor records, causing double counting or false receipt history.
- `HR-05` `GUEST-04` plus `DONOR-03`: guest or identified payment completion auto-creates account, donor role, or portal eligibility.
- `HR-06` `MULTI-04` plus `ROUTE-03`: raw role-order redirects or partial middleware cutover misroute multi-role users and bypass eligibility checks.
- `HR-07` `GOOG-03` plus `GPROT-03`: Google-verified email is misread as guardian linkage or protected-access proof.
- `HR-08` `PAY-01` plus `PAY-02` plus `POL-03`: tampered, duplicate, or cancelled payment attempts create settled records or leaked receipt access.
- `HR-09` `PHONE-02` plus `PHONE-03`: conflicting phone data silently overwrites `users.phone` or grants duplicate verified-phone ownership.
- `HR-10` `ROUTE-01` plus `UI-01`: route-name or shared-shell regressions break navigation and cross-surface consistency.

## Rollout Blockers

- `RB-01` `MIG-01` through `MIG-04`: no schema or read-path rollout until classification-first, conservative, idempotent backfill behavior is proven.
- `RB-02` `AUTH-01` plus `AUTH-02` plus `ROUTE-03`: no account-state or auth cutover until pending, approved, suspended, and deleted users all route correctly without blanket `verified` regression.
- `RB-03` `REG-02` plus `MIG-03` plus `GOOG-02`: no new onboarding or identity-linking rollout until ambiguous donor, guardian, and guest records fail closed.
- `RB-04` `GUEST-02` plus `PAY-01` plus `PAY-02`: no donor payment rollout until only authoritative settlement creates records or receipts and retries stay idempotent.
- `RB-05` `DONOR-01` plus `DONOR-04` plus `PAY-04`: no donor portal rollout until narrow receipt access, portal eligibility, and legacy-transaction separation are all correct.
- `RB-06` `GINFO-02` plus `GPROT-01` plus `GPROT-02` plus `POL-02`: no guardian rollout until informational and protected boundaries are independently proven.
- `RB-07` `MULTI-01` plus `MULTI-02` plus `MULTI-04`: no multi-role rollout until chooser or switching behavior prevents scope blending and raw role-order misrouting.
- `RB-08` `GOOG-01` through `GOOG-04`: no Google rollout until linking, conflict handling, and verification boundaries fail closed.
- `RB-09` `ROUTE-01` plus `ROUTE-04` plus `UI-01`: no release that touches routing or navigation until route-name preservation and shared-shell smoke checks pass.
- `RB-10` `POL-01` through `POL-04`: no protected-data rollout until object policies reject role-only and ambiguous-linkage access.

## Minimum Test Pack Per Phase

### Phase A - Shared Identity And State Foundation (`prompt-28` through `prompt-32`)

- Required pack: `AUTH-01` through `AUTH-04`, `REG-01` through `REG-04`, `EMAIL-01` through `EMAIL-04`, `PHONE-01` through `PHONE-04`, `ROUTE-01` through `ROUTE-04`, `MIG-01` through `MIG-04`
- Gate purpose: prove dark schema, conservative backfill, and auth or registration cutover can ship without broadening access or breaking legacy compatibility.

### Phase B - Donation Entry And Donor Rollout (`prompt-33` through `prompt-35`)

- Required pack: Phase A pack plus `GUEST-01` through `GUEST-04`, `PAY-01` through `PAY-04`, `DONOR-01` through `DONOR-04`
- Gate purpose: prove guest or identified donation, settlement, and donor portal history stay isolated and legacy donor transactions remain untouched.

### Phase C - Guardian Rollout (`prompt-36` through `prompt-37`)

- Required pack: Phase B pack plus `GINFO-01` through `GINFO-03`, `GPROT-01` through `GPROT-04`, `POL-01` through `POL-04`
- Gate purpose: prove informational and protected guardian surfaces stay separate and object authorization remains fail-closed.

### Phase D - Google, Multi-Role, And Route Finalization (`prompt-38` through `prompt-40`)

- Required pack: Phase C pack plus `GOOG-01` through `GOOG-04`, `MULTI-01` through `MULTI-04`, rerun `ROUTE-02` through `ROUTE-04`, rerun `POL-03` through `POL-04`
- Gate purpose: prove alternate auth, chooser or switching behavior, and eligibility-driven route behavior work together without scope blending.

### Phase E - Final UI And Release Readiness (`prompt-41` through `prompt-43`)

- Required pack: full blocker pack plus `UI-01` through `UI-03`, final rerun of `RB-01` through `RB-10`, and end-to-end happy-path smoke across public, auth, guest donor, donor, guardian informational, guardian protected, and multi-role flows
- Gate purpose: prove the final shipped product is visually consistent, route-stable, and safe to move into the rollout and risk planning gate.
