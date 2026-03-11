# Prompt 22 UI Implementation Slices

## Shared-Foundation-First Sequence

1. `UF1` design-token contract
2. `UF2` shell and header foundation
3. `UF3` feedback and state pattern foundation
4. `UF4` form and data-display primitive foundation

## Feature-Page Sequence

5. `AU1` auth and account-state UI adoption
6. `GD1` public donor-entry and guest-donation UI activation
7. `DO1` donor portal normalization
8. `GI1` guardian informational shell and content activation
9. `GP1` guardian protected normalization
10. `MR1` multi-role chooser and switcher UI
11. `AD1` public / guardian admission handoff activation
12. `FC1` final cross-product consistency sweep

## Prompt Mapping

- prompt-28 -> `UF1` to `UF4`
- prompt-31 / prompt-32 / prompt-38 -> `AU1`
- prompt-33 / prompt-34 -> `GD1`
- prompt-35 -> `DO1`
- prompt-36 -> `GI1`
- prompt-37 -> `GP1`
- prompt-39 -> `MR1`
- prompt-41 -> `AD1`
- prompt-42 -> `FC1`

## Independently Shippable

- `UF1`
- `UF2`
- `UF3`
- `UF4`
- `GD1`
- `DO1`
- `GI1`
- `GP1`
- `MR1`
- `AD1`

## Best Landed With Related Domain Work

- `AU1`
- `FC1`

## Rollback Checkpoints

1. after `UF1`
2. after `UF2`
3. after `UF3` + `UF4`
4. after `AU1`
5. after `GD1`
6. after `DO1`
7. after `GI1`
8. after `GP1`
9. after `MR1`
10. after `AD1`
11. after `FC1`

## Guardrails Carried Forward

- inherit prompt-20 baseline direction
- inherit prompt-21 screen/component map
- keep prompt-28 foundation-focused
- keep admission handoff external-only
- do not use prompt-22 to justify broad auth/public/management rewrites outside related prompts
