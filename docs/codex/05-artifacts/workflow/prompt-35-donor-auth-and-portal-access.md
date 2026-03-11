# Prompt 35 Donor Auth And Portal Access

## Route Surface

- `GET /donor`
- `GET /donor/donations`
- `GET /donor/receipts`

## Access States

- `portal_eligible`
  - donor profile exists
  - `portal_enabled = true`
  - `isActived = true`
  - `isDeleted = false`
  - result: full read-only donor portal
- `donor_no_portal`
  - donor context exists through donor profile, donor role, or identified donor history
  - portal eligibility is still off
  - result: donor no-portal state on `/donor`, but no history or receipt browsing
- `forbidden`
  - no donor context or account state is not accessible
  - result: fail closed

## Redirect Rules

- donor-only accounts with donor context may default to `donor.dashboard` after login
- donor-only accounts with donor context may also resolve to `donor.dashboard` when already verified or after email verification completes
- guardian and management routing remain unchanged in this step
- multi-role chooser behavior remains deferred

## History Provenance Rules

- legacy donor history source:
  - `transactions` rows with donor linkage and donation transaction type
- new donor history source:
  - `donation_records`
- portal rendering requirement:
  - show both sources together
  - keep source provenance visible
  - do not convert legacy `transactions` into new donor-domain records

## Receipt Rules

- donor portal receipts may include:
  - receipts tied to `DonationIntent`
  - donor-scoped legacy receipts using the current legacy null-payable pattern
- donor portal receipts must exclude:
  - guardian invoice receipts
  - guest account-wide browsing
  - non-portal donor receipt history browsing

## Preserved Boundaries

- prompt-34 `/donate` checkout/status flow remains unchanged
- guest transaction-specific access remains narrow
- identified donation still does not auto-grant donor portal eligibility
- guardian invoice settlement logic remains untouched
- claim/link, Google sign-in, and multi-role chooser work remain later prompts
