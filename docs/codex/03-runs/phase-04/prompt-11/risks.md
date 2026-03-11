# Risks

- The current repo has no internal guest donation UI, no public payment status route, and no public claim route yet, so later implementation prompts must add those surfaces without weakening payment ownership protections.
- The current auth model is still email-first and approval-overloaded, so any later lightweight-account flow must be adapted carefully to the frozen prompt-06 and prompt-07 target rules rather than copied from current login behavior.
- Phone-only guest donations remain self-service-limited in the smallest safe rollout because phone is not yet the canonical login identifier or a completed account-verification channel.
- Later donor portal history design must decide how claimed guest donations appear in donor history without forcing premature donor-profile creation or reusing unsafe legacy `transactions` shortcuts.
- Guest receipt recovery remains intentionally narrow when no contact information is supplied; support tooling will need clear manual recovery boundaries.
