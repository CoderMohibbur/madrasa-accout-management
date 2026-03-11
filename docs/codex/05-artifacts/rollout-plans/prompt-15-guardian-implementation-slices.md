# Prompt 15 Guardian Implementation Slices

## Slice Order

1. `GA1` - guardian intent and unlinked-profile foundation
2. `GA2` - guardian no-portal auth/read-path separation
3. `GI1` - light guardian informational portal shell
4. `GI2` - informational content and external-handoff surfaces
5. `GP1` - protected portal boundary split
6. `GP2` - linked student/invoice/receipt read-boundary adaptation
7. `GP3` - protected payment-entry continuity

## Slice Families

- Guardian auth/onboarding: `GA1`, `GA2`
- Informational portal: `GI1`, `GI2`
- Linkage-sensitive protected portal: `GP1`, `GP2`, `GP3`

## Safest Early-Delivery Slices

- `GA1`
- `GI1`
- `GI2`

## Highest-Risk Slices

- `GA2`
- `GP2`
- `GP3`

## Rollback Checkpoints

1. After `GA1`: guardian onboarding produces only an unlinked informational-state identity.
2. After `GA2`: guardian login/no-portal informational eligibility is separated from blanket verification assumptions.
3. After `GI1`: a dedicated informational guardian shell exists with no protected data.
4. After `GI2`: informational admission/institution content plus external handoff is live with no internal application workflow.
5. After `GP1`: informational and protected guardian entry paths are distinct.
6. After `GP2`: linked guardians retain protected read access while unlinked guardians remain informational-only.
7. After `GP3`: protected guardian payment-entry and history stay intact after the boundary split.

## Prompt Mapping

- Prompt-31 and prompt-32 provide prerequisites for `GA1` and `GA2`.
- Prompt-36 should primarily deliver `GI1` and `GI2`.
- Prompt-37 should primarily deliver `GP1`, `GP2`, and `GP3`.
- Prompt-41 later centralizes the external admission URL configuration across the approved placements.
