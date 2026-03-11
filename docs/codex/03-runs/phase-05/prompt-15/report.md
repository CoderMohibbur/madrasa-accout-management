# Report

This prompt defines the smallest safe guardian implementation slices on top of the approved prompt-13 guardian permission matrix and prompt-14 admission-information boundary. No contradiction was found with prompt-13 or prompt-14. The live repository already contains a protected guardian portal, but it is narrower than the approved target because it has no light informational surface and still depends on `verified` + `role:guardian` + profile flags before any guardian route entry. Prompt-15 therefore plans additive guardian slices that introduce the informational boundary first, preserve the existing protected data boundary, and only then adapt protected gating.

## Guardian Slice Order

1. `GA1` - Guardian intent and unlinked-profile foundation
2. `GA2` - Guardian no-portal auth/read-path separation
3. `GI1` - Light guardian informational portal shell
4. `GI2` - Informational content and external-handoff surfaces
5. `GP1` - Protected portal boundary split
6. `GP2` - Linked student/invoice/receipt read-boundary adaptation
7. `GP3` - Protected payment-entry continuity

## Guardian Auth Or Onboarding Slices

### `GA1` - Guardian intent and unlinked-profile foundation

- Area: guardian auth/onboarding
- Primary type: route-first
- Goal: use the approved open-registration model so guardian self-registration or guardian-intent capture creates only a base account plus guardian intent or an unlinked informational-state guardian profile.
- Must preserve: no protected access, no student linkage, no invoice visibility, no payment entitlement, no donor-boundary reopening.
- Dependencies: prompt-31 open-registration foundation and the prompt-04 account-state separation rules.
- Rollback-safe checkpoint: guardian onboarding can create only a no-protected-access state; current protected `/guardian` routes stay unchanged.
- Safe early-delivery: yes.

### `GA2` - Guardian no-portal auth/read-path separation

- Area: guardian auth/onboarding
- Primary type: service-first
- Goal: after the shared account-state and verification foundations land, allow guardian login and informational eligibility without relying on blanket `verified` middleware or overloaded `email_verified_at`.
- Must preserve: one `users` account model, no Google-sign-in dependency, no protected access from verification alone, no weakening of current protected ownership checks.
- Dependencies: prompt-29 account-state schema foundation, prompt-30 read-path adaptation, and prompt-32 verification foundation. `GA1` is helpful and usually precedes it.
- Rollback-safe checkpoint: guardian users can authenticate into a no-portal or informational-only state while current protected routes remain separately guarded.
- Safe early-delivery: moderate, after prompts 29-32 only.

## Informational Slices

### `GI1` - Light guardian informational portal shell

- Area: informational portal
- Primary type: route-first
- Goal: add a dedicated light guardian informational route/controller/view shell for authenticated guardian-intent or unlinked guardian users without reusing the current protected dashboard as the informational page.
- Must preserve: current protected guardian pages stay protected-only; no student, invoice, receipt, or payment data appears in the new shell.
- Dependencies: `GA2`.
- Rollback-safe checkpoint: the informational shell exists, but it shows only safe placeholder or curated non-sensitive content.
- Safe early-delivery: yes. This is the safest first guardian-facing portal slice.

### `GI2` - Informational content and external-handoff surfaces

- Area: informational portal
- Primary type: UI-first
- Goal: populate the informational portal with the approved institution/admission content, self-only onboarding/help/status messaging, and external admission CTA placements from prompt-14.
- Must preserve: no internal admission workflow, no draft/resume flow, no document upload, no decision tracking, and no leakage of protected guardian data.
- Dependencies: `GI1`. Later external-admission URL centralization from prompt-41 is a cross-cutting follow-up, not a blocker for defining the informational surface.
- Rollback-safe checkpoint: informational content is live, but all admission action remains an external handoff only.
- Safe early-delivery: yes.

## Protected Slices

### `GP1` - Protected portal boundary split

