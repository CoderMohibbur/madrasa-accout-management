# Decisions

- `ADMISSION_EXTERNAL_URL` remains the single environment-owned source for the external admission destination, exposed only through `portal.admission.external_url`.
- A shared `ExternalAdmissionUrlResolver` now owns admission URL validation for all approved surfaces.
- Public admission entry now points to one internal `admission` route instead of embedding the live external destination directly in public Blade templates.
- The public `/admission` page and the guardian informational admission page now share the same external handoff rendering path, so CTA availability and fallback messaging stay consistent.
- Same-host protected internal route destinations such as `/guardian`, `/donor`, `/dashboard`, `/management`, and `/payments` are rejected as admission handoff values.
- Missing or invalid admission configuration preserves admission guidance while suppressing the live CTA instead of guessing another destination.
- Prompt-40's donor, guardian informational, guardian protected, payment-policy, and management-surface hardening remains unchanged.
