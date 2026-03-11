# Codex Prompt Execution Bundle

This folder provides a structured, sequential prompt pack and reporting framework for Codex.

## Start here
1. Read `00-control/master-orchestrator.md`
2. Read `00-control/phase-order.md`
3. Use prompt files from `01-prompts/` in numeric order
4. Save every run result into `03-runs/`
5. Promote reusable outputs into `05-artifacts/`
6. Record placeholder items under `06-production-replace/`
7. Consolidate final outputs under `07-final/`

## Important
- Use dummy placeholders only for secrets and external providers.
- Do not skip the analysis phases.
- Do not widen implementation scope beyond the approved slice.
