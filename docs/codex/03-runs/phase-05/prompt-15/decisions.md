# Decisions

- Guardian implementation is approved as separate slices rather than one guardian wave: `GA1`, `GA2`, `GI1`, `GI2`, `GP1`, `GP2`, and `GP3`.
- Guardian self-registration and guardian intent capture belong in an unlinked informational-state onboarding slice and must not grant protected access.
- The light guardian informational portal must land as an additive non-sensitive surface before protected guardian gating is adapted.
- Protected guardian slices remain separately planned for linkage-sensitive read paths and protected payment-entry continuity.
- Guardian auth/read-path separation depends on the shared account-state and verification foundations and must not be forced early by reusing blanket `verified` semantics.
- External admission handoff remains informational-only and is preserved across guardian slices; it is not an internal admission workflow.
- Prompt-13 guardian permission decisions, prompt-14 admission-boundary decisions, and prompt-12 donor planning decisions remain preserved and were not reopened by prompt-15.
