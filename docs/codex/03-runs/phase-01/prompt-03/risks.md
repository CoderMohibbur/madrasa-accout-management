# Risks

- The current repository still ties login approval to `email_verified_at`, which conflicts with the newly frozen donor and guardian unverified-access rules.
- The current donor implementation is read-only and still coupled to legacy `transactions`, so the frozen guest-donation and donor-payment rules exceed the present live capability.
- Optional guest identity attachment creates duplicate-account and unsafe auto-merge risk if later prompts do not define strict matching and ownership rules.
- Multi-role users are currently redirected guardian-first on `/dashboard`, so later prompts must avoid assuming a finished context-switching design already exists.
- Phone verification coexistence and Google sign-in are now frozen requirements but remain absent from the current implementation baseline.
