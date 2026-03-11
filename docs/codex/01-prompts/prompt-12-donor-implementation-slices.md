Use the docs/codex-autopilot workflow from my project.

This task is PHASE_4_DONOR_IMPLEMENTATION_SLICE_PLANNING_ONLY.
Do not implement code yet.

Using the approved donor analysis outputs, do only this:
1) break donor work into the smallest safe implementation slices
2) order the slices by dependency
3) clearly separate:
   - guest donation slices
   - donor account/auth slices
   - donor payable slices
   - donor portal slices
4) identify which slices are schema-first, route-first, UI-first, service-first, or integration-first
5) identify rollback-safe checkpoints
6) identify which donor slices can ship independently

End with:
- donor slice order
- slice-by-slice goals
- dependency notes
- rollback checkpoints
- independently shippable slices
