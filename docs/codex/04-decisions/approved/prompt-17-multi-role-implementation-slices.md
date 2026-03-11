# Prompt 17 Multi-Role Implementation Slices

Approved baseline decisions from prompt-17:

- Multi-role implementation must be split into `MR1` shared eligible-context resolver foundation, `MR2` neutral multi-role home and chooser entry, `MR3` in-portal donor/guardian context switching affordances, and `MR4` final route/middleware redirect alignment.
- Multi-role support must not be introduced inside donor or guardian implementation slices themselves; it layers on only after donor and guardian contexts are independently correct.
- The earliest safe roadmap phase for live multi-role introduction is prompt-39, after the shared account-state foundations plus the approved donor and guardian portal slices are already in place.
- The smallest safe initial rollout is `MR1` through `MR3` for already independently eligible contexts only.
- `MR4` belongs with later route/middleware hardening rather than the first multi-role rollout wave.
- Shared home and switching behavior must remain eligibility-based, low-scope, and free of mixed donor/guardian dashboard data.
