# Decisions

- Prompt-43 remained validation-only; it did not reopen application behavior, routes, controllers, models, services, policies, middleware, or migrations.
- The prompt-43 release gate is the cumulative blocker-pack run, not a feature-specific smoke test; it must cover the Phase 1 through Phase 5 regression slice plus the full Phase-12 feature pack and policy checks.
- A stale test exposed by the release gate may be corrected inside prompt-43 only when the fix aligns the test to already-approved later behavior. `tests/Feature/Phase1/PortalRoleAccessTest.php` was corrected on that basis.
- Prompt-41's shared admission resolver and handoff component path remained intact; no hard-coded external admission URL returned to live Blade surfaces.
- Prompt-42's shared `ui-*` card/stat/table/alert/form patterns remained intact across auth, donor, guardian informational, and guardian protected surfaces.
- The runtime full-suite baseline improved from 14 previously documented failures to 10 remaining auth-suite failures because `Tests\Feature\ProfileTest` now passes; no new non-auth regressions were introduced.
- Prompt-43 is the final approved prompt in the sequence; there is no further numbered prompt to run.
