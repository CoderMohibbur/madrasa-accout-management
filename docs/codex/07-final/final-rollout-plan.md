# Final Rollout Plan

## Rollout Waves

### R1 - Shared Foundation And Dark-State Readiness

- `prompt-28` through `prompt-32`
- Shared UI foundation, dark schema, classification-first evidence, account-state/read-path adaptation, registration, and verification foundation

### R2 - Donation Entry And Donor Payment Foundation

- `prompt-33` through `prompt-34`
- Public/identified donation entry only with the safe donor payable model active

### R3 - Donor Account And Portal Rollout

- `prompt-35`
- Donor auth/account behavior separation, donor portal eligibility, and donor history access

### R4 - Guardian Informational Rollout

- `prompt-36`
- Additive non-sensitive guardian informational portal and external handoff

### R5 - Guardian Protected Rollout

- `prompt-37`
- Protected guardian student/invoice/payment/receipt access with linkage and object ownership

### R6 - Identity Expansion, Multi-Role, And Route Finalization

- `prompt-38` through `prompt-40`
- Google sign-in, neutral chooser/context switching, and final eligibility-driven route cleanup

### R7 - External Handoff, Final UI Consistency, And Release Gate

- `prompt-41` through `prompt-43`
- Canonical external URL activation, final UI consistency pass, and final release validation

## Testing And Readiness

- Prompt-25 test packs are cumulative, not per-feature replacements.
- Phase A gates `prompt-28` through `prompt-32`.
- Donor rollout adds `GUEST-*`, `PAY-*`, and `DONOR-*`.
- Guardian rollout adds `GINFO-*`, `GPROT-*`, and `POL-*`.
- Google/multi-role/route finalization adds `GOOG-*`, `MULTI-*`, rerun `ROUTE-*`, and rerun critical `POL-*`.
- Prompt-43 is the hard release gate with full blocker pack `RB-01` through `RB-10`.

## Exact Implementation Phase Order

1. Prompt 28 - shared UI foundation implementation
2. Prompt 29 - account-state schema foundation
3. Prompt 30 - account-state read-path adaptation
4. Prompt 31 - open registration foundation
5. Prompt 32 - email/phone verification foundation
6. Prompt 33 - guest donation entry
7. Prompt 34 - donor payable foundation
8. Prompt 35 - donor auth and portal access
9. Prompt 36 - guardian informational portal
10. Prompt 37 - guardian protected portal gating
11. Prompt 38 - Google sign-in foundation
12. Prompt 39 - multi-role home and switching
13. Prompt 40 - route/middleware/policy finalization
14. Prompt 41 - external admission URL implementation
15. Prompt 42 - final UI consistency pass
16. Prompt 43 - tests and rollout-readiness validation
