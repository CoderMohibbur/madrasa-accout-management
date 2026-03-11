# Risks

- The live repo still blocks login on null `email_verified_at`, so prompt-07 must redesign verification and authentication semantics before any implementation can safely match the frozen donor and guardian business rules.
- The live repo still uses `verified` plus `role:*` route entry and guardian-first dashboard redirect behavior. Any later implementation that assigns donor or guardian roles too early would misroute new registrants into protected or broken surfaces.
- Existing donor records and future self-registered donor profiles will need duplicate-safe linking rules to avoid split donation history or accidental attachment to the wrong account.
- Guardian claim matching remains high risk because unsafe auto-linking by loose email, phone, or name similarity could expose student, invoice, receipt, or payment data.
- Guest donation, unverified-access redesign, dual verification, and Google sign-in are still later-phase deltas. Later prompts must extend this model instead of pretending those capabilities already exist in the repo.
