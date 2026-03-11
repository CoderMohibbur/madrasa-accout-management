# Risks

- The live repo still has no account-level phone storage or verification state, so later implementation work will need schema and read-path decisions before phone verification can exist safely.
- The live repo still overloads `email_verified_at` and uses broad `verified` middleware, so later prompts must redesign route and policy gating without collapsing verification back into approval or eligibility.
- A strict verified-phone uniqueness rule is safer for recovery and anti-abuse, but it may create product friction if multiple legitimate users share one household phone number.
- Guest donation contact matching remains high risk; any future account suggestion or donor attachment flow must avoid silent linking based only on shared email or phone values.
- If phone verification later participates in recovery or step-up checks, implementation must avoid creating an SMS-based takeover path that is weaker than the current email-only reset flow.
