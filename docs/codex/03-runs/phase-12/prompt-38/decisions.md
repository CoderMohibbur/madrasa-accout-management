# Decisions

- Google sign-in is now implemented on the same shared `users` account model through a dedicated `external_identities` link table rather than provider-specific columns on `users`.
- Verified normalized `users.email` remains the only safe unauthenticated auto-link input for first-time Google sign-in.
- Existing provider-subject links are authoritative and are never silently reassigned to another user.
- First-time Google onboarding is enabled only for public and donor foundations in prompt-38; first-time guardian Google onboarding remains deferred.
- Explicit authenticated Google linking is available from profile for the current signed-in account only.
- Google verification still applies only to the email axis and does not imply donor portal eligibility, guardian linkage, or protected guardian access.
- Prompt-35 donor behavior and prompt-37 guardian protected gating remain authoritative and unchanged outside the additive Google entry points.
