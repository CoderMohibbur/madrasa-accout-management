# Prompt 26 Rollout And Risk Plan

This artifact is the durable prompt-26 rollout plan. It reuses the approved prompt-25 test matrix and keeps prompt-23 schema limits plus prompt-24 classification-first migration rules intact.

## Rollout Phase Order

### Phase R1 - Shared Foundation And Dark-State Readiness (`prompt-28` through `prompt-32`)

- Land shared UI foundation first so later feature work reuses one product family instead of cloning donor or guardian pages.
- Add the approved schema foundation dark and nullable-first.
- Run prompt-24 classification reports before any authoritative read-path change or mutating backfill.
- Adapt shared account-state reads, registration, and verification only after `MIG-*`, `AUTH-*`, `REG-*`, `EMAIL-*`, `PHONE-*`, and `ROUTE-*` gates are green.

### Phase R2 - Donation Entry And Donor Payment Foundation (`prompt-33` through `prompt-34`)

- Ship public and identified donation entry only after the donor payable model is active and settlement is server-authoritative.
- Keep donor payment ability, narrow receipt access, and donor portal eligibility separate.
- Keep donor-domain tables new and clean; legacy donor `transactions` remain out of the new settled history.

### Phase R3 - Donor Account And Portal Rollout (`prompt-35`)

- Layer donor auth/account behavior separation on top of the already-working payment flow.
- Turn on donor portal history and receipt-history browsing only for users that satisfy the explicit donor eligibility rules.
- Keep transaction-specific receipt/status access broader than portal eligibility where approved, but never broaden into full history by payment completion alone.

### Phase R4 - Guardian Informational Rollout (`prompt-36`)

- Ship the additive informational guardian route space before touching protected guardian ownership boundaries.
- Limit this phase to non-sensitive institution information, admission handoff, and self-only linkage/help/status messaging.
- Preserve the live protected `/guardian` route as a separately gated domain.

### Phase R5 - Guardian Protected Rollout (`prompt-37`)

- Turn on protected guardian student, invoice, payment, and receipt views only after informational guardian and shared account-state foundations are stable.
- Require both guardian profile eligibility and object-level ownership.
- Keep ambiguity buckets fail-closed: pending-review, linkage drift, and invoice-versus-pivot conflicts do not ship as auto-resolved access.

### Phase R6 - Identity Expansion, Multi-Role, And Route Finalization (`prompt-38` through `prompt-40`)

- Add Google sign-in only after local identity, approval, and linkage boundaries are stable.
- Add neutral chooser and explicit context switching only after donor and guardian contexts are independently correct.
- Finalize route, middleware, and policy cleanup last in this cluster so shared-home and portal-entry behavior becomes eligibility-driven rather than role-order-driven.

### Phase R7 - External Handoff, Final UI Consistency, And Release Gate (`prompt-41` through `prompt-43`)

- Activate the canonical external admission URL only on approved public and guardian informational placements.
- Run the final shared-UI consistency pass after all domain surfaces exist.
- Treat prompt-43 as the hard release gate: full blocker pack, end-to-end smoke, and no-go review.

## Risk Ranking

### 1. Highest Risk - Shared Account-State Read Cutover (`prompt-29` through `prompt-32`)

- Why it ranks first: it changes login, registration, verification, routing, and account eligibility at the same time the system is learning new state fields.
- Primary failure mode: approved legacy users get locked out, pending users get let in, or blanket `verified` removal widens access before replacement middleware is ready.
- Blocking tests: `RB-01`, `RB-02`, plus the Phase A minimum pack from prompt-25.

### 2. Highest Risk - Donor Payment-Domain Foundation (`prompt-34`)

- Why it ranks second: it introduces the most settlement-sensitive public flow in the roadmap.
- Primary failure mode: unauthorized or ambiguous payment attempts create records or receipts, or legacy donor `transactions` get mixed into the new donor history.
- Blocking tests: `RB-04`, `RB-05`, `PAY-*`, `GUEST-*`, `DONOR-01`, `DONOR-04`.

### 3. Highest Risk - Guardian Protected Boundary Split (`prompt-37`)

- Why it ranks third: it sits closest to sensitive student, invoice, payment, and receipt data.
- Primary failure mode: pivot-only or invoice-only linkage interpretation broadens guardian access, especially in multi-guardian families.
- Blocking tests: `RB-06`, `POL-*`, `GPROT-*`.

