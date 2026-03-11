# Prompt 19 External Admission URL Configuration

Approved baseline decisions from prompt-19:

- The external admission destination should be environment-driven through one canonical key, proposed as `ADMISSION_EXTERNAL_URL`.
- The preferred config owner is a dedicated portal-domain config entry, proposed as `config/portal.php` with `portal.admission.external_url`.
- Environment/config ownership is limited to the external destination itself; route names, placement rules, button copy, and authorization boundaries stay in application code.
- Missing or invalid configuration must remove the live CTA and fall back to neutral informational messaging, not to guessed hard-coded URLs or protected internal routes.
- Approved placements are limited to public admission surfaces and the future guardian-informational surface; the current protected `/guardian` portal, donor portal, shared home, auth forms, management pages, and payment flows are not approved CTA surfaces.
- Prompt-41 should consume the destination through one shared helper/component or equivalent single resolver so all approved placements stay consistent.
