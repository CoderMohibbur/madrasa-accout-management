# Prompt 02 Current System Baseline

Approved baseline decisions from prompt-02:

- The live implemented codebase is the authoritative baseline for later prompts.
- Auth remains single-guard and user-centric, with `email_verified_at` acting as both verification and approval gate.
- Guardian payment capability is invoice-backed; donor portal capability remains read-only.
- `config/payments.php` is the current payment/provider config surface.
- Legacy management compatibility still relies on `management.surface`, so later prompts must not assume a full management-role-only cutover.
