# Report

This prompt defines the safe multi-role account model for one authenticated user who may hold both donor and guardian roles under the already-approved donor and guardian boundaries. No contradiction was found with prompt-12 donor planning, prompt-13 guardian permission rules, prompt-14 admission-information rules, prompt-15 guardian slice planning, or the earlier prompt-05 shared-account linkage model. The live repository already allows multiple roles on one `User`, but its current `/dashboard` behavior still assumes only one non-management portal context because `RedirectPortalUsersFromLegacyDashboard` redirects guardians before donors and the portal layouts expose no cross-context switching.

## Current Repo Findings

- `User` already supports many-to-many role membership through `HasRoles`, plus one donor profile and one guardian profile relation on the same authenticated account.
- Donor route entry is currently `auth` + `verified` + `role:donor`, followed by donor-profile eligibility inside `DonorPortalData::requireDonor()`.
- Guardian route entry is currently `auth` + `verified` + `role:guardian`, followed by guardian-profile eligibility inside `GuardianPortalData::requireGuardian()` and then linkage-sensitive `StudentPolicy`, `StudentFeeInvoicePolicy`, `ReceiptPolicy`, and invoice payable rules.
- Donor data is donor-scoped today through `transactions.doner_id` and donor/user-bound receipts.
- Guardian protected data is guardian-scoped today through guardian-student linkage, invoice ownership compatibility, and guardian-bound payment access.
- Direct portal URLs can already stay separate, but `/dashboard` is not a safe final multi-role home because guardian-first ordering hides a second eligible donor context.
- The current repo remains narrower than the target model because guardian informational access is still not implemented and universal `verified` middleware still sits above both portal groups.

## Target Multi-Role Model

- One authenticated `users` account remains the only shared login identity.
- That one account may hold both `donor` and `guardian` roles at the same time.
- Each role expands into its own domain profile and its own derived portal context:
  - donor portal context
  - guardian informational context
  - guardian protected context
- Role membership continues to mean domain potential only. It does not itself prove portal eligibility, protected eligibility, or ownership of domain data.
- Donor and guardian contexts must remain parallel, not merged:
  - donor context is donor-owned history and donor-visible receipts only
  - guardian informational context is non-sensitive informational/admission guidance only
  - guardian protected context is linkage-controlled student, invoice, payment, and receipt access only
- A multi-role user is therefore one login with multiple separately derived contexts, not one mixed donor-guardian portal.

## Role-Expansion Rules

- Role expansion must be additive on the same `users` account. Adding donor capability must not remove guardian capability, and adding guardian capability must not remove donor capability.
- Role expansion must happen in layers:
  1. base authenticated account
  2. additive role assignment
  3. domain profile presence and lifecycle eligibility
  4. domain-specific entitlement such as donor portal eligibility or guardian linkage-controlled protected access
- Donor expansion rules:
  - donor role plus linked donor profile may later become donor-portal-eligible under the approved donor rules
  - donor role assignment must not imply guardian linkage, guardian informational access, or protected guardian entitlement
- Guardian expansion rules:
  - guardian role may first produce only guardian intent or an unlinked informational-state guardian identity
  - protected guardian eligibility arrives later and only after linkage-controlled authorization is satisfied
  - guardian role assignment must not imply donor profile ownership or donor receipt entitlement
- Future optional identity-claim or account-link behavior, if later approved, must attach a new role/profile to the existing authenticated account rather than silently creating a second user account or auto-merging records by email alone.
- Because the live repository still uses blanket `verified` middleware, the final target model must remain conceptually separate from current implementation narrowness. Future auth-state foundations still need to widen guardian informational eligibility safely without weakening protected guardian access.

## Scope-Isolation Rules

- Donor-owned data must remain isolated to donor-domain sources:
  - the linked `donorProfile`
  - donation rows owned by that donor record
  - donor-visible receipts bound to that user or donor-linked payment
