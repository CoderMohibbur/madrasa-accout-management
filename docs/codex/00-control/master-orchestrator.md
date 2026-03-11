# Master Orchestrator

This repository contains a sequential Codex execution pack for phased analysis and implementation.

## Objective
Run the numbered prompt files in order, starting from Prompt 01 and ending at Prompt 43, while saving step-by-step reports, decisions, risks, blockers, and next-step guidance under `docs/codex/03-runs/`.

## Mandatory operating rules
- Stay strictly inside the exact task scope of the current prompt.
- Do not silently widen scope.
- Prefer additive-first changes.
- Do not refactor unrelated code.
- Preserve legacy management behavior unless explicitly approved otherwise.
- Do not weaken student/invoice/payment ownership protections.
- Do not assume legacy transactions are safe for new donor payment finalization.
- Do not copy weak existing UI patterns as the target standard.
- Use one coherent, global-standard, modern UI/UX direction for all new or revised affected surfaces.

## Sequential execution algorithm
For each prompt:
1. Read the current prompt file from `docs/codex/01-prompts/`.
2. Read prior approved reports, decisions, blockers, and next-step notes from earlier run folders.
3. Adapt the current prompt only as needed to reflect:
   - approved decisions from earlier phases
   - unresolved blockers
   - corrected assumptions from earlier reports
4. Run the prompt.
5. Save the current prompt input used into `input.md`.
6. Save the primary output into `report.md`.
7. Save approved decisions into `decisions.md`.
8. Save known risks into `risks.md`.
9. Save blockers into `blockers.md`.
10. Save the next recommended prompt action into `next-step.md`.

## Correction loop rules
- If a report reveals a contradiction, dependency gap, unsafe assumption, or missing prerequisite, do not blindly continue.
- First correct the current phase scope by:
  - updating the current or next prompt with the minimum necessary clarification
  - documenting the correction inside the current run folder
- If the issue is fixable inside the current phase, fix the prompt and rerun the current prompt.
- If the issue affects later phases only, carry the correction forward into the next prompt.

## Blocking condition rules
- Do not fabricate real secrets.
- Use only documented dummy placeholders from `docs/codex/06-production-replace/`.
- If a real external dependency is required, continue as far as safely possible with placeholders and record the replacement requirement.
- Stop only when a real blocker prevents safe progress.
- When blocked, save the blocker and the minimum required human follow-up.

## Reporting expectations
Every run folder should contain:
- `input.md`
- `report.md`
- `decisions.md`
- `risks.md`
- `blockers.md`
- `next-step.md`

## Finalization
At the end of the sequence, consolidate final outcomes into `docs/codex/07-final/`.
