# Report

This prompt re-runs open registration analysis against the live repository plus approved prompt-01 to prompt-05 outputs. The live code still creates only a bare `User` and still treats raw `email_verified_at` as the current login gate, but the approved target model requires registration, portal eligibility, linkage, and protected access to remain separate concerns.

## Current Registration Conflicts

- Public registration is currently a single generic guest-only flow in `routes/auth.php` and `RegisteredUserController`, with one `auth.register` form that captures only `name`, `email`, and `password`.
- `RegisteredUserController::store()` creates only a base `users` row, forces `email_verified_at = null`, suppresses the `Registered` event, skips auto-login, and redirects with an admin-approval message. Open registration therefore creates identity existence only, but it does so through an overloaded approval mechanism.
- `User` still implements `MustVerifyEmail`, while verification routes and views remain enabled. The repo therefore still exposes Laravel verification scaffolding even though the registration flow suppresses normal verification startup.
- `LoginRequest::authenticate()` blocks any account whose `email_verified_at` is null, so the current public registration flow does not produce a user who can log in and reach even a light authenticated surface.
- Registration captures no donor intent, no guardian intent, no phone, no profile draft, no linkage claim, and no neutral post-registration state other than "wait for admin approval."
- Donor and guardian protected access already depend on separate profile records, lifecycle flags, and guardian ownership/linkage checks. Registration creates none of those, which is good for safety, but it also means the current repo has no self-service bridge from public registration into donor or guardian onboarding.
- The live repo is narrower than the frozen target rules: donor portal is read-only, there is no separate guardian informational portal yet, guest donation is not implemented, and current `verified` route usage is still only a current implementation detail rather than the final target rule.

## Recommended Open Registration Model

- The safest model is one unified account-creation backend with optional public, donor, and guardian entry pages that all feed the same underlying registration pipeline.
- Registration should create only one base authenticated identity plus explicit domain intent. It must never directly create donor portal eligibility, guardian linkage, or protected access.
- The model must remain compatible with the frozen prompt-03 rules: donor login and donation cannot depend on universal verification, and guardian users must be able to reach an informational-only surface before linkage.
- Domain intent should be modeled separately from final active route roles. Safe intermediate states are:
  - `public_only`
  - `donor_intent_pending` or `donor_profile_present_inactive`
  - `guardian_intent_pending` or `guardian_profile_present_unlinked`
- Separate donor and guardian landing pages are acceptable only as intent presets or copy variants. They should not create separate auth tables, separate guards, or permanently separate account systems.
- Because the live repo still routes and redirects by `verified` plus `role:*`, open registration should not auto-assign the current donor or guardian route-driving roles during initial public onboarding. Role-intent capture is safer than premature role activation.
- A draft donor or guardian profile may be created during onboarding only if it is explicitly non-eligible by default: no portal entry, no protected data visibility, no student linkage, no payment entitlement, and no ownership assumptions.
- A neutral authenticated home or onboarding handoff is safer than redirecting newly registered users toward `/dashboard`, `/donor`, or `/guardian` while current middleware still targets protected portal surfaces.

## Public Registration Flow

1. User opens the generic register page or an intent-preselected entry page.
2. System creates one base `User` account only.
3. If no donor or guardian intent is selected, the account lands in a `public_only` or `authenticated_no_portal` state.
4. After authentication under the later prompt-07 verification model, the user should land on a neutral home with safe next actions such as donate, become a guardian, or manage profile data.
5. Registration alone creates no donor profile, no guardian profile, no student linkage, no receipt visibility, and no portal eligibility.
6. Verification, resend, and duplicate-contact behavior remain a later prompt-07 concern and must not be folded back into portal eligibility.

## Donor Registration Flow

1. User enters through the generic register page or a donor-branded entry page that preselects donor intent.
2. System creates the same base `User` account and records donor intent on that account.
3. Safest initial handling is either:
   - keep donor intent only until later onboarding requires a donor record, or
   - create one donor profile in an explicitly inactive, non-portal state.
4. Registration alone does not grant donor portal access, donation history visibility, receipt visibility, or any protected donor route access.
5. The donor can later use donor-facing public or authenticated onboarding surfaces, including future donation flows, without creating a second account.
6. Donor portal eligibility becomes a later derived state only after the donor domain record is explicitly linked to the account and marked active, non-deleted, and portal-eligible under the later donor access model.

## Guardian Registration Flow

1. User enters through the generic register page or a guardian-branded entry page that preselects guardian intent.
2. System creates the same base `User` account and records guardian intent.
3. The system may create one guardian profile only in an unlinked, non-protected state that carries no student attachment and no protected entitlement.
4. Registration alone must not assign guardian protected eligibility, attach student records, expose invoices, expose receipts, or expose payment history.
5. After authentication under the later verification model, the user may reach only the future guardian informational surface until linkage is proven and approved.
6. Guardian protected portal access is always a later derived state that requires guardian-domain presence plus explicit linkage or ownership authorization. Registration is never the linkage event.

## Later Role-Expansion Rules

- Donor-to-guardian or guardian-to-donor expansion must happen inside the same authenticated account, not by creating a second login.
- Adding donor intent later should:
  - reuse the current account
  - create or attach only one donor profile for that user
  - require duplicate-safe matching before linking to an existing donor record
  - reveal no donor receipts or donation history until donor eligibility is complete
- Adding guardian intent later should:
  - reuse the current account
  - create or attach only one guardian profile for that user
  - keep that profile unlinked until a separate claim, review, or linkage step succeeds
  - reveal no student, invoice, receipt, or payment data before linkage and authorization succeed
- Matching rules for existing donor or guardian records must prefer authenticated ownership proof and explicit confirmation. Loose name matching or unverified contact similarity is not sufficient.
- Final multi-role behavior should be eligibility-driven. One account may accumulate donor and guardian contexts over time, but it should later use explicit context switching rather than raw role-order redirects.

## Minimal Safe Rollout Version

- Keep one unified registration backend and the existing single `users`-based account model.
- Add explicit intent capture for `public`, `donor`, and `guardian` without auto-granting portal eligibility.
- Reinterpret registration as creation of a base identity plus optional domain intent, not as donor portal enablement, guardian linkage, or protected access.
- Do not auto-assign the current donor or guardian route-driving roles during initial open registration while the live repo still routes those roles into protected surfaces.
- Land newly authenticated public accounts on a neutral authenticated surface.
- Allow guardian registrants to become only informational or unlinked users first; protected guardian access remains a later linkage-driven state.
- Keep donor portal access, guardian protected access, guest donation, dual verification, unverified-access redesign, and Google sign-in as later prompts or later rollout steps.
- This is the smallest safe rollout that satisfies prompt-06 without violating the preserved rule that registration alone does not grant portal eligibility, linkage, or protected access.
