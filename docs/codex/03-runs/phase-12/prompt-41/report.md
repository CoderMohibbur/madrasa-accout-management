# Report

## Scope Lock Before Coding

### Exact Approved Slice

- Prompt-41 is limited to the approved external admission URL implementation slice only.
- The approved implementation for this run was limited to:
  - safe configuration-backed external admission URL support
  - approved public and guardian-informational admission placements only
  - removal of public hard-coded admission destination literals in favor of the canonical config-backed path
  - one shared validation and rendering path for live CTA versus fallback messaging
- Prompt-41 had to preserve:
  - prompt-40's explicit donor, guardian informational, guardian protected, and shared-home route middleware boundaries
  - prompt-40's reusable payment, invoice, student, and receipt policy hardening
  - prompt-40's management-surface hardening
  - prompt-36's guardian informational versus protected guardian separation

### Config Ownership Rules Restated

- Environment/config owns only the external destination value:
  - environment key: `ADMISSION_EXTERNAL_URL`
  - config key: `portal.admission.external_url`
- Code continues to own:
  - internal route names
  - approved placement decisions
  - CTA copy and fallback copy
  - public versus guardian-informational versus protected boundary enforcement
- Prompt-41 could not move admission behavior into auth, donor, payment, management, or protected guardian ownership.

### Validation / Fallback Requirements Restated

- Trim empty values to unconfigured.
- Accept only absolute `https://` URLs with a non-empty host.
- Reject unsafe or non-browser-safe values and reject same-host protected internal route destinations such as `/guardian`, `/donor`, `/dashboard`, `/management`, and `/payments`.
- Keep admission information visible even when the destination is unavailable.
- Suppress the live CTA when config is missing or invalid.
- Never guess another URL or fall back to protected Laravel routes.

## Implementation Result

Prompt-41 completed inside the approved external admission URL implementation scope.

### Files Changed

- `app/Services/Portal/ExternalAdmissionUrlResolver.php`
- `app/Http/Controllers/PublicAdmissionController.php`
- `app/Services/GuardianPortal/GuardianInformationalPortalData.php`
- `routes/web.php`
- `resources/views/components/admission/external-handoff.blade.php`
- `resources/views/admission/show.blade.php`
- `resources/views/guardian/info/admission.blade.php`
- `resources/views/welcome.blade.php`
- `resources/views/donations/guest-entry.blade.php`
- `tests/Feature/Phase12/ExternalAdmissionUrlImplementationTest.php`
- `docs/codex/06-production-replace/env-placeholders.md`

### External Admission URL Configuration Implemented

- Added a shared `ExternalAdmissionUrlResolver` that now owns admission URL validation for all approved surfaces.
- The resolver:
  - trims empty config values to unconfigured
  - accepts only absolute `https://` URLs with a host
  - rejects same-host protected internal route destinations so protected Laravel paths cannot be repurposed as admission fallbacks
- Kept `ADMISSION_EXTERNAL_URL` and `portal.admission.external_url` as the single environment/config path and documented the placeholder in `docs/codex/06-production-replace/env-placeholders.md`.

### UI Placements Updated

- Added a new public internal admission-information route:
  - `GET /admission`
  - route name: `admission`
- Added a new public admission-information page that keeps guidance visible and renders the external handoff through the shared component.
- Updated the public welcome-page admission navigation and admission card to point to the internal `/admission` surface instead of a literal off-site destination.
- Updated the guest-donation public navigation admission entry to point to the same internal `/admission` surface so public admission entry no longer spreads the raw destination literal.
- Updated the guardian informational admission page to consume the same shared handoff component and shared resolver path as the new public admission page.

### Validation / Fallback Behavior Notes

- When `portal.admission.external_url` is valid:
  - the public `/admission` page and `guardian.info.admission` render the live external CTA
- When the config is blank or invalid:
  - both pages preserve admission guidance
  - both pages suppress the live CTA
  - both pages show neutral unavailability messaging
- Prompt-41 intentionally leaves auth forms, protected guardian routes, donor portal routes, payment routes, shared `/dashboard`, and management surfaces untouched.

## Durable Artifact Promotion

- Promoted approved decisions to `docs/codex/04-decisions/approved/prompt-41-external-admission-url-implementation.md`
- Promoted the reusable external-admission implementation workflow artifact to `docs/codex/05-artifacts/workflow/prompt-41-external-admission-url-implementation.md`

## Validation

- Route registration check:
  - `php.exe artisan route:list --path=admission`
    - result: `pass`
    - summary: `public admission route and guardian informational admission route are both registered`
- Prompt-41 focused feature validation:
  - `php.exe artisan test --env=testing tests/Feature/Phase12/ExternalAdmissionUrlImplementationTest.php`
    - result: `pass`
    - summary: `3 passed (24 assertions)`
- Sequential regression slice:
  - `php.exe artisan test --env=testing tests/Feature/Phase12/ExternalAdmissionUrlImplementationTest.php tests/Feature/Phase12/GuardianInformationalPortalTest.php tests/Feature/Phase12/GuestDonationEntryTest.php tests/Feature/Phase12/RouteMiddlewarePolicyFinalizationTest.php`
    - result: `pass`
    - summary: `14 passed (100 assertions)`
- Validation note:
  - a false migration-collision failure appeared only when two MySQL-backed test runs overlapped; rerunning sequentially returned clean results

## Contradiction / Blocker Pass

- No contradiction was found with prompt-19's canonical config ownership, validation, fallback, and approved placement rules.
- No contradiction was found with prompt-36's guardian informational boundary; the protected `/guardian` routes remain untouched and the guardian informational admission page still stays informational-only.
- No contradiction was found with prompt-40's explicit donor, guardian informational, guardian protected, and shared-home middleware finalization or its payment/invoice policy hardening.
- Prompt-41 did not reopen donor portal behavior, protected guardian payment behavior, shared-home routing, or management dashboard behavior.
- No product blocker was found.
- No correction pass is required.

## Risks

- Until a real production-safe `ADMISSION_EXTERNAL_URL` replaces the placeholder, the public and guardian informational admission surfaces will intentionally show guidance without a live external CTA.
- The MySQL-backed testing database should be validated sequentially; overlapping Laravel test runs can produce false migration collisions even when the application behavior is correct.

## Next Safe Slice

- `docs/codex/01-prompts/prompt-42-final-ui-consistency-pass.md`
