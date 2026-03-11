# Prompt 17 Multi-Role Implementation Slices

## Slice Order

1. `MR1` shared eligible-context resolver foundation
2. `MR2` neutral multi-role home and chooser entry
3. `MR3` in-portal donor/guardian context switching affordances
4. `MR4` final route/middleware redirect alignment

## Dependency Map

- Auth foundations:
  - prompt-29
  - prompt-30
  - prompt-32
- Donor dependency:
  - prompt-35
- Guardian dependencies:
  - prompt-36
  - prompt-37
- Routing and policy:
  - prompt-18 analysis before implementation
  - prompt-40 finalization after initial rollout
- UI:
  - prompt-20 through prompt-22 analysis
  - prompt-28 shared UI foundation

## Earliest Safe Introduction

- first live introduction phase: prompt-39
- first safe live scope: `MR1` through `MR3` only
- do not introduce multi-role behavior earlier inside prompt-35, prompt-36, or prompt-37

## Rollback Sequence

1. keep direct `/donor` and `/guardian` access working while `MR1` is introduced
2. make the chooser additive in `MR2` so shared-home fallback remains possible
3. make switch controls additive in `MR3` so chooser and direct-route fallback remain available
4. leave final redirect/middleware cleanup to `MR4` after the earlier slices are validated
