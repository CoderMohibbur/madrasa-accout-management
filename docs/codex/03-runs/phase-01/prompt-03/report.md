# Report

## Frozen Business Rules

1. Registration remains open to the public. Donor self-registration and guardian self-registration are required target behaviors.
2. The system should continue to prefer one authenticated account model. A single account may legitimately hold donor and guardian roles at the same time.
3. Verification must support both email and phone as coexistence options, but verification completion must not be a universal prerequisite for all allowed donor or guardian entry flows.
4. Donors must be allowed to register, log in, and donate without first completing email or phone verification, subject to safe payment-domain, anti-abuse, reconciliation, and reporting rules.
5. Donation must support guest use. Prior registration, prior portal access, and mandatory account creation must not be required for a direct amount-based donation flow.
6. Guest donation identity fields may be optional, but every donation must still capture enough traceability metadata for reconciliation, anti-abuse controls, and audit/reporting safety.
7. If guest donation includes usable contact data, the system may create or attach a lightweight donor identity only through duplicate-safe, non-privilege-escalating rules. Guest donation must never auto-grant portal access.
8. Canonical donor terminology should distinguish:
   - `guest donor`: no account and no portal access requirement
   - `anonymous-display donor`: internally traceable but intentionally hidden in public display contexts
   - `identified donor`: a linked or known donor/account identity
9. Guardian users may log in without completing verification, but unverified or unlinked guardians may only access a light authenticated informational surface until linkage and authorization are satisfied.
10. The light guardian informational surface may include non-sensitive institution information and admission-related information only.
11. Student-linked academic, invoice, receipt, and payment-sensitive data must remain linkage- or authorization-controlled at all times, regardless of registration or verification flexibility.
12. This project does not include a full admission application system. Admission scope is limited to information plus a button or link to an external application.
13. Legitimate multi-role users must be able to reach both donor and guardian portal surfaces from the same account without mixing data scopes.
14. Google sign-in is an optional onboarding/sign-in method, especially for donor/public flows, but it must still respect safe guardian linkage and protected-surface rules.
15. Existing accounting, reporting, guardian invoice payment, donor/payment safety, and legacy management protections remain frozen unless a later prompt explicitly narrows an approved change surface.
16. UI/UX must converge on one modern, globally consistent product standard. Existing weak or inconsistent patterns are not binding requirements.

## Hard Must-Haves

- Public registration must remain possible, including donor and guardian self-registration.
- Donor flows must allow registration, login, and donation without mandatory email or phone verification.
- Guest donation and direct amount-based donation must be supported without mandatory account creation.
- Guest or anonymous donations must preserve traceability, anti-abuse controls, reconciliation safety, and reporting safety.
- Unverified or unlinked guardians may access a light informational portal after login, but protected student/invoice/payment data must stay authorization-controlled.
- Admission scope must stay informational only with an external application handoff, not a full admissions workflow.
- One account may hold donor and guardian roles simultaneously, and legitimate users must be able to access both surfaces without cross-scope leakage.
- Existing accounting, reporting, guardian invoice payment, donor/payment safety, and legacy management protections must remain intact.
- A single, modern, consistent design system is mandatory across new/public/auth/donor/guardian/multi-role surfaces.

## Optional Or Later-Phase Items

- Google sign-in is required as an optional convenience path, but it is a later-phase enhancement rather than the core baseline gate for business-rule freeze.
- Full phone-verification implementation is required capability, but it can be delivered later than the initial registration/donation access paths as long as the rule remains frozen.
- Lightweight donor identity creation or attachment from optional contact data is allowed later, but only if duplicate-handling and privilege-escalation rules are defined first.
- Multi-role home and context-switching UX refinement is a later-phase implementation concern so long as role/data-scope isolation stays frozen now.
- Public-display terminology and reporting treatment for anonymous-display donors can be refined later, provided the internal traceability rule stays fixed.

## Ambiguities And Chosen Interpretations

- Ambiguity: “Unverified donors may log in” could imply full portal access without safeguards.
  Chosen interpretation: unverified login is allowed, but any write or sensitive access still depends on the safest payment-domain and authorization rules for that surface.

- Ambiguity: “Guardians may log in without verification” could imply access to linked student financial data before linkage.
  Chosen interpretation: unverified or unlinked guardians get only the light informational portal until linkage and protected-surface authorization are satisfied.

- Ambiguity: “Name, phone, and email may be optional during donation” could imply donations with no usable audit handle.
  Chosen interpretation: human identity fields may be optional, but operational traceability fields are mandatory.

- Ambiguity: “Create or attach a lightweight donor/user identity” could imply unsafe auto-merging into an existing account.
  Chosen interpretation: no automatic merge into an existing privileged or portal-enabled identity without explicit safe matching rules and proof of ownership.

- Ambiguity: “Guest / anonymous / hidden donor” could create overlapping accounting terms.
  Chosen interpretation: use `guest donor`, `anonymous-display donor`, and `identified donor` as the canonical classes unless a later prompt needs a tighter reporting vocabulary.

- Ambiguity: “Google sign-in should work for guardian onboarding too” could imply bypassing guardian linkage controls.
  Chosen interpretation: Google sign-in may create or authenticate the base account, but guardian-protected surfaces still require the same linkage and authorization checks as password-based accounts.

## Forbidden Scope Expansions

- Do not turn the project into a full admission application system.
- Do not require registration, verification, or portal access as a blanket prerequisite for every donation.
- Do not auto-grant donor or guardian portal access from guest donation data alone.
- Do not weaken student-linked academic, invoice, receipt, payment, or guardian linkage authorization controls.
- Do not reuse unsafe legacy `transactions` shortcuts as the direct finalization model for new donor or guest online payments.
- Do not introduce multi-guard auth complexity as a shortcut around role/profile separation.
- Do not silently change legacy accounting, reporting totals, route-name compatibility, or management protections under the guise of satisfying these business rules.
- Do not preserve weak or inconsistent legacy UI patterns as the required target standard.

## Whether Business Rules Are Sufficiently Frozen To Proceed

Yes. The business rules are sufficiently frozen for later analysis prompts to proceed.

Important caveat:
- The current repository only partially implements this frozen target rule set. Later prompts must treat gaps such as guest donation, unverified-access redesign, phone verification coexistence, and Google sign-in as planned deltas or out-of-scope gaps, not as already-completed capabilities.
