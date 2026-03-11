# Risks

- Donor checkout remains sandbox-only because live shurjoPay mode is still intentionally disabled.
- Prompt-34 does not add donor manual-bank fallback; only the gateway-backed donor payable flow is live in this slice.
- Prompt-35 must bridge donor portal/history carefully so new `donation_records` do not double-count against legacy donor `transactions`.
- Guest and identified transaction-specific access now relies on `public_reference` plus access key or authenticated ownership; losing that opaque material still requires support/help rather than broad history access.
- Runtime Git validation remains limited because `git` is unavailable in the current shell environment.
