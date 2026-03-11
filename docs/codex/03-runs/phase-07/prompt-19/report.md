# Report

This prompt analyzes how the repository should own and reuse the external admission destination without implementing code. Current-state review found one live hard-coded admission URL in `resources/views/welcome.blade.php`, no config key for that destination in `config/*.php`, no admission CTA in the current auth views, and only the already-protected guardian portal under `/guardian`. That means prompt-19 must preserve prompt-18's additive boundary: keep live `/guardian` protected, keep donor and multi-role work in their approved lanes, and introduce the external admission handoff only through public and future guardian-informational surfaces.

## Current-State Findings

- The only live external admission destination is the literal `https://attawheedic.com/admission/` link in the public welcome-page navigation.
- `routes/web.php` still serves `/` directly from `welcome.blade.php`; there is not yet a first-class public admission-information route.
- `resources/views/auth/login.blade.php`, `resources/views/auth/register.blade.php`, and `resources/views/auth/verify-email.blade.php` currently have no admission CTA, which is consistent with prompt-14's boundary.
- `routes/web.php`, `routes/guardian.php`, `app/Http/Controllers/Guardian/GuardianPortalController.php`, and the current guardian views still define a protected student/invoice/payment portal, not a light informational guardian space.
- Existing config conventions favor domain-specific config files such as `config/payments.php`; there is no current pattern that justifies keeping this URL hard-coded in Blade.

## Config Design

The safest design is to move the external admission destination into one dedicated domain config entry and make every future placement consume that one canonical value.

### Recommended Ownership Model

1. Environment owns the environment-specific destination only.
   - Proposed key: `ADMISSION_EXTERNAL_URL`
2. A dedicated domain config file should normalize and expose that value.
   - Preferred location: `config/portal.php`
   - Preferred key shape: `portal.admission.external_url`
3. Routes, controllers, Blade views, and JS should consume only the config key.
   - No raw `env()` calls in views or controllers
   - No repeated literal URL strings

### Why `config/portal.php` Is The Best Fit

- This setting is a portal/public-surface boundary, not a credential.
- It has UI availability implications, so it belongs with domain configuration rather than `config/services.php`.
- It leaves room for prompt-36 and prompt-41 to reuse the same admission config from both public and guardian-informational surfaces without widening scope into donor or management settings.

### What Should Not Be Environment-Owned

- Internal route names
- placement decisions
- button copy or translation text
- guardian-protected versus guardian-informational access rules

Those remain application-code concerns so prompt-18's route and policy decisions stay separate from the external destination itself.

## Validation / Fallback Rules

### Validation Rules

- Trim the configured value and treat an empty string as not configured.
- Accept only an absolute `https://` URL with a non-empty host.
- Reject relative paths, protocol-relative URLs, `javascript:` URLs, `mailto:` links, and any other non-browser-safe scheme.
- Reject any attempt to repurpose the value as an internal protected path such as `/guardian`, `/donor`, `/dashboard`, or a payment route. The admission CTA must remain an external handoff boundary.

### Fallback Rules

- If the config value is missing or invalid, public and guardian-informational admission content may still render, but the external CTA must not render as a live link.
- The failure mode should be graceful:
  - hide or disable the CTA
  - show neutral informational copy that online admission is unavailable or must be requested through the institution
- Do not silently fall back to a guessed hard-coded URL.
- Do not fall back to any protected internal route, donor surface, management page, or payment flow.

### Safer Navigation Pattern

The safest long-term pattern is:

1. app navigation points to one canonical internal admission-information surface
2. that surface renders the external CTA from config

This keeps public information available even when the external destination is missing and avoids spreading raw off-site links across the application shell.

## Approved UI Placements

### Approved Public Placements

- the public landing experience on `/`, including a top-level admission entry point
- future additive public admission-information page(s) approved by prompt-14 and prompt-18
- public CTA sections whose only job is to hand the user off to the external admission site

### Approved Guardian-Informational Placements

- a future authenticated guardian-informational dashboard card
- a future guardian-informational admission/help page
- future self-only onboarding/help messaging inside the separate guardian-informational route space

These are approved because prompt-14 allows authenticated admission information only inside the light guardian-informational portal, not inside the protected guardian portal.

### Explicitly Not Approved

- current protected `/guardian` dashboard, student, invoice, and history views
- donor portal views
- shared `/dashboard`
- payment initiation, status, receipt, or review surfaces
- management/reporting pages
- generic auth forms (`login`, `register`, `verify-email`) as admission CTA surfaces

The auth views were reviewed to confirm they are currently neutral guest/auth pages. Keeping them neutral avoids implying that admission requires login or that the auth layer owns admission flow.

## Duplication-Prevention Rules

- One environment key -> one config key -> one shared consumer path.
- No literal admission URL in Blade templates, controllers, routes, JS, or tests outside config/docs fixtures.
- Public navigation should eventually target one internal admission-information route instead of embedding the external destination in multiple menus.
- Public admission CTA rendering and guardian-informational CTA rendering should share one resolver/helper/component so availability checks and fallback copy stay consistent.
- Route-level separation stays distinct from object-level protection:
  - public and guardian-informational surfaces may show the CTA
  - student/invoice/payment ownership policies remain untouched
- Prompt-41 should also record the new environment key in `docs/codex/06-production-replace/env-placeholders.md` when the implementation lands.

## Contradiction / Blocker Pass

- No contradiction with prompt-14 or prompt-18 was found.
- Donor, guardian, and multi-role work stayed in their approved lanes.
- The additive route approach remains preserved:
  - live `/guardian` stays protected
  - only public and future guardian-informational surfaces are approved for the admission CTA
- No correction pass is required.
- No hard blocker prevents prompt-20.
