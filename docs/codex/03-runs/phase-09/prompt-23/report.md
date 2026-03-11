# Report

This prompt converts the approved account-state, donor, guardian, verification, guest-donation, and multi-role analysis into one additive schema plan. The live repository already has reusable `roles`, `guardians`, `guardian_student`, `payments`, `receipts`, `audit_logs`, and `student_fee_invoices` tables, so prompt-23 does not reopen that groundwork. The remaining schema work is narrower and more specific: separate account-state from `email_verified_at`, make guardian linkage explicit instead of inferred, and add donor-domain settlement tables that keep donor money flow out of legacy `transactions`.

Prompts 20, 21, and 22 remain preserved as carry-forward UI inputs only. They influence rollout ordering, but they do not add new schema requirements.

## Current Schema Baseline Reused

- `users` still contains only `name`, `email`, `email_verified_at`, `password`, and timestamps.
- `donors` and `guardians` already have `user_id`, `portal_enabled`, `isActived`, and `isDeleted`, so domain-profile presence is partially modeled already.
- `guardian_student` exists, but today linkage is expressed only by row presence, not by an explicit approved/pending/revoked state.
- `payments` already supports nullable `user_id` and polymorphic `payable`, which is compatible with both guest and identified donor payments.
- `receipts.issued_to_user_id` is already nullable, which is compatible with guest-donation receipts.
- `roles` plus `role_user` already allow one `users` account to hold both donor and guardian roles at the same time.

## Mandatory Schema Changes

### 1. Shared Account-State Foundation On `users`

Add additive account-state columns to `users` so approval, lifecycle, and phone verification stop riding on `email_verified_at` or profile flags.

Minimum required fields:

- `approval_status`
- `account_status`
- `phone`
- `phone_verified_at`
- one user-level deletion marker such as `deleted_at` or an equivalent account soft-delete column

Keep as-is:

- `email_verified_at`
  - it remains the email-verification axis only
  - it must stop acting as the approval gate

Why this is mandatory:

- prompt-04 requires approval, verification, lifecycle, deletion, and eligibility to be separate axes
- prompt-07 requires phone verification to be an optional account-level concern, not a profile-level guess
- prompt-18 requires later donor, guardian-informational, shared-home, and guest-donation routing to stop depending on blanket `verified`
- prompt-29 through prompt-32 cannot safely adapt auth and read paths unless these user-level fields exist first

What this does not require:

- no new auth guard
- no separate account table
- no multi-role-specific table

### 2. Guardian Linkage-State Normalization

Add explicit linkage state so guardian informational eligibility and guardian protected eligibility stop being inferred from a raw pivot row or invoice ownership.

Minimum required additions:

- a profile-level linkage state on `guardians`
  - example values: `unlinked`, `pending_review`, `linked`, `revoked`
- an object-level linkage state on `guardian_student`
  - so protected student access is not inferred from pivot existence alone
- linkage timestamps where needed
  - for example `linked_at`, `revoked_at`

Why this is mandatory:

- prompt-13 and prompt-15 require guardian informational and guardian protected access to stay distinct
- prompt-18 requires route-level guardian eligibility to remain separate from object-level ownership/policy protection
- prompt-36 and prompt-37 cannot cleanly implement the approved guardian split if linkage remains only an implied side effect of pivot rows

What should remain during the additive rollout:

- keep `portal_enabled`, `isActived`, and `isDeleted` on `guardians` for compatibility until later read-path and backfill work is complete

### 3. Donor Donation-Domain Settlement Tables

Create the approved donor-domain tables that let donor checkout settle safely without turning legacy `transactions` into the donor payment source of truth.

#### `donation_intents`

Required shape:

- `user_id` nullable FK to `users`
- `donor_id` nullable FK to `donors`
- `donor_mode`
- `display_mode`
- `amount`
- `currency`
- `status`
- `public_reference` unique
- `guest_access_token_hash` or equivalent opaque proof material
- `name_snapshot` nullable
- `email_snapshot` nullable
- `phone_snapshot` nullable
- optional metadata / consent / expiry fields
- timestamps

