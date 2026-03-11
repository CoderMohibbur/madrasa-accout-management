# Report

This prompt consolidates the approved analysis sequence into the final implementation planning packet. No contradiction was found with prompt-23 schema constraints, prompt-24's classify-first migration posture, prompt-25's approved test matrix, or prompt-26's rollout and risk plan. Implementation may begin, but only with prompt-28 and only under the no-go warnings listed at the end of this packet.

## Final Business Rules

- One authenticated `users` account is the only account model and may hold donor and guardian roles simultaneously.
- Role membership expresses domain potential only; final donor portal access, guardian informational access, guardian protected access, and shared-home behavior must derive from explicit eligibility rules.
- Approval, email verification, phone verification, lifecycle, portal eligibility, linkage, and deletion remain separate state dimensions.
- Registration creates only a base account plus domain intent or a non-eligible draft profile; it never auto-grants donor portal access, guardian linkage, or protected access.
- Guest donation remains a first-class path with optional human identity capture and mandatory operational traceability, but no default account or donor-profile creation.
- Donor payment ability is separate from donor portal eligibility, and transaction-specific receipt/status access is narrower than full donor history access.
- Guardian informational access and guardian protected access are separate approved products; protected access requires linkage and object-level authorization.
- Admission scope remains informational-only with one external application handoff boundary.
- Google sign-in and multi-role support are additive same-account capabilities and must never auto-link ambiguous identities or auto-grant protected access.

## Final Technical Architecture

- Keep a single `users` table, one `web` guard, one password broker, and one session-auth system.
- Add shared account-state fields on `users` rather than creating separate authenticatable donor or guardian tables.
- Keep donor and guardian as domain profiles linked to the shared user account.
- Model donor payments with `donation_intents` as pre-settlement payables, `payments` as attempts, `donation_records` as settled donor outcomes, and `receipts` as payment-specific post-settlement artifacts.
- Reuse the existing `payments` and `receipts` tables for the minimal donor rollout instead of redesigning them.
- Model guardian access with both route-level guardian profile state and object-level guardian-student/invoice ownership checks.
- Preserve `transactions` as the legacy posted-money/reporting layer; it is not the donor live-payment source of truth.
- Split routes into public, auth, guest-donation, donor, guardian informational, guardian protected, shared-home, and management buckets.
- Replace blanket `verified` and raw `role:*` checks with explicit donor, guardian-informational, guardian-protected, and shared-home eligibility middleware plus object policies.
- Use one shared UI foundation, shared shell system, and shared component library across public, auth, donor, guardian, management, and multi-role surfaces.
- Keep migrations additive-first, backfills classification-first, and read-path cutovers delayed until the approved evidence is ready.

## Final Account-State Model

- Base state axes:
  - identity existence
  - approval status
  - email verification
  - phone verification
  - account status/lifecycle
  - deletion marker
  - role membership
  - donor profile state
  - guardian profile state
  - guardian linkage state
- Derived access states:
  - `public_only`
  - `donor_intent_pending`
  - `donor_profile_present_inactive`
  - `donor_portal_eligible`
  - `guardian_intent_pending`
  - `guardian_profile_present_unlinked`
  - `guardian_informational_eligible`
  - `guardian_protected_eligible`
  - `multi_context_eligible`
- Guardrails:
  - `email_verified_at` remains the email-proof field and must stop acting as the long-term approval/eligibility proxy once shared account-state fields land.
  - Phone remains optional and verification-first.
  - Account deletion/suspension must not remain implicit profile-only behavior.
  - Shared-home behavior must derive from eligible contexts, not raw role ordering.

## Final Donor Model

