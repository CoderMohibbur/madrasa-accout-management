# Prompt 22 UI Implementation Slices

Approved baseline decisions from prompt-22:

- UI implementation must start with four shared-foundation slices: `UF1` design tokens, `UF2` shell/header primitives, `UF3` feedback/state patterns, and `UF4` form/data-display primitives.
- Prompt-28 should primarily implement those shared foundations and avoid broad feature-page restyling.
- Feature-page UI adoption should follow the approved prompt order: auth/account-state support, guest donation, donor portal, guardian informational, guardian protected, multi-role, external admission handoff, and final consistency cleanup.
- The additive feature UI slices for guest donation, donor, guardian informational, guardian protected, multi-role, and external admission are independently shippable once their domain prompts are ready.
- Prompt-19 remains preserved: admission handoff activation is a later dedicated UI slice for approved public and guardian-informational surfaces only.