#### `donation_records`

Required shape:

- `donation_intent_id` unique FK
- `winning_payment_id` unique FK to `payments`
- `user_id` nullable FK to `users`
- `donor_id` nullable FK to `donors`
- `donor_mode`
- `display_mode`
- `amount`
- `currency`
- `donated_at`
- `posting_status`
- contact snapshot fields or equivalent operational snapshot payload
- timestamps

Why this is mandatory:

- prompt-10 froze `donation_intent` as the donor-side payable and `donation_record` as the post-settlement donor truth
- prompt-11 requires guest and identified donor flows to share one safe donor payment model
- prompt-12 makes `P1` the donor schema-first slice for prompt-34
- without these tables, donor payment would be forced back into legacy `transactions`, which prompt-10 rejected

What does not need redesign here:

- `payments`
  - already supports nullable `user_id` and polymorphic payables
- `receipts`
  - already supports guest-compatible nullable `issued_to_user_id`
- guardian invoice payment schema
  - must remain intact and separate

### 4. Supporting Indexes And Constraints For The New Surfaces

These are part of the mandatory schema slice, even though they are not new business tables by themselves:

- unique `donation_intents.public_reference`
- strong unique or lookup-safe proof material for guest retrieval
- indexes on new donor ownership/status columns used by history and status lookup
- indexes on new guardian linkage-state columns used by guardian informational/protected routing and policy queries

## Optional Schema Changes

### 1. External Identity Linkage Table For Google Sign-In

Later prompt-38 can add a dedicated `external_identities` table or equivalent linkage model with:

- `provider`
- `provider_subject`
- `user_id`
- provider email snapshot
- provider email-verified flag
- link / last-used timestamps

This is optional for prompt-23 because prompt-08 keeps Google sign-in as a later additive feature, not a blocker for the main donor/guardian rollout.

### 2. Multi-Role Context Preference Persistence

An optional context-preference field or small `user_portal_preferences` table may later remember:

- last-used context
- preferred landing context

This is not a blocker because prompt-16 and prompt-17 allow the first chooser/switcher rollout to stay eligibility-derived and stateless.

### 3. Later Guest-Claim / Claim-Audit Persistence

If the later optional `C1` slice needs richer proof tracking, the project may add a `donation_claims` table or equivalent audit structure.

This is optional because the first safe claim behavior can be built from:

- `donation_intents.public_reference`
- `guest_access_token_hash`
- authenticated claimant identity
- existing `audit_logs`

### 4. Approval Governance And Profile-Normalization Niceties

These are useful later, but not blockers for the first additive rollout:

- `approved_by_user_id`
- `approval_decided_at`
- `approval_reason`
- profile-level enum replacements for `portal_enabled`, `isActived`, and `isDeleted`
- direct `receipt_id` convenience on `donation_records`

## Blocker Vs Later-Change Classification

### Schema Blockers For The Approved Implementation Sequence

- shared account-state fields on `users`
  - blocker for prompt-29 through prompt-32
  - therefore also a blocker for prompt-35, prompt-36, and prompt-39 follow-on eligibility work
- explicit guardian linkage-state additions
  - blocker for prompt-36 and prompt-37
- `donation_intents` and `donation_records`
  - blocker for prompt-34 and the donor history bridge inside prompt-35

### Later Enhancements, Not Rollout Blockers

- `external_identities`
  - later prompt-38 only
- context preference persistence
  - convenience for prompt-39 or prompt-42, not required for the first chooser rollout
- `donation_claims` or richer claim-proof persistence
  - later optional `C1`, not required for prompt-34 or prompt-35
- profile-flag consolidation
  - later hardening, not required before additive account-state rollout

### Explicit Non-Blocker Clarification

