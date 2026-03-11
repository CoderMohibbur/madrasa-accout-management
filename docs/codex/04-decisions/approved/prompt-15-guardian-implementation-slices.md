# Prompt 15 Guardian Implementation Slices

Approved baseline decisions from prompt-15:

- Guardian implementation must be split into these ordered slices: `GA1` guardian intent and unlinked-profile foundation, `GA2` guardian no-portal auth/read-path separation, `GI1` light guardian informational portal shell, `GI2` informational content and external-handoff surfaces, `GP1` protected portal boundary split, `GP2` linked student/invoice/receipt read-boundary adaptation, and `GP3` protected payment-entry continuity.
- Guardian self-registration and guardian intent capture belong in an unlinked informational-state slice and must not grant protected access.
- The light guardian informational portal must land as an additive non-sensitive surface before protected guardian gating is adapted.
- Protected guardian slices remain separately planned for linkage-sensitive read paths and payment-entry continuity.
- Guardian auth/read-path separation depends on the shared account-state and verification foundations and must not be forced early through blanket `verified` reuse.
- External admission handoff remains informational-only and is preserved across guardian slices; it is not an internal admission workflow.
- Prompt-13 guardian permission decisions, prompt-14 admission-boundary decisions, and prompt-12 donor planning decisions remain preserved and are not reopened by guardian slice planning.
