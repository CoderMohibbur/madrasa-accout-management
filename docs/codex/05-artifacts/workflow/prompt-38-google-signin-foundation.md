# Prompt 38 Google Sign-In Foundation

## Route Surface

- `GET /auth/google/redirect`
- `GET /auth/google/callback`
- `POST /auth/google/link`

## Supported Foundation Flows

- first-time public Google sign-in
  - creates one shared `users` account
  - marks email verified only when Google reports a verified email
  - assigns the registered-user role only
  - lands through the existing shared-home/dashboard compatibility flow
- first-time donor Google sign-in
  - creates the same shared account
  - creates only a non-portal donor profile
  - does not grant donor role or donor portal eligibility
  - lands on the existing donor no-portal/dashboard behavior from prompt-35
- repeat Google sign-in on an already-linked account
  - resolves by stored provider subject
  - updates the provider email snapshot and `last_used_at`
  - does not reassign the link to another user
- authenticated Google link from profile
  - links Google only to the current signed-in account
  - fails closed if that provider subject or another Google identity is already linked elsewhere

## Fail-Closed Rules

- no first-time guardian Google onboarding in prompt-38
- no auto-link from donor/guardian profile email, phone, guest donation contact, or name similarity
- no provider-subject reassignment
- no unlink/reassignment tooling
- no donor payable or donor history redesign
- no guardian protected widening beyond the existing prompt-37 eligibility rules

## Guardian Boundary Preservation

- Google verification remains email-only proof.
- Existing guardian informational vs protected route separation remains intact.
- Protected guardian access still requires:
  - accessible shared account state
  - verified email
  - portal-enabled active guardian profile
  - explicit linked-student ownership state

## Deferred Work

- prompt-39 multi-role chooser and switching
- Google unlink/reassignment and recovery tooling
- any broader guardian-first Google onboarding beyond existing-account linking
