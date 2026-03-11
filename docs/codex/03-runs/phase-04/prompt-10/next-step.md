# Next Step

Run `docs/codex/01-prompts/prompt-11-guest-donation-onboarding.md` next.

Carry forward these prompt-10 rules:

- guest and identified donor checkout share one `donation_intent` plus `payments` attempt model
- donor payment ability remains separate from donor portal eligibility
- guest receipt and payment-status access require an opaque transaction-specific reference
- settled donor truth is `donation_record` plus receipt, not legacy `transactions`
- legacy accounting posting remains a separate downstream concern
