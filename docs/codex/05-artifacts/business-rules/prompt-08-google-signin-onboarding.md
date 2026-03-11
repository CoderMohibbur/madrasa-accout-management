# Prompt 08 Google Sign-In Onboarding

## Core Model

- Google sign-in is an alternate sign-in and onboarding path on the same account model
- Google sign-in is not a second account system
- Google sign-in follows the prompt-06 open-registration rule: base identity plus optional intent only

## Verification Boundary

- Google-asserted verification maps only to the email channel
- It does not imply phone verification
- It does not imply approval
- It does not imply role assignment
- It does not imply portal eligibility
- It does not imply guardian linkage

## Linking Rules

- Prefer explicit linking from an authenticated local session
- Verified normalized `users.email` may be used for safe first-time auto-linking
- Provider subject conflicts must fail closed
- Never auto-link from donor/guardian profile contact fields alone
- Never auto-link from guest-donation contact data alone

## Role And Context Rules

- Donor-only, guardian-only, and dual-role expansions stay on the same user
- Google sign-in does not create donor or guardian eligibility by itself
- Guardian protected access still requires separate linkage and authorization
- Dual-role post-login behavior must eventually be eligibility-driven, not guardian-first by raw role order

## Minimal Safe Rollout

- Public and donor-friendly first-time Google onboarding
- Authenticated linking for existing local accounts
- Verified-email auto-link only when conflict-free
- No automatic protected guardian onboarding in the first rollout
- Dummy OAuth placeholders only until real credentials are provided
