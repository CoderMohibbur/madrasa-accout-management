# Decisions

- The external admission destination should move to one dedicated domain config entry, with environment ownership limited to a single proposed key: `ADMISSION_EXTERNAL_URL`.
- The preferred canonical config location is `config/portal.php` with a nested `portal.admission.external_url` key, because this is a portal/public-surface setting rather than a third-party credential or an app-global identity setting.
- Internal route names, placement rules, and button copy must stay in code; only the external destination itself belongs in environment/config.
- Missing or invalid configuration must suppress the live CTA and fall back to neutral informational messaging, never to a guessed hard-coded URL or a protected internal route.
- Approved admission CTA placements are limited to public admission surfaces and the future guardian-informational surface; auth forms, donor surfaces, shared home, management, payment flows, and the current protected `/guardian` portal are not approved placements.
- Prompt-41 should centralize all consumption behind one shared helper/component or equivalent single resolver so public and guardian-informational placements cannot drift.
