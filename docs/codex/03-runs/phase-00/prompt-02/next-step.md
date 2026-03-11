# Next Step

Proceed to `docs/codex/01-prompts/prompt-03-business-rule-freeze.md` using the prompt-02 live inventory as the baseline.

Carry forward these frozen assumptions first:

- auth remains single-guard and `email_verified_at` still doubles as approval state
- guardian portal and online payment scope are invoice-backed and guardian-owned
- donor portal is read-only and still coupled to legacy donation transactions plus explicit receipts
- legacy management surfaces still preserve backward-compatible access for unroled users via `management.surface`
- payment configuration and sandbox constraints live in `config/payments.php` and the autopilot risk/handoff artifacts