### 4. High Risk - Route / Middleware / Policy Finalization (`prompt-40`)

- Why it ranks fourth: it replaces temporary compatibility routing with the final eligibility-driven model.
- Primary failure mode: role-order redirects or partial middleware swaps bypass donor or guardian gates, or break `dashboard` compatibility.
- Blocking tests: `RB-07`, `RB-09`, `ROUTE-*`, `MULTI-04`, rerun key `POL-*`.

### 5. High Risk - Google Sign-In Introduction (`prompt-38`)

- Why it ranks fifth: alternate auth flows can become an identity-linking bypass if conflict handling is weak.
- Primary failure mode: provider email or contact-field similarity silently links to the wrong local account or implies guardian protected eligibility.
- Blocking tests: `RB-03`, `RB-08`, `GOOG-*`, `EMAIL-04`.

### 6. Medium-High Risk - Donor Portal Eligibility And History Rollout (`prompt-35`)

- Why it ranks here: it is mostly read-path and eligibility work, but it sits on top of payment-domain truth and narrow receipt access.
- Primary failure mode: payment completion or raw donor role gets mistaken for full donor portal eligibility.
- Blocking tests: `DONOR-*`, `PAY-03`, `RB-05`.

### 7. Medium Risk - Guardian Informational Rollout (`prompt-36`)

- Why it ranks lower: it is additive and non-sensitive by design.
- Primary failure mode: informational surfaces accidentally reuse protected components or expose student/invoice data.
- Blocking tests: `GINFO-*`, `UI-02`, `RB-06` entry conditions.

### 8. Lowest Relative Risk - Shared UI Foundation And Final UI Consistency (`prompt-28`, `prompt-42`)

- Why it ranks lowest: it is largely presentation-layer normalization and reuse of shared primitives.
- Primary failure mode: inconsistent shell adoption or broken navigation, not direct ownership leakage.
- Blocking tests: `UI-*`, `ROUTE-01`, `ROUTE-04`.

## Rollback Points

### Rollback Point A - After Shared UI Foundation (`prompt-28`)

- Safe reason: presentation-only foundation can be reverted without data rollback.
- Stop condition: shared shell or component adoption breaks existing navigation or layout consistency.

### Rollback Point B - After Schema Dark Launch And Before Authoritative Read Switch (`prompt-29`)

- Safe reason: prompt-23 schema adds new fields and tables additively; prompt-24 keeps them dark first.
- Stop condition: migrations land, but classification or backfill evidence is not good enough to trust read-path cutover.

### Rollback Point C - After Classification Reports And Conservative Backfill, Before Login And Eligibility Cutover (`prompt-30` through `prompt-32`)

- Safe reason: new columns can remain populated but non-authoritative while legacy gating stays in place.
- Stop condition: ambiguity buckets are larger than expected, or `AUTH-*` / `MIG-*` gates fail.

### Rollback Point D - After Donation Entry Activation But Before Donor Portal History (`prompt-33` through `prompt-34`)

- Safe reason: public and identified donation entry can remain available or be paused independently of donor portal browsing.
- Stop condition: settlement or receipt access is unstable even though donor portal is not yet live.

### Rollback Point E - After Guardian Informational Release, Before Guardian Protected Adaptation (`prompt-36`)

- Safe reason: informational guardian routes are additive and separate from live protected `/guardian`.
- Stop condition: informational content leaks protected data or external handoff is placed on an unapproved surface.

### Rollback Point F - After Google Sign-In, Before Multi-Role Final Redirect Alignment (`prompt-38` through `prompt-39`)

- Safe reason: provider routes can be disabled while local auth and independent donor/guardian contexts continue to work.
- Stop condition: identity-link conflicts or chooser/switching behavior create authorization ambiguity.

### Rollback Point G - Final Release Gate (`prompt-43`)

- Safe reason: this is the last no-go checkpoint before broader rollout.
- Stop condition: any `RB-*` blocker remains open, baseline-vs-regression separation is unclear, or full smoke coverage exposes routing, ownership, or UI consistency regressions.

## Must-Delay Items