- Canonical donor identities are `guest donor`, `identified donor`, and `anonymous-display donor`.
- Guest and identified donors share one safe payment domain and one settlement model.
- Approved donor flow: `donation_intent -> payments -> donation_record -> receipt`.
- Guest donation creates no account and no donor profile by default, even when contact fields are supplied.
- Identified donation may be linked to a known `user_id`, but donor payment completion never auto-grants donor portal eligibility.
- Transaction-specific receipt/status access ships earlier and broader than donor portal receipt-history browsing.
- Donor portal history and receipt history are reserved for explicitly donor-eligible users.
- Guest claim/account-link remains later optional work and requires donation-specific proof plus authenticated intent.
- Recurring donation, saved payment methods, and donor-history conversion from legacy `transactions` are out of the initial rollout.

## Final Guardian Model

- Guardian self-registration and guardian intent capture may create only an unlinked informational-state guardian record.
- The product must ship a separate authenticated guardian informational portal before adapting the protected guardian portal.
- Informational guardian scope is limited to institution information, admission information, external handoff, and self-only linkage/help/status messaging.
- Protected guardian scope includes linked students, invoices, receipts, payment history, and payment initiation only after linkage and object authorization succeed.
- Guardian linkage and protected ownership must compare guardian profile state, `guardian_student`, and invoice ownership together.
- Role membership alone never grants guardian protected access.
- Multi-guardian families remain object-scoped rather than flattened into one family-wide permission rule.
- Admission handoff remains external-only and cannot become an internal admission workflow inside guardian pages.

## Final Multi-Role Model

- One authenticated account may hold donor and guardian roles simultaneously.
- Donor-owned data and guardian-owned data remain isolated even for the same logged-in user.
- `/donor` and `/guardian` stay as explicit context routes.
- Shared-home behavior must be eligibility-driven:
  - one eligible context -> direct redirect
  - multiple eligible contexts -> neutral chooser
  - zero eligible contexts -> safe fallback
- Multi-role switching must be explicit, must preserve deep-link authorization, and must not create mixed-scope dashboards.
- Multi-role support layers on only after donor and guardian contexts are independently correct.
- Google sign-in and later claim/link flows attach to the same account model and must not create parallel identities.

## Final Routing / Policy Plan

- Preserve existing route names: `dashboard`, `guardian.*`, `donor.*`, `payments.*`, and `management.*`.
- Keep live protected `/guardian` routes protected while adding a separate guardian informational route space.
- Keep public routes for institution/admission information and guest donation entry/status distinct from authenticated portal routes.
- Keep `management.surface` compatibility in place until the approved implementation prompts replace it safely.
- Replace blanket `verified` dependence only when donor, guardian informational, guardian protected, and shared-home eligibility middleware are ready.
- Keep `StudentPolicy` and `StudentFeeInvoicePolicy` protected-only and domain-specific.
- Keep receipt, payment detail, payment initiation, and payment status authorization domain-aware and narrower than raw route membership.
- Use one shared resolver/component for the external admission URL and restrict it to approved public and guardian informational surfaces.

## Final Global UI/UX Direction

- Use a light-first institutional product family across public, auth, donor, guardian, management, and multi-role surfaces.
- Preserve one shared design language; role differences appear through content and subtle accenting, not separate visual systems.
- Start implementation with shared foundation slices `UF1` through `UF4`: tokens, shell/header primitives, feedback/state patterns, and form/data-display primitives.
- Build from the approved shared template families: auth form, informational content, portal overview, portal list, portal detail, payment outcome, donation entry, account state, and context chooser.
- Normalize shells, navigation, headers, cards, buttons, forms, alerts, tables, empty states, loading states, and no-access states before broad feature-page adoption.
- Treat payment outcome/status UI as one reusable cross-domain family.
- Keep external admission handoff on approved public and guardian informational surfaces only.

## Final Schema / Migration Plan

- Mandatory schema work is limited to:
  - shared account-state columns on `users`
  - explicit guardian linkage-state fields
  - donor-domain `donation_intents` plus `donation_records`
- Existing `payments` and `receipts` remain the approved minimal donor payment infrastructure.
- No mandatory multi-role table is required; multi-role remains derived from shared account-state plus profile eligibility.
- Later optional schema improvements include a dedicated external-identity linkage record, guest-claim audit persistence, and profile-flag normalization.
- Migration posture:
  - add schema dark and nullable-first
  - generate read-only classification reports before mutation
  - backfill conservatively and idempotently
  - switch read paths only after classification/backfill evidence is accepted
  - defer destructive cleanup until after implementation validation
