# Risks

- The current route and policy layer is still `auth` / `verified` oriented, so later implementation prompts must redesign status and receipt access for guests and verification-independent donors without weakening ownership checks.
- The current donor portal still reads history primarily from legacy `transactions`, so later prompts must define how new settled donor records appear in donor history without corrupting legacy reports.
- Guest receipt recovery needs an opaque-reference strategy strong enough for public access without relying on registration or weak contact matching.
- Timeout and retry rules need concrete implementation-time windows so stale attempts, duplicate callbacks, and superseded provider references cannot be replayed.
- Live payment activation remains separately constrained by provider-contract and secret-replacement work already captured in the broader payment risk register.
