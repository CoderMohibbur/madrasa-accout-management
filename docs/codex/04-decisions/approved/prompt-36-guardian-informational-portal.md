# Prompt 36 Guardian Informational Portal

Approved implementation decisions from prompt-36:

- Prompt-36 adds a separate authenticated guardian informational route space at `/guardian/info*`; the existing protected `/guardian` routes remain protected-only in this step.
- Guardian informational access is derived from shared accessible account state plus guardian-domain context through a guardian profile or guardian role, not from blanket `verified` middleware.
- Guardian-only informational accounts may land on `guardian.info.dashboard` after login, verification completion, or `/dashboard` redirect handling when no donor or management context is active.
- Verified protected guardians keep the existing protected `/guardian` default flow; prompt-36 does not reopen guardian protected routing or payment behavior.
- Guardian informational pages may show institution guidance, admission guidance, external handoff, and self-only help/status messaging only; student, invoice, receipt, and payment-sensitive data stay outside this surface.
- The guardian informational admission CTA resolves from `portal.admission.external_url`, accepts only absolute `https://` URLs, and fails closed to neutral messaging when config is missing or invalid.
- Prompt-35 donor no-portal behavior, donor portal history bridging, and prompt-34 donor checkout behavior remain unchanged.
- Prompt-30's dashboard read-path test now carries forward the approved prompt-36 redirect for guardian-role informational users instead of asserting the older fail-closed behavior.
