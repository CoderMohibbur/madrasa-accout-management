# Prompt 01 Workflow Guardrail Baseline

Approved baseline decisions from prompt-01:

- The `docs/codex` sequence is a retrospective analysis/documentation pass over the current repository state.
- Live branch contents plus the latest autopilot handoff/report artifacts outrank stale commit metadata when they differ.
- Later prompts must preserve the existing single-guard auth model, protected route names, frozen historical migrations, and financial/reporting safety constraints unless a prompt explicitly and narrowly analyzes those areas.
- Later prompt execution must stop if it would require real secrets, broad protected-path rewrites, or unsafe payment assumptions.
