# Next Step

Run `docs/codex/01-prompts/prompt-20-global-ui-ux-baseline.md` next.

Carry forward these prompt-19 decisions:

- configure the external admission destination from one canonical config key rather than hard-coded Blade links
- keep environment ownership limited to the external destination itself, not route names, access rules, or CTA copy
- keep live `/guardian` protected and reserve admission CTA usage for public and future guardian-informational surfaces only
- use graceful hide/disable fallback behavior when the external destination is missing or invalid
- centralize future CTA rendering behind one shared resolver/component so prompt-41 can wire all approved placements consistently
