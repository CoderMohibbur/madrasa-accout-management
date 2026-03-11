# Decisions

- Use the live implemented repository, not the older implementation-analysis “missing features” list, as the authoritative baseline for all later codex prompt inventory and planning work.
- Keep treating auth as a single-guard `users`-based model with a custom `email_verified_at` approval gate until a later prompt explicitly analyzes that boundary.
- Treat guardian portal access and online payment capability as invoice-backed and guardian-owned only; donor portal capability remains read-only over linked legacy donation rows and explicit receipts.
- Treat `config/payments.php` as the active payment/provider configuration surface for later prompts; `config/services.php` is not the current source of truth for payments or Google sign-in.
- Preserve the compatibility bridge in `management.surface`: legacy management surfaces are not yet fully cut over to explicit management-role-only access.
