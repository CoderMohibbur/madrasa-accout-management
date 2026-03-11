# Prompt 39 Multi-Role Home And Switching

## Shared Home Surface

- `GET /dashboard`
  - direct redirect when exactly one donor/guardian context is eligible
  - neutral chooser when more than one donor/guardian context is eligible
  - existing `registration.onboarding` fallback for verified `registered_user` accounts with no donor/guardian context
  - existing management dashboard behavior for management-compatible accounts

## Supported Context Choices

- donor context
  - `donor.dashboard`
  - covers donor portal and donor no-portal states using the existing prompt-35 access logic
- guardian informational context
  - `guardian.info.dashboard`
  - used when guardian-domain context exists but protected guardian eligibility is not yet satisfied
- guardian protected context
  - `guardian.dashboard`
  - used only when prompt-37 protected guardian eligibility is already satisfied

## Post-Auth Routing Alignment

- password login uses the chooser-aware resolver
- Google callback uses the chooser-aware resolver
- email-verification prompt, resend, and completion use the chooser-aware resolver
- no flow bypasses donor eligibility, guardian linkage, or protected guardian gating

## In-Portal Switching

- donor, guardian informational, and guardian protected shells expose additive switch links only when more than one context is already eligible
- switch links never grant access by themselves
- shared home remains available as the neutral chooser anchor

## Boundary Preservation

- no mixed donor-plus-guardian records render on `/dashboard`
- donor pages stay donor-owned only
- guardian informational pages stay non-sensitive
- guardian protected pages still require verified email, eligible guardian profile state, and linked-student ownership
- route names remain unchanged

## Deferred Work

- prompt-40 final route/middleware/policy cleanup
- remembered context preference or other context-persistence polish
- any broader identity merge, unlink, or provider reassignment tooling
