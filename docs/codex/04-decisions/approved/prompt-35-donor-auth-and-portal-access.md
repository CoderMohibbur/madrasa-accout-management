# Prompt 35 Donor Auth And Portal Access

Approved implementation decisions from prompt-35:

- Implement only donor slices `A2`, `O1`, and `H1` in this run.
- Donor route access is derived from donor-domain context plus shared account-state eligibility rather than blanket `verified` middleware plus raw `role:donor`.
- Donor-only accounts with donor context may land on `/donor` after login or email-verification checks even when full donor portal eligibility is still off.
- `/donor` has two safe outcomes:
  - portal-eligible donors get the read-only donor dashboard
  - donor-context but non-eligible accounts get a donor no-portal state
- `/donor/donations` and `/donor/receipts` remain portal-only history views and do not widen into non-portal donor browsing.
- Portal eligibility still requires an explicit donor profile with `portal_enabled = true`, `isActived = true`, and `isDeleted = false`; identified donation completion alone does not satisfy that rule.
- Portal donor history now includes both legacy donor `transactions` and prompt-34 `donation_records`, but their provenance remains explicit and no legacy conversion shortcut is introduced.
- Donor receipt history includes prompt-34 `DonationIntent` receipts plus donor-scoped legacy receipts only; guardian invoice receipts remain outside donor views.
- Prompt-34's `/donate` flow, guest access rules, identified donation rules, and no-auto-portal side effects remain intact.
