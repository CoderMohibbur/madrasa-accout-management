# Decisions

- Do not finalize donor online payments directly against legacy `transactions`; donor settlement must use a dedicated donor-domain payable path.
- Use one dedicated pre-settlement `donation_intent` as the donor-side payable object for `payments`; the minimal safe rollout does not need a second separate `donation_payable` table.
- Reuse `payments` as the payment-attempt table for both guest and identified donor flows, with `user_id` nullable for guest attempts.
- Create a separate post-settlement `donation_record` only after authoritative payment verification or manual approval.
- Keep `anonymous-display donation` as a visibility flag on guest or identified donations, not as a third ownership class.
- Keep receipts payment-specific and post-settlement; transaction-specific receipt access remains separate from donor portal eligibility and donor receipt-history access.
- Keep donor payment finalization separate from legacy accounting posting; posting may be deferred, retried, skipped, or added later without changing donor settlement truth.
- The smallest safe live donor payment rollout is one-time guest plus identified online donation with narrow status/receipt access, no recurring billing, no saved payment methods, and no dependency on legacy transaction posting.
