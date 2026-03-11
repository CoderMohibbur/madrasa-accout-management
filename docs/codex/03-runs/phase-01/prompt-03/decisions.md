# Decisions

- Freeze the identity model around one authenticated account that can hold donor and guardian roles simultaneously; do not assume separate auth tables or guards.
- Freeze donor access rules so registration, login, and donation do not require completed email or phone verification, while sensitive payment-domain safety and traceability still remain mandatory.
- Freeze guardian access rules so unverified or unlinked guardians may access only a light informational portal until linkage and protected-surface authorization are satisfied.
- Freeze guest donation as a mandatory target behavior with optional human identity fields but mandatory reconciliation, anti-abuse, and reporting traceability.
- Freeze admission scope as informational only with an external application handoff.
- Freeze Google sign-in and full dual email/phone verification support as later-phase required capabilities rather than current-state assumptions.
