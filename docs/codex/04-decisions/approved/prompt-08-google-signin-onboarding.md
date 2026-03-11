# Prompt 08 Google Sign-In Onboarding

Approved baseline decisions from prompt-08:

- Google sign-in is an optional alternate onboarding/sign-in method on the same single `users` account model.
- Provider-asserted verification applies only to the email channel and remains separate from phone verification, approval, role assignment, portal eligibility, and guardian linkage.
- The safest persistence model is a dedicated external-identity linkage record rather than provider-specific columns on `users`.
- Verified-email matches should link to the existing local `users` account instead of creating a second user, but only when no provider-link conflict exists.
- Google sign-in must never auto-grant donor portal eligibility, guardian portal eligibility, guardian linkage, or protected access.
- The safest minimal rollout is public/donor-focused first-time Google onboarding plus authenticated existing-account linking, with broader guardian-first onboarding deferred.
