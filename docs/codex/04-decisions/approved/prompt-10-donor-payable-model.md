# Prompt 10 Donor Payable Model

Approved baseline decisions from prompt-10:

- Donor online payments must not be finalized directly against legacy `transactions`.
- A dedicated pre-settlement `donation_intent` should act as the donor-side payable for `payments` in the minimal safe rollout.
- The minimal safe rollout does not require a second separate `donation_payable` table beyond that `donation_intent`.
- `payments` remains the attempt table for both guest and identified donor flows, with nullable `user_id` support for guest attempts.
- A separate `donation_record` is required as the post-settlement source of truth for completed donor payments.
- Receipts remain payment-specific and post-settlement, with transaction-specific access narrower than donor portal receipt-history access.
- Donor settlement remains separate from legacy accounting posting, which may be deferred or retried independently.
- The smallest safe donor payment rollout is one-time guest plus identified online donation, with no recurring billing and no saved payment methods.
