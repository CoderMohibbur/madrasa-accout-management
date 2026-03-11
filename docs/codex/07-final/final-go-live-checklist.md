# Final Go-Live Checklist

## Implementation Entry Decision

- Implementation may begin: `yes`
- Allowed starting point: `docs/codex/01-prompts/prompt-28-shared-ui-foundation-implementation.md`
- Entry conditions:
  - follow the exact prompt-28 through prompt-43 order
  - preserve prompt-26 rollout waves and rollback checkpoints
  - preserve prompt-23 schema limits, prompt-24 classification-first migration posture, and prompt-25 cumulative blocker packs
  - keep baseline-vs-regression test classification explicit

## Exact No-Go Warnings

- Stop if any implementation step requires renaming existing route names or editing historical migrations.
- Stop if any step tries to finalize donor online payments directly against legacy `transactions`.
- Stop if any read-path or auth cutover happens before prompt-24 classification-first evidence and review buckets are ready.
- Stop if any donor, guardian, guest, phone, or Google flow auto-links or auto-verifies ambiguous records from loose contact similarity.
- Stop if guardian protected access is granted from role membership alone, informational access alone, or only one ownership signal.
- Stop if blanket `verified` is removed before explicit donor, guardian informational, guardian protected, and shared-home eligibility middleware exist.
- Stop if Google/provider flows auto-resolve subject conflicts or imply portal eligibility, guardian linkage, or protected access.
- Stop if guest checkout auto-creates accounts/profiles or if guest claim/account-link, recurring billing, or saved payment methods are pulled into the first rollout.
- Stop if multi-role work introduces mixed-scope dashboards, self-service role claiming, or raw role-order redirect logic as the final model.
- Stop if admission work expands into internal forms, drafts, uploads, or protected guardian/auth CTA placements beyond approved surfaces.
- Stop if baseline-vs-regression test separation becomes unclear or if any prompt-25 blocker pack gate fails.

## Final Release Gate Reminder

- Prompt-43 remains the hard release gate.
- Full blocker pack `RB-01` through `RB-10` must pass in the release candidate.
- End-to-end smoke must distinguish baseline failures from new regressions clearly enough for a go/no-go decision.
