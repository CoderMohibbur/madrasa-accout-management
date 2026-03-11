# Next Step

Run `docs/codex/01-prompts/prompt-36-guardian-informational-portal.md` next.

Carry forward:
- preserve prompt-35's donor access separation: donor login/no-portal behavior stays distinct from donor portal eligibility
- preserve prompt-34's `donation_intent -> payment -> donation_record -> receipt` flow and the narrow `/donate` checkout/status access rules
- keep donor history provenance explicit; do not convert or silently merge legacy donor `transactions` into the new donor-domain model
- keep guardian invoice payment behavior intact and additive-only while prompt-36 introduces guardian informational access
- do not pull multi-role chooser logic, Google sign-in, donor claim/link, or guardian protected data/payment behavior forward under prompt-36
