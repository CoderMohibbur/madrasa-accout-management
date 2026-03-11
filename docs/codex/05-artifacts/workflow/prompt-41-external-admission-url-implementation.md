# Prompt 41 External Admission URL Implementation

## Canonical Configuration Path

- environment key: `ADMISSION_EXTERNAL_URL`
- config key: `portal.admission.external_url`
- shared resolver: `App\Services\Portal\ExternalAdmissionUrlResolver`

## Validation And Fallback Rules

- trim empty values to unconfigured
- accept only absolute `https://` URLs with a host
- reject same-host protected internal route destinations:
  - `/dashboard`
  - `/donor*`
  - `/guardian*`
  - `/management*`
  - `/payments*`
- when missing or invalid:
  - keep admission information visible
  - suppress the live CTA
  - show neutral informational messaging
  - never guess another URL

## Approved Surfaces Implemented

- public navigation admission entry
  - now points to internal `admission`
- public admission information page
  - `GET /admission`
  - renders the shared external handoff card
- guardian informational admission page
  - `GET /guardian/info/admission`
  - reuses the same shared external handoff card and resolver

## Explicitly Preserved Boundaries

- no admission CTA added to auth forms
- no admission CTA added to protected `/guardian` routes
- no admission CTA added to donor portal, payment, shared `/dashboard`, or management surfaces
- prompt-40 route, middleware, and policy finalization remains intact