- Backfill rules:
  - use `email_verified_at` only to seed legacy approval compatibility
  - do not rewrite `email_verified_at`
  - default account-level lifecycle conservatively
  - populate `users.phone` only from unambiguous sources
  - keep ambiguous identity, linkage, and phone rows in review buckets
  - start new donor-domain tables empty for the first rollout
  - do not auto-create missing profiles from role-only rows

## Final Testing Plan

- The prompt-25 durable test matrix is the source of truth for required coverage.
- Test packs are cumulative, not per-feature replacements.
- Phase A coverage gates prompt-28 through prompt-32 with `AUTH-*`, `REG-*`, `EMAIL-*`, `PHONE-*`, `ROUTE-*`, and `MIG-*`.
- Donor rollout adds `GUEST-*`, `PAY-*`, and `DONOR-*` coverage before donor portal history is live.
- Guardian rollout adds `GINFO-*`, `GPROT-*`, and `POL-*` coverage, with informational and protected gates kept separate.
- Google/multi-role/route finalization adds `GOOG-*`, `MULTI-*`, rerun `ROUTE-*`, and rerun critical `POL-*`.
- Prompt-43 is the hard release gate: full blocker pack `RB-01` through `RB-10`, end-to-end smoke, and baseline-vs-regression separation.
- Known auth/profile baseline failures remain baseline until an implementation prompt explicitly changes those behaviors.

## Exact Implementation Phase Order

1. Prompt 28 - shared UI foundation implementation
2. Prompt 29 - account-state schema foundation
3. Prompt 30 - account-state read-path adaptation
4. Prompt 31 - open registration foundation
5. Prompt 32 - email/phone verification foundation
6. Prompt 33 - guest donation entry
7. Prompt 34 - donor payable foundation
8. Prompt 35 - donor auth and portal access
9. Prompt 36 - guardian informational portal
10. Prompt 37 - guardian protected portal gating
11. Prompt 38 - Google sign-in foundation
12. Prompt 39 - multi-role home and switching
13. Prompt 40 - route/middleware/policy finalization
14. Prompt 41 - external admission URL implementation
15. Prompt 42 - final UI consistency pass
16. Prompt 43 - tests and rollout-readiness validation

## Exact No-Go Warnings

- Stop if any implementation step requires renaming existing route names or editing historical migrations.
- Stop if any step tries to finalize donor online payments directly against legacy `transactions`.
- Stop if any read-path or auth cutover happens before prompt-24 classification-first evidence and review buckets are ready.
- Stop if any donor, guardian, guest, phone, or Google flow auto-links or auto-verifies ambiguous records from loose contact similarity.
- Stop if guardian protected access is granted from role membership alone, informational access alone, or only one ownership signal.
- Stop if blanket `verified` is removed before explicit donor, guardian informational, guardian protected, and shared-home eligibility middleware exist.
- Stop if Google/provider flows auto-resolve subject conflicts or imply portal eligibility, guardian linkage, or protected access.
- Stop if guest checkout auto-creates accounts/profiles or if guest claim/account-link, recurring billing, or saved payment methods are pulled into the first rollout.
- Stop if multi-role work introduces mixed-scope dashboards, self-service role claiming, or raw role-order redirect logic as the final model.
- Stop if admission work expands into internal forms, drafts, uploads, or protected guardian/auth CTA placements beyond approved surfaces.
- Stop if baseline-vs-regression test separation becomes unclear or if any prompt-25 blocker pack gate fails.

## Whether Implementation May Begin

Yes. Implementation may begin with prompt-28 only.

Conditions:

- follow the exact implementation phase order above
- preserve the prompt-26 rollout waves and rollback checkpoints
- preserve prompt-23 schema limits, prompt-24 classification-first migration rules, and prompt-25 blocker packs
- stop immediately if any no-go warning is triggered
