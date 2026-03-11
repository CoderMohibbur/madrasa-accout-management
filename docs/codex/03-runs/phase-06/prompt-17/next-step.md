# Next Step

Run `docs/codex/01-prompts/prompt-18-route-middleware-policy.md` next.

Carry forward these prompt-17 decisions:

- keep multi-role implementation split into `MR1` shared eligibility resolution, `MR2` neutral chooser entry, `MR3` in-portal switching, and later `MR4` route/middleware alignment
- do not introduce multi-role behavior inside donor or guardian implementation slices themselves
- keep the earliest live multi-role introduction at prompt-39 after donor and guardian portal slices are independently in place
- keep the initial rollout limited to already independently eligible contexts with no mixed-scope dashboard data
- preserve donor-owned and guardian-owned isolation rules while replacing raw guardian-first home redirection with eligibility-derived behavior
