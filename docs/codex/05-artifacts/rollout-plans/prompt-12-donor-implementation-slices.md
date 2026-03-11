# Prompt 12 Donor Implementation Slices

## Slice Order

1. `G1` - public donor entry shell
2. `P1` - donation domain schema foundation
3. `P2` - donor payment-domain service foundation
4. `G2` - live guest checkout activation
5. `A1` - identified account-linked donation entry
6. `A2` - donor auth/account behavior separation
7. `O1` - donor portal boundary adaptation
8. `H1` - receipt/history eligibility bridge
9. `C1` - optional guest-claim/account-link flow

## Prompt Mapping

- Prompt-33 -> `G1`
- Prompt-34 -> `P1`, `P2`, `G2`, `A1`
- Prompt-35 -> `A2`, `O1`, `H1`
- Later optional work only if approved -> `C1`

## Slice Matrix

| Slice | Area | Primary type | Smallest safe goal | Independent ship |
| --- | --- | --- | --- | --- |
| `G1` | Guest donation | UI-first | Internal donor landing plus guest vs identified branching and approved identity-capture shell only | Informational/pre-activation only |
| `P1` | Donor payable | Schema-first | Add `donation_intent` and `donation_record` foundations with opaque guest retrieval fields | Yes |
| `P2` | Donor payable | Service-first | Add safe donor payable initiation/finalization logic without disturbing guardian invoice flows | Yes |
| `G2` | Guest donation | Integration-first | Activate guest checkout on top of the donor payable domain with opaque status/receipt access | Yes |
| `A1` | Donor account/auth | Route-first | Let authenticated accounts donate without donor portal eligibility | Yes |
| `A2` | Donor account/auth | Service-first | Separate donor login/no-portal behavior from blanket verification and portal assumptions | Yes, after prompts 29-32 |
| `O1` | Donor portal | Service-first | Gate `/donor` by derived donor eligibility while keeping it read-only | Yes |
| `H1` | Receipt/history | Service-first | Bridge new donor-domain history/receipts into the portal while keeping guest and non-portal access narrow | Yes |
| `C1` | Claim/link | Route-first | Allow one-by-one proven guest claim into an account with no auto-portal side effect | Approval-gated only |

## Rollback Checkpoints

1. After `G1`: no payment-domain writes or provider calls exist.
2. After `P1`: schema is present but dark.
3. After `P2`: donor services are present but dark.
4. After `G2`: guest donation is live with transaction-specific access only.
5. After `A1`: identified donation is live without portal changes.
6. After `A2`: donor no-portal account behavior is separated from blanket verification rules.
7. After `O1`: donor portal gating is corrected, but the portal stays read-only.
8. After `H1`: portal-eligible donors can see new donor-domain history and receipts.
9. After `C1`: one-by-one claim is live; bulk or automatic linking remains off.
