# Next Step

Run `docs/codex/01-prompts/prompt-37-guardian-protected-portal-gating.md` next.

Carry forward:
- preserve the new `/guardian/info*` route space as non-sensitive guardian informational-only surfaces
- keep the existing protected `/guardian` routes, guardian invoice/payment behavior, and route names intact while prompt-37 adapts protected gating
- preserve prompt-35 donor access separation and donor history provenance; do not reopen donor checkout or donor claim/link work
- keep the external admission CTA limited to approved guardian informational surfaces; do not spread it onto auth or protected guardian pages
- reuse the guardian informational access-state logic instead of reintroducing blanket `verified` + raw role coupling
