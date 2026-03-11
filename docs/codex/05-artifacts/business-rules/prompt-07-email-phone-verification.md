# Prompt 07 Email And Phone Verification

## Core Model

- Email verification and phone verification are separate contact-trust axes
- Verification state does not equal approval
- Verification state does not equal role assignment
- Verification state does not equal portal eligibility
- Verification state does not equal guardian linkage

## Channel Policy

- Email stays the canonical login identifier in the smallest safe rollout
- Phone is optional and verification-first
- Phone is not a universal gate for donor or guardian entry flows
- Donor and guardian profile `mobile` fields are not the canonical verified phone source

## Verification-State Outcomes

- Neither verified:
  - donor login and donation still allowed
  - guardian login and informational access still allowed
  - no trusted contact channel for step-up or recovery
- Email only:
  - trusted email channel
  - phone still untrusted
- Phone only:
  - trusted phone channel
  - email still untrusted
- Both:
  - strongest contact assurance
  - still no automatic portal or linkage grant

## Duplicate And Guest Rules

- Normalized account email stays unique
- Verified phone resolves to one active account-level owner by default
- Guest donation contact capture remains optional and unverified
- Matching guest contact to an account never auto-verifies or auto-grants access

## Guardrails For Later Prompts

- Do not use blanket `verified` middleware as the final donor or guardian boundary
- If stronger assurance is needed later, add explicit step-up checks
- Provider-asserted verification applies only to the relevant channel, not to portal eligibility or linkage
