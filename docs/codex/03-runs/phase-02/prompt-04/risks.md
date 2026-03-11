# Risks

- `email_verified_at` is currently doing too much, so any later prompt that changes auth or portal gating without first separating that field risks breaking login, verification, and approval behavior at once.
- The route-level `verified` middleware is currently spread across donor, guardian, dashboard, and payment routes, so future rule changes will ripple widely unless portal eligibility is separated first.
- Donor and guardian lifecycle flags live on profile tables instead of a shared account-state model, which can produce inconsistent access outcomes across multi-role users.
- Guardian linkage is currently inferred rather than explicitly modeled, which makes it easy to leak protected access when later prompts widen onboarding or verification paths.
- Legacy management compatibility for unroled users remains a hidden state and could be broken accidentally if later prompts assume pure role-based management access too early.
