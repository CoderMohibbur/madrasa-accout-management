# Decisions

- Donor implementation is approved as separate slices rather than one donor wave: `G1`, `P1`, `P2`, `G2`, `A1`, `A2`, `O1`, `H1`, and later optional `C1`.
- Prompt-33 should be limited to the `G1` public guest-donation entry shell and must not add unsafe legacy finalization, silent account creation, or silent donor-profile creation.
- Prompt-34 should implement `P1` -> `P2` -> `G2` -> `A1` so guest and identified checkout share the same dedicated donor payable model.
- Prompt-35 should implement `A2` -> `O1` -> `H1` so donor auth/account behavior, donor portal eligibility, and donor portal history remain separate concerns.
- Guest donation remains payment-only by default, and identified donation remains account-linked only; neither path auto-grants donor portal eligibility.
- Transaction-specific guest or identified receipt/status access remains narrower than donor portal receipt/history browsing.
- Claim/account-link behavior is not part of the initial donor wave and remains a later optional slice that requires explicit approval.
- Google sign-in remains outside donor slice activation and must stay deferred to its own later approved work.
