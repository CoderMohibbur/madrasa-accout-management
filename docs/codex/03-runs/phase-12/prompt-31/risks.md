# Risks

- The `registered_user` role is created lazily during registration, so later rollout work may still need a seed or backfill step if tooling assumes the role already exists.
- Newly registered users still encounter the existing verification notice on legacy verified routes such as `/dashboard`; that is intentional for prompt-31 but leaves the wider post-registration journey dependent on prompt-32 and later auth prompts.
- Donor and guardian self-registration now creates draft domain rows without protected access, so later linking and eligibility prompts must keep duplicate handling and authorization boundaries strict.
- Local validation still depends on the working Laragon PHP 8.2 runtime because the local PHP 8.4 runtime in this environment does not currently load `mbstring`.
