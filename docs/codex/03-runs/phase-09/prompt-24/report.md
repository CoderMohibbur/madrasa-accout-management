# Report

This prompt translates the approved prompt-23 schema plan into the data-interpretation and backfill strategy that later implementation prompts must follow. No contradiction was found with prompts 12 through 23. The key rule is conservative migration: add new fields dark, classify existing records before mutating them, preserve legacy columns during the transition, and put ambiguous donor/guardian/account rows into explicit review buckets instead of guessing.

Prompt-24 does not add new schema requirements beyond prompt-23. It defines how existing data should be interpreted once the approved schema lands.

## Backfill Targets

### 1. `users` Approval, Account-State, And Phone Backfill

Every existing `users` row needs interpretation for the new shared account-state columns from prompt-23:

- `approval_status`
- `account_status`
- user-level deletion marker
- `phone`
- `phone_verified_at`

Safest initial interpretation:

- `email_verified_at` non-null
  - backfill `approval_status = approval_approved` for legacy-compatibility purposes
  - keep `email_verified_at` unchanged
- `email_verified_at` null
  - backfill `approval_status = approval_pending`
  - do not infer `account_status = inactive` from that alone
- `account_status`
  - backfill conservatively as `active` unless there is explicit account-wide evidence to the contrary
  - do not derive account-wide inactivity or suspension from donor/guardian profile flags alone
- user-level deletion marker
  - leave unset by default for existing rows
  - do not backfill from donor or guardian `isDeleted` alone

Phone backfill target:

- only rows with exactly one unambiguous normalized phone candidate across linked donor/guardian profiles should be considered for `users.phone`
- conflicting or missing phone sources should leave `users.phone = null`
- `phone_verified_at` should remain null for all legacy rows unless there is separate authoritative verification evidence, which the current repo does not model

### 2. Role / Profile Consistency Buckets

The current repo already uses both `role_user` and linked donor/guardian profiles, so these existing combinations need interpretation before later eligibility middleware is trusted:

- user-linked donor profile, but missing donor role
- donor role, but no donor profile
- user-linked guardian profile, but missing guardian role
- guardian role, but no guardian profile
- one user with both donor and guardian roles and both profiles

Safest rule:

- profile presence may justify adding the matching role only when the link is explicit and conflict-free
- role presence alone must not create a missing profile automatically
- role-only users remain valid no-portal candidates until later prompt logic decides otherwise

### 3. Donor Profile Backfill Buckets

Existing `donors` rows must be classified into later donor account states without creating new identities implicitly.

Required buckets:

- linked donor profile with `portal_enabled = true`, `isActived = true`, `isDeleted = false`
  - donor portal candidate
- linked donor profile with `portal_enabled = false`
  - donor no-portal candidate
- linked donor profile with `isActived = false` or `isDeleted = true`
  - inactive or blocked donor-profile candidate
- donor row with `user_id = null`
  - legacy management-side donor record only
  - do not auto-create a `users` row
- donor row whose email or mobile matches a user but has no explicit `user_id`
  - manual review only
  - do not auto-link from contact similarity

### 4. Guardian Profile And Linkage Backfill Buckets

Existing guardian data needs both profile-state interpretation and linkage-state interpretation.

Required buckets:

- guardian row with `user_id` and no `guardian_student` rows
  - profile exists, but linkage state should backfill to `unlinked`
- guardian row with `user_id` and one or more `guardian_student` rows with no ownership conflicts
  - profile-level linkage candidate `linked`
  - pivot-level linkage candidate `linked`
- guardian row with `user_id = null`
  - legacy management-side guardian record only
  - do not auto-create a `users` row
- guardian rows whose pivot links and invoice ownership disagree
  - manual review or `pending_review`
- guardian rows with conflicting lifecycle flags
  - preserve lifecycle flags
  - do not infer linkage revocation from `isDeleted` or `isActived` alone

Important cross-check surface:

- `student_fee_invoices.guardian_id`
- `guardian_student`
- guardian profile lifecycle flags

The safe first migration must compare those together instead of trusting any one source by itself.

### 5. Existing Guardian Invoice And Student Ownership Rows

Existing protected guardian reads currently depend on both:

- the guardian-student pivot
- invoice guardian ownership checks

Rows requiring interpretation:

- invoice rows where `guardian_id` is null, but pivot linkage exists
  - not automatically a problem
  - current policy already allows null-or-matching guardian ownership
- invoice rows where `guardian_id` points to a guardian who is not linked to the invoice student in `guardian_student`
  - manual review
- students linked to multiple guardians where invoice ownership narrows to one guardian
  - expected in some families
  - must remain object-level, not flattened into one profile-wide rule

### 6. Existing Donor Transactions And Payment History

This prompt needs one explicit non-backfill decision:

- do not convert legacy donor `transactions` into `donation_intents`
- do not convert legacy donor `transactions` into `donation_records`
- do not rewrite existing `payments` or `receipts` rows that belong to student-fee invoice payables

Reason:

- legacy donor `transactions` do not contain the pre-settlement, idempotency, callback, or receipt-boundary information that prompt-10 and prompt-12 require for the new donor model

Result:

- the first donor rollout may start with empty new donor-domain tables
- legacy donor history remains a later bridge concern, not a prompt-24 prerequisite

## Interpretation Risks

### Overloaded `email_verified_at`

Current auth behavior makes `email_verified_at` mean both:

- "approved enough to log in"
- "email verified"

Backfill risk:

- treating all non-null values as high-assurance verified email is unsafe
- treating all null values as globally inactive accounts is also unsafe

Safest interpretation:

- use `email_verified_at` to seed legacy approval compatibility
- preserve the field unchanged
- avoid stronger verification claims than the current repository can prove

