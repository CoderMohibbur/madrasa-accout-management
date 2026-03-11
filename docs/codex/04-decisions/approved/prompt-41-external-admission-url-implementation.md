# Prompt 41 External Admission URL Implementation

Approved implementation decisions from prompt-41:

- `ADMISSION_EXTERNAL_URL` remains the single environment-owned source for the external admission destination, exposed only through `portal.admission.external_url`.
- External admission URL validation now runs through one shared resolver that trims empty values, accepts only absolute `https://` URLs, and rejects same-host protected internal route destinations such as `/guardian`, `/donor`, `/dashboard`, `/management`, and `/payments`.
- Public navigation now points admission entry to one internal `admission` route instead of embedding the live off-site destination directly in public Blade templates.
- The new public `/admission` page and the existing guardian informational admission page share the same external handoff rendering path, so CTA availability and fallback messaging stay consistent.
- Missing or invalid admission configuration preserves public and guardian-informational admission guidance while suppressing the live CTA instead of guessing another destination.
- Prompt-40's donor, guardian informational, guardian protected, shared-home, payment-policy, and management-surface hardening remain unchanged.