- prompts 20, 21, and 22 do not introduce any new schema blockers
- they remain preserved as rollout-order and UI-map inputs only

## Migration Safety Notes

- Use additive migrations only.
- Do not edit historical migrations.
- Do not rename or drop `email_verified_at` in the first schema pass.
- Do not remove `portal_enabled`, `isActived`, or `isDeleted` from donor or guardian tables in the first schema pass.
- Introduce new user-state columns first, then interpret/backfill them in prompt-24, then adapt read paths in prompt-30 and later prompts, then consider cleanup only after the new paths are stable.
- Do not couple donor settlement tables to legacy `transactions`.
- Do not add first-pass donor schema that forces payment success to depend on legacy accounting posting.
- Reuse the existing `payments.payable` polymorphism instead of rewriting the payment attempt model.
- Protect guest retrieval material by storing only hashes or other opaque non-guessable proof artifacts, never raw reusable secrets.
- Avoid first-pass uniqueness on `users.phone`; prompt-07 keeps phone optional and unverified by default, and current profile data may be duplicate or incomplete.
- Avoid database-enforced not-null or uniqueness rules that depend on legacy-data interpretation until prompt-24 has classified the existing rows safely.
- Keep guardian route-level eligibility and object-level ownership separate while migrating:
  - guardian profile linkage state drives informational vs protected routing
  - guardian-student linkage state drives object authorization

## Default / Nullability Strategy

### `users`

- `approval_status`
  - add nullable-first for existing rows
  - let prompt-24 classify/backfill before the app treats it as the sole source of truth
- `account_status`
  - add nullable-first for existing rows
  - backfill before route or middleware reads switch over
- `phone`
  - nullable
- `phone_verified_at`
  - nullable
  - `null` means absent or not yet verified
- user-level deletion marker
  - nullable
  - absence means not deleted
- `email_verified_at`
  - keep nullable as-is

### `guardians` And `guardian_student`

- new linkage-state columns
  - add nullable-first for existing rows
  - new writes should set explicit values immediately
- `linked_at` / `revoked_at`
  - nullable
- current profile lifecycle booleans
  - keep current defaults and behavior until later migration and read-path work completes

### `donation_intents`

- `user_id`
  - nullable
- `donor_id`
  - nullable
- `name_snapshot`, `email_snapshot`, `phone_snapshot`
  - nullable
- `donor_mode`
  - required
- `display_mode`
  - required
  - safe default `identified`
- `currency`
  - required
  - safe default `BDT`
- `status`
  - required
  - safe default such as `open` or `pending`
- `public_reference`
  - required and unique
- `guest_access_token_hash`
  - required for guest flows
  - may remain nullable for identified-only flows if that path does not need public retrieval

### `donation_records`

- `donation_intent_id`
  - required and unique
- `winning_payment_id`
  - required and unique
- `user_id`
  - nullable
- `donor_id`
  - nullable
- contact snapshot fields
  - nullable
- `posting_status`
  - required
  - safe default such as `pending`, `skipped`, or `not_requested`
- direct `receipt_id`
  - optional later convenience only
  - if added later, keep it nullable initially

### Optional Later Tables

- `external_identities.provider_email_verified`
  - default `false`
- context-preference tables
  - nullable-friendly and additive
- claim-audit tables
  - nullable-friendly and additive

## Contradiction / Blocker Pass

- No contradiction with prompts 12 through 22 was found.
- Prompt-22's UI implementation slice plan remains preserved and schema-neutral.
- Prompt-21's screen/component map remains preserved and schema-neutral.
- Prompt-20's product-family baseline remains preserved and schema-neutral.
- Prompt-18's route-level vs object-level protection rule is preserved by splitting guardian profile linkage from guardian-student authorization state.
- Prompt-19 remains preserved; no internal admission schema was introduced.
- No correction pass is required.
- No hard blocker prevents prompt-24.