- Area: linkage-sensitive protected portal
- Primary type: service-first
- Goal: separate the future informational entry path from the existing protected `/guardian` pages so linked guardians keep the protected experience while unlinked guardians stop being routed into pages they are not eligible to use.
- Must preserve: existing protected student/invoice/payment pages remain inaccessible to unlinked or merely informational guardians.
- Dependencies: `GA2` and `GI1`.
- Rollback-safe checkpoint: protected and informational entry paths are distinct, but the protected pages themselves still use the current narrow ownership rules.

### `GP2` - Linked student/invoice/receipt read-boundary adaptation

- Area: linkage-sensitive protected portal
- Primary type: service-first
- Goal: adapt student list/detail, invoice list/detail, payment history, and receipt visibility so they remain available only to linked/authorized guardians under the future protected portal boundary instead of the current all-in-one guardian group assumptions.
- Must preserve: `StudentPolicy`, `StudentFeeInvoicePolicy`, receipt ownership rules, guardian-student linkage rules, and invoice `guardian_id` compatibility.
- Dependencies: `GP1`.
- Rollback-safe checkpoint: protected read pages still work for linked guardians, while unlinked guardians remain on the informational side with no protected leakage.

### `GP3` - Protected payment-entry continuity

- Area: linkage-sensitive protected portal
- Primary type: integration-first
- Goal: keep shurjoPay initiation, manual-bank submission, payment return pages, and guardian payment history aligned with the protected guardian boundary after the informational/protected split is introduced.
- Must preserve: current invoice-payable ownership rules, payment finalization safety, receipt boundaries, management review behavior, and the separation from donor payment work.
- Dependencies: `GP2`.
- Rollback-safe checkpoint: protected payment entry and history still function only for linked/authorized guardians after the boundary split.

## Safest Early-Delivery Guardian Slices

- `GA1` is the safest early-delivery slice because it only establishes guardian intent or an unlinked informational-state profile without exposing protected data.
- `GI1` is the safest portal slice because it is additive and non-sensitive.
- `GI2` is also low-risk because it introduces only curated institution/admission content and external handoff messaging.

## High-Risk Guardian Slices

- `GA2` is high risk because the current repo still overloads `email_verified_at` and relies on blanket `verified` middleware, so changing guardian login and informational eligibility too early could destabilize auth behavior.
- `GP2` is high risk because any mistake can leak student, invoice, receipt, or payment-sensitive data across the new informational/protected split.
- `GP3` is the highest-risk guardian slice because it touches the live protected payment-entry path and must preserve guardian invoice-payable safety, receipt boundaries, and management-review continuity.

## Dependency Notes

- Guardian implementation should not be treated as one wave. The light informational portal and the protected guardian portal are separate slices with different risk classes.
- Prompt-31 and prompt-32 are prerequisites for the guardian auth/onboarding semantics needed by `GA1` and `GA2`.
- Prompt-36 should primarily implement `GI1` and `GI2`.
- Prompt-37 should primarily implement `GP1`, `GP2`, and `GP3`.
- Prompt-41 later centralizes the external admission URL configuration and placement consistency, but prompt-15 keeps that as a downstream dependency rather than a prerequisite for the guardian slice plan.
- Prompt-39 later refines multi-role switching; guardian slice planning here assumes that informational and protected guardian contexts remain separately derivable but does not expand into cross-role home behavior.

## Rollback Checkpoints

1. After `GA1`: guardian self-registration or guardian-intent capture produces only an unlinked informational-state identity.
2. After `GA2`: guardian login and no-portal informational eligibility are separated from blanket verification assumptions, but current protected routes remain unchanged.
3. After `GI1`: a dedicated informational guardian portal shell exists with no protected data.
4. After `GI2`: admission/institution content plus external handoff is live inside the informational portal, but no admission workflow is internalized.
5. After `GP1`: informational and protected entry paths are distinct, but the protected pages still use the current narrow ownership-safe internals.
6. After `GP2`: linked guardians retain student/invoice/receipt read access while unlinked guardians stay informational-only.
7. After `GP3`: protected guardian payment-entry and history behavior remains intact after the boundary split.

## Completion Status

- No contradiction with prompt-13, prompt-14, or earlier approved boundary decisions was found.
- No donor-scope or admission-boundary correction was needed.
- Prompt-15 is complete.
- No hard blocker prevents prompt-16.
