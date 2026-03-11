# Decisions

- UI work should be implemented in four shared-foundation slices first: `UF1` design tokens, `UF2` shared shells/headers, `UF3` feedback/state patterns, and `UF4` form/data-display primitives.
- Feature-page UI work should follow the approved implementation order instead of competing with it: auth/account-state adoption, guest donation, donor portal, guardian informational, guardian protected, multi-role, external admission activation, and finally the consistency sweep.
- Prompt-28 should focus primarily on the shared-foundation slices and stop before broad page-family restyling.
- Donor, guardian informational, guardian protected, guest donation, and multi-role UI slices are independently shippable once their domain prompts are ready because they can adopt the shared foundation additively.
- Prompt-19 remains preserved in the slice plan: admission handoff activation is a later dedicated slice for approved public and guardian-informational surfaces only, not an internal workflow or auth/protected-guardian change.
