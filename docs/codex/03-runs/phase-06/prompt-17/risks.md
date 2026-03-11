# Risks

- If multi-role behavior is introduced during donor or guardian implementation slices, those phases will silently widen scope and may entangle donor and guardian boundaries before their own gates are stable.
- If the neutral chooser is implemented before shared eligibility derivation is centralized, the app could advertise contexts based on raw roles instead of real portal eligibility.
- If prompt-39 tries to absorb the final redirect/middleware cleanup as well, regression risk rises because home behavior, portal chrome, and route-policy changes would land in one wave.
- If guardian informational and protected contexts are not kept distinct inside switching logic, unlinked guardians could be routed toward protected pages too early.
- If the chooser or switch affordances ever aggregate donor totals with guardian balances or student data, the approved scope-isolation rules will be violated.
- The current repo still has guardian-first `/dashboard` behavior and blanket `verified` middleware in the live stack, so implementation must avoid assuming current routing already matches the target multi-role boundary.