- Do not ship destructive cleanup of legacy columns, pivots, route compatibility shims, or legacy transaction/report dependencies before prompt-43 plus post-rollout validation.
- Do not ship any read-path cutover that treats prompt-24 ambiguity buckets as auto-resolved identities, phones, or guardian ownership.
- Do not ship any donor history bridge that converts legacy donor `transactions` into `donation_records` during the initial rollout.
- Do not ship guest claim/account-link flows, recurring donations, or saved payment methods in the first rollout wave.
- Do not ship protected guardian access, student visibility, invoice visibility, or payment-entry controls inside guardian informational phases.
- Do not ship Google-driven protected guardian onboarding, provider-subject conflict auto-resolution, or contact-field-based auto-linking.
- Do not ship multi-role mixed dashboards, self-service role claiming, or raw role-order shared-home redirects as the final model.
- Do not ship internal admission workflow, draft/resume, upload, or internal status tracking; admission remains external-handoff-only.
- Do not ship removal of blanket `verified` middleware until donor, guardian informational, guardian protected, and shared-home eligibility middleware are each in place and tested.

## Readiness Criteria Per Phase

### R1 Readiness - Shared Foundation And Dark-State Readiness

- Prompt-23 schema additions are additive and nullable-first.
- Prompt-24 classification-first reports exist and ambiguous rows remain in review buckets.
- Prompt-25 Phase A pack passes: `AUTH-*`, `REG-*`, `EMAIL-*`, `PHONE-*`, `ROUTE-*`, `MIG-*`.
- Baseline auth/profile failures remain unchanged and still classified as baseline only.
- Existing route names remain preserved.

### R2 Readiness - Donation Entry And Donor Payment Foundation

- R1 criteria remain green.
- Donor payable model is authoritative for new donor payments.
- `GUEST-*`, `PAY-*`, and donor payment-scope tests pass without showing legacy donor `transactions` as new settled history.
- Transaction-specific receipt/status access is narrow and opaque where public.

### R3 Readiness - Donor Account And Portal Rollout

- R2 criteria remain green.
- Donor portal eligibility is proven separate from payment completion.
- `DONOR-*` tests and `RB-05` gates pass.
- Portal history and receipt history are limited to explicit donor-owned scope.

### R4 Readiness - Guardian Informational Rollout

- R1 criteria remain green.
- Informational guardian routes stay additive and separate from protected `/guardian`.
- `GINFO-*` tests pass and `UI-02` confirms admission CTA placement remains approved-only.
- No student, invoice, receipt, or payment data appears on informational surfaces.

### R5 Readiness - Guardian Protected Rollout

- R4 criteria remain green.
- Protected guardian policies pass for linked eligible guardians and fail for unlinked, role-only, deleted, inactive, or pending-review rows.
- `GPROT-*`, `POL-*`, and `RB-06` gates pass.
- Multi-guardian object scoping is explicitly proven.

### R6 Readiness - Identity Expansion, Multi-Role, And Route Finalization

- R3 and R5 criteria remain green.
- `GOOG-*`, `MULTI-*`, rerun `ROUTE-*`, and route/policy blocker tests pass.
- Email-based safe linking is conflict-free and fail-closed on ambiguity.
- Shared-home behavior is eligibility-driven with no mixed-scope dashboard data.

### R7 Readiness - External Handoff, Final UI Consistency, And Release Gate

- All prior rollout criteria remain green.
- `UI-*` smoke passes across public, auth, donor, guardian informational, guardian protected, and multi-role surfaces.
- Canonical external admission URL appears only on approved placements.
- Prompt-25 blocker pack `RB-01` through `RB-10` passes in the final release candidate.
- End-to-end smoke distinguishes baseline failures from new regressions clearly enough for a go/no-go decision.

## Safest Early-Value Slices

### 1. Guardian Informational Portal (`prompt-36`)

- Why it is early-value: it delivers visible guardian-facing value, admission guidance, and help/status messaging without exposing protected student finance data.
- Why it is relatively safe: it is additive, non-sensitive, and explicitly separate from protected guardian ownership boundaries.

### 2. External Admission URL Implementation (`prompt-41`)

- Why it is early-value: it improves public and guardian informational conversion with a canonical configured handoff.
- Why it is relatively safe: it is content/configuration placement on already-approved surfaces, not a new protected-data model.

### 3. Shared UI Foundation (`prompt-28`)

- Why it is early-value: it improves product consistency and reduces rework across every later surface.
- Why it is relatively safe: it does not depend on account-state or payment correctness.

### 4. Guest Donation Entry Shell (`prompt-33`)

- Why it is early-value: it creates public donation entry momentum before full donor portal rollout.
- Why it is only conditionally safe: it should ship only together with the prompt-34 donor payment-domain guardrails, not as an isolated payment shortcut.
