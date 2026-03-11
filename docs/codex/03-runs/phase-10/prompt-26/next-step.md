# Next Step

Run `docs/codex/01-prompts/prompt-27-final-implementation-planning-packet.md` next.

Carry forward these prompt-26 decisions:

- keep rollout grouped into seven additive waves, with shared foundation before payment, guardian informational before guardian protected, and Google plus multi-role only after independent context correctness
- treat shared account-state cutover, donor payment foundation, guardian protected boundary split, and route finalization as the highest-risk rollout areas
- preserve the prompt-25 blocker pack as cumulative release gating rather than resetting tests by feature area
- use only rollback points that leave new schema dark, new routes additive, or new auth/provider surfaces disableable without rewriting data
- keep destructive cleanup, legacy donor-history bridge work, guest claim flows, mixed-scope multi-role surfaces, and internal admission workflow out of the first rollout
