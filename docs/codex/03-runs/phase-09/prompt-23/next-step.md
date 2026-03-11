# Next Step

Run `docs/codex/01-prompts/prompt-24-data-backfill-migration.md` next.

Carry forward these prompt-23 decisions:

- implement schema additively in the order shared account-state first, guardian linkage explicitness second, donor-domain settlement tables third
- keep `email_verified_at`, donor/guardian profile flags, and existing payment/invoice tables intact during the first schema pass
- treat Google external identities, multi-role context preferences, and guest-claim audit persistence as later optional schema work
- keep guardian route-level eligibility distinct from guardian-student object authorization
- keep donor settlement isolated from legacy `transactions` and reuse the existing `payments` plus `receipts` base tables
