# Risks

- Prompt-41 still needs to centralize the public admission CTA onto the same canonical config/helper path; prompt-36 intentionally limited config-backed consumption to guardian informational surfaces.
- The repository working tree was already dirty during prompt-36 validation, so commit-level isolation remains an operational risk outside the product behavior itself.
- Final dedicated guardian-informational and guardian-protected eligibility middleware remains later route-hardening work; prompt-36 uses additive redirect/read-path logic only.