- Guardian-owned data must remain isolated to guardian-domain sources:
  - the linked `guardianProfile`
  - guardian-student linkage
  - invoice ownership compatibility
  - guardian-authorized payments and receipts derived from linked invoices
- A shared login must not cause any of these unsafe shortcuts:
  - donor role implies guardian student access
  - guardian role implies donor receipt access
  - donor profile email match auto-links guardian data
  - guardian linkage auto-enables donor profile ownership
  - shared account profile settings expose cross-domain records on a neutral home
- Receipt handling must stay domain-aware:
  - donor receipts remain donor/user-bound
  - guardian receipt visibility remains invoice-ownership-derived
  - a user who qualifies for both may view both, but only inside the correct donor or guardian context
- Neutral multi-role surfaces may show only context availability, eligibility state, or next-action messaging. They must not aggregate donor giving totals together with guardian student, invoice, or payment summaries.

## Role-Switching Behavior

- `/donor` and `/guardian` remain explicit context routes. The path itself is part of the active context selection.
- Once a user is inside one context, the system must not auto-switch them into the other context just because another role exists.
- Switching between donor and guardian should be explicit:
  - via a neutral chooser on shared home when more than one context is eligible
  - via a visible "switch context" action inside portal chrome when the alternate context is also eligible
- The switch action must only navigate to another already-eligible context. It must not grant access by itself.
- Deep links remain context-local:
  - donor deep links stay donor-only
  - guardian deep links stay guardian-only and still require object-level authorization where applicable
- If the user has donor eligibility plus guardian informational eligibility only, the guardian switch target must land in the informational guardian surface, not the protected guardian surface.
- If the user has donor eligibility plus guardian protected eligibility, both contexts may be switchable, but their navigation, summaries, and permissions remain separate after switching.

## Multi-Role Home Rules

- Shared home behavior must derive from eligible contexts, not raw role order.
- Safest target behavior:
  - if exactly one context is eligible, redirect directly to that context
  - if more than one context is eligible, land on a neutral chooser
  - if no portal context is eligible, keep the existing non-portal outcome for that account class
- The neutral chooser must be intentionally low-scope:
  - show available contexts
  - show non-sensitive status or gating messages
  - show which context is protected versus informational
  - do not show donor donation totals, donor receipt tables, guardian student lists, guardian balances, or payment history
- Current guardian-first `/dashboard` redirect is therefore not the final rule. It should be treated as temporary single-context behavior only.
- This prompt does not reopen management-surface policy. The safest carry-forward assumption is that any future donor/guardian multi-role home change must not break existing management behavior while route-policy finalization remains a later phase.

## Minimal Safe Rollout Version

- The smallest safe rollout version is not a full final multi-role platform. It is a narrowly scoped multi-role home and switching improvement.
- Minimal rollout scope:
  - support one authenticated account holding both donor and guardian roles
  - detect independently eligible contexts instead of relying on guardian-first ordering
  - introduce a neutral chooser only for accounts with more than one eligible context
  - keep `/donor` and `/guardian` as separate route spaces
  - keep existing donor data rules, guardian protected rules, and payment/receipt policies intact
- Minimal rollout intentionally does not include:
  - merged donor and guardian dashboards
  - cross-domain summary widgets
  - self-service role claiming
  - auto-linking donor and guardian records
  - guest-to-account ownership conversion
  - weakening protected `/guardian` pages before the shared account and verification foundations are in place
- Because guardian informational access is still a future additive slice, the smallest immediately safe live rollout is:
  - donor portal eligible plus currently protected-guardian eligible users get neutral home and explicit switching
  - donor plus future guardian-informational coexistence lands later, after the approved auth and guardian slices are in place

## Completion Status

- No contradiction with prompt-12, prompt-13, prompt-14, prompt-15, or prompt-05 was found.
- No correction pass was required.
- Prompt-16 is complete.
- No hard blocker prevents prompt-17.
