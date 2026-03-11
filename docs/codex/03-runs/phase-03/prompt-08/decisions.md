# Decisions

- Treat Google sign-in as an optional alternate onboarding/sign-in method on the same single `users` account model, not as a separate account system.
- Provider-asserted verification may satisfy only the email-verification axis; it must remain separate from phone verification, approval, role assignment, portal eligibility, and guardian linkage.
- Prefer a dedicated external-identity linkage record for Google accounts instead of overloading `users` with provider-specific columns.
- If Google returns a verified normalized email that matches an existing `users.email`, link to that account instead of creating a second user, but only when there is no conflicting provider link.
- Google sign-in must never auto-grant donor portal eligibility, guardian portal eligibility, guardian linkage, or protected access.
- The safest minimal rollout is public/donor-focused first-time Google onboarding plus authenticated account linking, while broader guardian-first onboarding waits for guardian informational access and cleaner verification/approval separation.
