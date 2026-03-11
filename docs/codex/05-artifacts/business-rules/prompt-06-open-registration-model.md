# Prompt 06 Open Registration Model

## Model Summary

- One account model
- One unified registration backend
- Optional public, donor, and guardian entry pages that only preselect intent
- Registration outcome: base identity plus zero or more domain intents
- Registration alone never grants donor portal eligibility, guardian linkage, or protected access
- The model stays compatible with donor access without universal verification and guardian informational access before linkage

## Safe Registration States

- `public_only`
- `donor_intent_pending`
- `donor_profile_present_inactive`
- `guardian_intent_pending`
- `guardian_profile_present_unlinked`

## Flow Rules

- General or public:
  - create the base account
  - do not require any donor or guardian profile
  - land on a neutral authenticated home
- Donor:
  - capture donor intent on the same account
  - optionally create one inactive donor profile
  - do not expose donor portal data until donor eligibility is explicitly complete
- Guardian:
  - capture guardian intent on the same account
  - optionally create one unlinked guardian profile
  - allow only future informational access until linkage and authorization succeed

## Expansion Rules

- Add donor or guardian later inside the same authenticated account
- Never auto-merge records by loose name matching
- Never auto-link guardian or student-owned data from registration alone
- Multi-role access is earned by separate eligible contexts and later explicit context switching

## Current-State Caveats

- Live registration still sets `email_verified_at = null`, suppresses `Registered`, and blocks login on null `email_verified_at`
- Live routes still use `verified` plus `role:*` for donor and guardian portals
- Guest donation, dual verification, unverified-access redesign, and Google sign-in remain later-phase deltas
