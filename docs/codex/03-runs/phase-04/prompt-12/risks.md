# Risks

- Implementing prompt-33 as more than the approved `G1` shell would risk pushing live donor finalization back onto legacy `transactions` before the dedicated donor payable foundation exists.
- `A2` and `O1` depend on prompts 29-32; if donor auth or donor portal gating is changed earlier, the repository is likely to re-entrench the same `email_verified_at` and `verified` coupling that prompt-04 and prompt-07 rejected.
- `H1` needs a strict provenance rule so new `donation_record` history and legacy `transactions` history do not double-count or mislabel the same donor money movement.
- Guest flows that allow no email and no phone remain intentionally self-service-limited; lost opaque references will still require narrow manual-support handling.
- `C1` carries the highest ownership-takeover risk in the donor slice plan because weak contact matching could accidentally attach donations to the wrong account if the proof rules are loosened.
- Real donor live-payment activation still depends on replacing the documented payment placeholders with real provider values before production use.
