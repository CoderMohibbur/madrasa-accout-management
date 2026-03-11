# Prompt 05 Role Profile Portal Linkage

## Final Distinctions

- Role: potential domain membership
- Profile: domain record plus lifecycle
- Portal eligibility: derived entry permission
- Linkage: guardian-specific protected-data entitlement

## Boundary Rules

- Donor portal
  - donor-domain-only
  - not ultimately keyed to raw `email_verified_at`
  - no guardian linkage required
  - no student-owned data exposure
- Guardian informational portal
  - authenticated light surface
  - unverified and unlinked guardian access allowed
  - no student linkage required
  - non-sensitive/admission-only content
- Guardian protected portal
  - guardian-domain eligibility plus explicit linkage/ownership
  - student, invoice, receipt, and payment-sensitive data only after object-level authorization
- Shared multi-role home
  - context-based switching
  - no mixed-scope data on the neutral surface

## Current-State Caveats

- Live code still uses `verified` plus role middleware plus profile flags.
- Live code has no separate guardian informational portal yet.
- Live code redirects guardian before donor on `/dashboard`, which is not the final multi-role boundary rule.
