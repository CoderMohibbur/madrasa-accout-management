# Decisions

- The mandatory schema foundation is limited to three areas: shared account-state fields on `users`, explicit guardian linkage-state fields, and donor-domain `donation_intents` plus `donation_records`.
- `email_verified_at` remains the email-verification field and must not keep serving as the approval gate once the new account-state columns exist.
- Guardian schema must model both route-level linkage state and object-level guardian-student linkage state so informational access and protected ownership remain separate.
- Existing `payments` and `receipts` tables are reusable for donor rollout; prompt-23 does not approve a payment-table rewrite.
- No mandatory multi-role table is required; multi-role remains derived from shared account state plus donor/guardian profile eligibility, with context-preference persistence deferred as optional later work.
