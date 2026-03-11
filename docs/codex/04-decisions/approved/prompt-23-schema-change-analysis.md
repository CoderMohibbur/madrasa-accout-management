# Prompt 23 Schema Change Analysis

Approved baseline decisions from prompt-23:

- The mandatory schema foundation is limited to three areas: shared account-state columns on `users`, explicit guardian linkage-state fields, and donor-domain `donation_intents` plus `donation_records`.
- `email_verified_at` remains the email-verification field and must not continue to act as the approval or portal-eligibility proxy once the new account-state schema lands.
- Guardian schema must express both route-level profile linkage state and object-level guardian-student linkage state so informational access and protected ownership remain distinct.
- Existing `payments` and `receipts` tables are sufficient for the minimal donor rollout and should be reused rather than redesigned.
- No mandatory multi-role table is required; multi-role remains derived from shared account-state plus donor/guardian profile eligibility, while context-preference persistence stays optional later work.
- Google external identity linkage, guest-claim audit persistence, and profile-flag normalization are later optional schema improvements, not blockers for prompts 24 through 37.
