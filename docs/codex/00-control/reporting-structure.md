# Reporting Structure

All phase and prompt outputs must be saved under `docs/codex/03-runs/`.

## Per-prompt file set
Each prompt folder should contain:
- `input.md` — the exact prompt text used for that run, including any approved adaptations
- `report.md` — the main analysis or implementation result
- `decisions.md` — approved decisions extracted from the report
- `risks.md` — risks, warnings, and technical debt
- `blockers.md` — blockers, missing dependencies, and required follow-up
- `next-step.md` — the exact next recommended action or prompt

## Adaptation rules
Before each prompt run:
1. Read all prior `decisions.md`, `risks.md`, and `blockers.md`.
2. Update the current prompt only where needed to:
   - reflect approved earlier decisions
   - avoid repeated mistakes
   - incorporate discovered constraints
3. Save the adapted version to the current prompt folder as `input.md`.

## Directory convention
- Phase folders: `phase-00` through `phase-12`
- Prompt folders: `prompt-01` through `prompt-43`

## Artifact promotion rules
When a report creates reusable design output:
- copy or summarize it into `docs/codex/05-artifacts/`
- promote final approved outcomes into `docs/codex/07-final/`
