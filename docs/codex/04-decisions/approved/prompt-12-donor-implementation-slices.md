# Prompt 12 Donor Implementation Slices

Approved baseline decisions from prompt-12:

- Donor implementation must be split into these ordered slices: `G1` public donor entry shell, `P1` donation domain schema foundation, `P2` donor payment-domain service foundation, `G2` live guest checkout activation, `A1` identified account-linked donation entry, `A2` donor auth/account behavior separation, `O1` donor portal boundary adaptation, `H1` receipt/history eligibility bridge, and later optional `C1` guest-claim/account-link.
- Prompt-33 is limited to `G1` and must not introduce unsafe donor finalization, silent account creation, silent donor-profile creation, or donor portal auto-enablement.
- Prompt-34 should implement `P1` -> `P2` -> `G2` -> `A1` so guest and identified donors share one safe donor payable model.
- Prompt-35 should implement `A2` -> `O1` -> `H1` so donor account behavior, donor portal eligibility, and donor portal read paths remain separate layers.
- Guest donation stays payment-only by default, and identified donation stays account-linked only under the approved identity-capture rules.
- Transaction-specific receipt/status access ships earlier than donor portal receipt/history browsing.
- Claim/account-link behavior remains later optional work and is not part of the initial donor implementation wave unless explicitly approved.
- Google sign-in remains a separate later delta and is not part of donor slice activation.
