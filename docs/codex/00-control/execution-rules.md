# Execution Rules

## Global rules
- Stay inside the approved slice.
- Prefer additive-first changes.
- Do not silently refactor unrelated code.
- Preserve legacy management compatibility.
- Preserve ownership boundaries for students, invoices, receipts, and payments.
- Use one consistent, modern UI/UX pattern.
- If a dependency gap is discovered, stop at analysis for that dependency and report it.

## Analysis-phase rules
- Do not implement code in analysis-only prompts.
- Normalize business rules before designing architecture.
- Separate current-state inventory from target-state design.

## Implementation-phase rules
- Restate the exact approved slice before coding.
- Restate what must not change.
- Implement only the minimum approved scope.
- Record deferred work clearly.

## Placeholder rules
- Use dummy placeholders only.
- Do not pretend dummy secrets are production-ready.
- Record all placeholder usage under `docs/codex/06-production-replace/`.
