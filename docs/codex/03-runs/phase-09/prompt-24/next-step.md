# Next Step

Run `docs/codex/01-prompts/prompt-25-test-matrix.md` next.

Carry forward these prompt-24 decisions:

- treat backfill as a separate classification step after additive schema lands and before any read-path switch
- keep `email_verified_at`, donor/guardian profile flags, pivot rows, and invoice ownership untouched during the first backfill pass
- backfill user-level state conservatively and leave ambiguous rows in review buckets rather than guessing
- keep guardian profile linkage review distinct from guardian-student object ownership review
- start donor-domain tables empty and defer any legacy donor transaction bridge to later prompt-specific work
