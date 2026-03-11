# Prompt 33 Guest Donation Entry

## Entry Points

- `GET /donate`
- `POST /donate/start`

## Prompt-33 Guest Entry Contract

- required:
  - `amount`
- optional:
  - `name`
  - `email`
  - `phone`
  - `anonymous_display`

## Capture Rules

- guest donation remains public and does not require registration
- guest email is normalized to lowercase
- guest phone is normalized with the shared phone helper
- optional guest contact fields remain unverified operational snapshots
- `anonymous_display` is only a visibility preference

## Current Storage Boundary

- prompt-33 stores only a session-scoped guest draft
- prompt-33 creates no `users` row
- prompt-33 creates no `donors` row
- prompt-33 creates no `donation_intent`, `payment`, `donation_record`, or receipt row

## Prompt-31 / Prompt-32 Compatibility

- prompt-31 registration remains the only account-creation path
- prompt-32 email and phone verification remain account-level trust axes only
- authenticated users may still use guest donation entry without affecting their account verification state

## Deferred Boundaries

- dedicated donor payable persistence
- payment initiation and finalization
- narrow status / receipt lookup
- identified account-linked donation entry
- guest claim / account-link flow
