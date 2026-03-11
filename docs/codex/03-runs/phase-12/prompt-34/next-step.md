# Next Step

Run `docs/codex/01-prompts/prompt-35-donor-auth-and-portal-access.md` next.

Carry forward:
- preserve the approved prompt-33 guest entry shell and prompt-34 donor payable foundation exactly as implemented
- keep `donation_intent -> payments -> donation_record -> receipt` as the donor-domain truth and do not route donor settlement through legacy `transactions`
- preserve prompt-31 registration behavior and prompt-32's separate email/phone trust axes
- keep guest and identified transaction-specific status/receipt access narrow; donor portal history remains a separate prompt-35 read-path concern
- do not reopen prompt-33 or prompt-34 unless a real contradiction is found
- do not pull guardian changes, donor manual-bank rollout, guest claim/account-link, or legacy history conversion forward under prompt-35
