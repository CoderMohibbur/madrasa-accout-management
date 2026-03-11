# Prompt 19 External Admission URL Configuration

## Canonical Source

- environment key: `ADMISSION_EXTERNAL_URL`
- preferred config key: `portal.admission.external_url`
- preferred config file: `config/portal.php`

## Ownership Split

- environment/config owns only the external destination
- code owns:
  - internal route names
  - placement decisions
  - CTA copy
  - access boundaries between public, guardian-informational, and protected surfaces

## Validation Rules

- trim empty values to unconfigured
- accept only absolute `https://` URLs
- reject relative/internal paths and unsafe schemes
- never treat protected Laravel routes as fallback destinations

## Fallback Behavior

- keep admission information visible even when the destination is unavailable
- suppress or disable the live CTA when config is missing/invalid
- show neutral informational messaging instead of guessing another URL

## Approved Placements

- public landing admission entry
- public admission-information page(s)
- future guardian-informational dashboard/admission-help surface

## Not Approved

- current protected `/guardian` views
- donor portal views
- shared `/dashboard`
- auth forms
- payment or management surfaces

## Duplication Guard

- one config key
- one shared resolver/component
- no hard-coded destination literals in Blade, controllers, routes, or JS
- public navigation should prefer one internal admission-information route that renders the shared CTA
