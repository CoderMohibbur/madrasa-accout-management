# Next Step

Run `docs/codex/01-prompts/prompt-42-final-ui-consistency-pass.md` next.

Carry forward:
- preserve prompt-40's explicit donor, guardian informational, guardian protected, and shared-home middleware boundaries
- preserve prompt-40's reusable payment, invoice, student, and receipt policy hardening plus management-surface hardening
- keep the external admission destination external-only and config-backed through `ADMISSION_EXTERNAL_URL` -> `portal.admission.external_url`
- keep admission CTA placement limited to approved public and guardian-informational surfaces only
- do not spread admission CTA behavior onto auth forms, protected guardian routes, donor portal routes, payment routes, shared `/dashboard`, or management surfaces unless a later prompt explicitly approves it
