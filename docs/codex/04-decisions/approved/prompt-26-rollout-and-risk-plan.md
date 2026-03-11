# Prompt 26 Rollout And Risk Plan

Approved baseline decisions from prompt-26:

- The implementation roadmap should roll out in seven additive waves: shared foundation, donation entry/payment foundation, donor portal, guardian informational, guardian protected, identity expansion plus multi-role finalization, and final release gating.
- Shared account-state read cutover is the highest overall rollout risk and must not ship before prompt-24 classification-first evidence and the prompt-25 Phase A blocker pack pass.
- Donor payment-domain rollout must precede donor portal history rollout, and guardian informational rollout must precede guardian protected rollout.
- Google sign-in and multi-role rollout must come only after donor and guardian contexts are independently correct, with route/middleware finalization last in that cluster.
- Rollback points are valid only where schema remains dark, routes stay additive, or new provider/auth surfaces can be disabled without rewriting data.
- Destructive cleanup, legacy donor-history bridging, guest claim flows, internal admission workflow, mixed-scope multi-role surfaces, and identity auto-link shortcuts remain later work and must not ship in the first implementation rollout.
