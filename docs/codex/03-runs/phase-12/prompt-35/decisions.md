# Decisions

- Prompt-35 implements donor slices `A2` -> `O1` -> `H1` only; guest claim/link, Google sign-in, guardian rollout, and multi-role chooser work remain deferred.
- Donor route entry now derives from donor-domain context and account state, not from blanket `verified` middleware plus raw `role:donor`.
- Donor payment ability remains broader than donor portal eligibility; donor-only accounts may reach a donor no-portal state without gaining history access.
- Donor-only accounts with donor context now use `/donor` as their safe post-login and post-email-verification landing while multi-role chooser behavior remains deferred.
- The donor portal root has two safe outcomes: portal-eligible dashboard or donor no-portal state; `/donor/donations` and `/donor/receipts` stay portal-only read paths.
- Portal-eligible donor history now includes both legacy donor `transactions` and new `donation_records`, but their provenance stays explicit and they are not converted or merged silently.
- Donor receipt history includes donor-specific legacy receipts plus `DonationIntent` receipts only; prompt-35 does not bridge guardian invoice receipts into donor views.
- Prompt-34's `/donate` checkout/status flow, narrow guest/identified access rules, and no-auto-portal side effects remain intact.
