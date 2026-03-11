# Prompt 38 Google Sign-In Foundation

Approved implementation decisions from prompt-38:

- Google sign-in now uses a dedicated `external_identities` linkage model on the same shared `users` account system.
- Verified normalized `users.email` is the only safe unauthenticated auto-link input; provider-subject conflicts fail closed.
- Public and donor first-time Google onboarding are live in prompt-38; first-time guardian Google onboarding remains deferred.
- Explicit authenticated Google linking is available from profile for the current signed-in account only.
- Google provider verification can satisfy only the email-verification axis and never by itself grants donor portal eligibility, guardian linkage, or protected guardian access.
- Prompt-35 donor access/history behavior and prompt-37 guardian protected gating remain preserved.
- Google unlink, provider-subject reassignment, and broad merge logic remain disabled in this rollout.