### Profile Flags Are Domain-Local, Not Account-Wide

`portal_enabled`, `isActived`, and `isDeleted` on donor/guardian profiles describe domain profile state, not the whole base account.

Backfill risk:

- copying those flags directly into user-level account suspension or deletion would break the prompt-04 separation rule
- a user with both donor and guardian contexts could have one disabled profile and one still-valid profile

### Contact Similarity Is Not Ownership Proof

Donor and guardian tables already store `email` and `mobile`, but those values are not safe auto-link evidence for:

- creating a new `users` row
- linking a profile to an existing user
- populating `users.phone` when multiple conflicting values exist

### Role And Profile Drift Affect Current Routing

`RedirectPortalUsersFromLegacyDashboard` still redirects by role order, so any role/profile mismatch can create misleading current behavior.

Backfill risk:

- a guardian role without a guardian profile can still redirect into guardian entry logic today
- a donor or guardian profile without its matching role may disappear from role-based route checks until later prompts replace that coupling

### Guardian Pivot Vs Invoice Ownership Drift

Protected guardian access is currently checked through both `guardian_student` and `student_fee_invoices.guardian_id`.

Backfill risk:

- inferring final linkage from pivot-only rows can miss invoice-specific ownership mismatches
- inferring final linkage from invoice-only rows can broaden access beyond actual guardian-student relationships

### Legacy Donor Transactions Are Unsafe For Conversion

Legacy donor `transactions` are accounting/report rows, not checkout-settlement rows.

Backfill risk:

- converting them into `donation_records` would create false settled-donation history
- mixing them into new donor-domain history too early risks double counting once prompt-35 bridges portal history later

## Safe Migration Order

1. Add the approved schema columns and tables in additive, nullable-first form.
   - no destructive cleanup
   - no read-path switch yet
2. Produce read-only classification reports before mutating rows.
   - users by `email_verified_at`
   - donor profiles by `user_id` and portal flags
   - guardian profiles by `user_id` and portal flags
   - role/profile mismatches
   - guardian-student vs invoice-ownership mismatches
   - phone conflicts across linked profiles
3. Backfill `users.approval_status` from current login semantics.
   - non-null `email_verified_at` -> `approval_approved`
   - null `email_verified_at` -> `approval_pending`
4. Backfill `users.account_status` conservatively.
   - default existing users to `active`
   - do not infer suspension or deletion from donor/guardian profile flags alone
   - leave user-level deletion marker unset unless explicit account-wide evidence exists
5. Backfill `users.phone` only for unambiguous single-source matches.
   - conflicting or missing values remain null
   - `phone_verified_at` remains null
6. Backfill guardian linkage state.
   - no pivot rows -> `unlinked`
   - clean pivot ownership -> `linked`
   - conflicts -> `pending_review`
   - do not auto-backfill `revoked` from lifecycle flags alone
7. Classify role/profile consistency rows.
   - apply only unambiguous role additions if the later implementation requires them
   - otherwise preserve the current rows and record review buckets before route changes
8. Create new donor-domain tables empty.
   - do not migrate legacy donor transactions into them before first rollout
9. Only after classification and validation should later prompts switch read paths and route eligibility to the new fields.

## Rollback-Safe Migration Strategy

- Separate schema DDL, data backfill DML, and read-path changes into different prompts or deploy steps.
- Make every backfill idempotent and rerunnable by primary-key range or deterministic filters.
- Save read-only classification output before writing backfilled values so ambiguity buckets can be reviewed without re-querying changing data.
- Write only new columns during backfill.
  - do not clear `email_verified_at`
  - do not clear donor/guardian profile flags
  - do not remove pivot rows
  - do not rewrite invoice ownership
- Keep old reads available until later validation confirms the new fields and review buckets are correct.
- Treat ambiguous rows as safe no-op/manual-review candidates, not as forced automatic matches.
- For donor schema:
  - rolling back is safe because new donor tables start empty
  - no existing financial or receipt rows are rewritten
- Destructive cleanup of legacy columns should remain deferred until after prompt-29 through prompt-39 style adoption is validated.

## Deferable Vs Non-Deferable Migration Work

### Non-Deferable Before The Relevant Rollouts

- initial `users.approval_status` backfill
- initial `users.account_status` backfill
- explicit decision to leave user-level deletion mostly unset unless there is true account-wide evidence
- explicit `users.phone` backfill policy
  - unambiguous copy only
  - conflicts remain null
- guardian linkage-state backfill from pivot plus invoice cross-checks
- role/profile mismatch reporting for user-linked donor and guardian profiles before later donor/guardian eligibility rollout
- explicit decision that new donor tables start empty and do not depend on legacy donor transaction conversion

### Deferable To Later Prompts Or Later Hardening

- legacy donor `transactions` to `donation_records` or portal-history bridge work
- auto-linking donor or guardian profiles to users by matching email or mobile
- backfilling ambiguous phone values into `users.phone`
- Google external identity data
- multi-role context-preference persistence
- later guest-claim audit persistence
- dropping or normalizing old donor/guardian profile flags
- any destructive cleanup of `email_verified_at` semantics

## Contradiction / Blocker Pass

- No contradiction with prompts 12 through 23 was found.
- Prompt-23's schema plan is preserved exactly: prompt-24 adds interpretation and ordering only, not new schema scope.
- Prompt-18's route-level vs object-level rule is preserved by keeping guardian profile linkage and guardian-student ownership review separate.
- Prompt-16 and prompt-17's multi-role boundaries are preserved by treating role/profile mismatches as review buckets, not as auto-merged access.
- Prompt-20 through prompt-22 remain preserved and schema-neutral.
- No correction pass is required.
- No hard blocker prevents prompt-25.
