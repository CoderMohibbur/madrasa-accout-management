# Decisions

- Multi-role implementation must be split into these ordered slices: `MR1` shared eligible-context resolver foundation, `MR2` neutral multi-role home and chooser entry, `MR3` in-portal donor/guardian context switching affordances, and `MR4` final route/middleware redirect alignment.
- Multi-role support must not be introduced inside donor or guardian slices themselves. Donor and guardian portals stay independently implemented first, and multi-role behavior layers on top afterward.
- The earliest safe roadmap phase for live multi-role introduction is prompt-39, after the shared account-state foundations and the approved donor and guardian portal slices are already in place.
- The smallest safe initial rollout is `MR1` through `MR3` for already independently eligible contexts only; `MR4` remains the follow-on route/middleware cleanup and hardening step.
- Shared home and switching behavior must stay eligibility-based and low-scope: no mixed donor/guardian summaries, no auto-linking, and no raw guardian-first redirect ordering.
