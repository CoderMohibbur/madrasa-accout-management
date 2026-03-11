# Final Business Rules

## Core Rules

- One authenticated `users` account is the only account model and may hold donor and guardian roles simultaneously.
- Roles express domain potential only; final donor portal access, guardian informational access, guardian protected access, and shared-home behavior derive from explicit eligibility rules.
- Approval, email verification, phone verification, lifecycle, portal eligibility, linkage, and deletion remain separate state dimensions.
- Registration creates only a base account plus domain intent or a non-eligible draft profile; it never auto-grants portal eligibility or guardian linkage.
- Guest donation stays distinct from identified donation and keeps mandatory operational traceability with optional human identity fields.
- Donor payment ability is separate from donor portal eligibility, and narrow receipt/status access is separate from full donor history access.
- Guardian informational access and guardian protected access are separate approved products; protected access requires linkage and object authorization.
- Admission scope remains informational-only with one external application handoff boundary.

## Account-State Model

- Base axes: approval status, email verification, phone verification, account status/lifecycle, deletion, role membership, donor profile state, guardian profile state, and guardian linkage state.
- Derived states: `public_only`, `donor_intent_pending`, `donor_profile_present_inactive`, `donor_portal_eligible`, `guardian_intent_pending`, `guardian_profile_present_unlinked`, `guardian_informational_eligible`, `guardian_protected_eligible`, and `multi_context_eligible`.
- `email_verified_at` remains the email-proof field and must stop acting as the long-term approval or portal proxy once shared account-state columns land.
- Shared-home behavior must derive from eligible contexts, not raw role ordering.

## Donor Model

- Canonical donor identities are `guest donor`, `identified donor`, and `anonymous-display donor`.
- Approved donor flow: `donation_intent -> payments -> donation_record -> receipt`.
- Guest donation creates no account and no donor profile by default.
- Identified donation may link to a known `user_id`, but payment completion never auto-grants donor portal eligibility.
- Transaction-specific receipt/status access ships earlier than donor portal history.
- Guest claim/account-link, recurring donations, saved payment methods, and legacy donor-history conversion remain later work.

## Guardian And Multi-Role Model

- Guardian self-registration and guardian intent capture create only an unlinked informational-state guardian record.
- Guardian informational scope is non-sensitive and separate from the protected guardian portal.
- Protected guardian scope requires guardian profile eligibility, linkage, and object-level ownership.
- Multi-guardian families remain object-scoped rather than family-wide broad permissions.
- One account may hold donor and guardian roles simultaneously, but donor-owned and guardian-owned data remain isolated.
- `/donor` and `/guardian` stay as explicit context routes, with one-context redirect, multi-context chooser, and zero-context fallback behavior.
