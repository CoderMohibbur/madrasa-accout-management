# Next Step

Run `docs/codex/01-prompts/prompt-30-account-state-read-path-adaptation.md` next.

Carry forward:
- preserve the prompt-28 shared UI foundation as-is
- treat the prompt-29 `users` account-state columns as dark, nullable-first schema until prompt-30 and later backfill prompts adapt reads safely
- keep `email_verified_at` intact until the approved read-path/auth cutover explicitly replaces its overloaded approval role
- keep guardian linkage-state adoption and donor settlement tables deferred to their later approved prompts
- keep baseline-vs-regression validation classification explicit using the existing auth/profile baseline manifest
