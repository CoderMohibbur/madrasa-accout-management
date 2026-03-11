# Decisions

- The implementation rollout should proceed in seven additive waves: shared foundation, donation entry/payment foundation, donor portal, guardian informational, guardian protected, identity expansion plus multi-role finalization, and final release gating.
- Shared account-state cutover is the highest overall rollout risk and must ship only after prompt-24 classification-first evidence and the prompt-25 Phase A test pack are green.
- Donor payment-domain rollout must precede donor portal history rollout, and guardian informational rollout must precede guardian protected rollout.
- Google sign-in and multi-role behavior should land only after donor and guardian contexts are independently correct; route and middleware finalization should come at the end of that cluster.
- Durable rollback points exist only where new schema remains dark, new routes are additive, or new auth/providers can be disabled without data rewriting.
- Destructive cleanup, legacy donor-history bridging, guest claim flows, internal admission workflow, Google auto-link shortcuts, and mixed-scope multi-role dashboards are all later work and must not ship in the first implementation rollout.
