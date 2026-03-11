# Risks

- The current public welcome page already hard-codes the external admission URL, so later implementation must avoid spreading that duplication into more views before prompt-19 defines the safe config pattern.
- If later guardian informational pages reuse current protected guardian layouts or dashboards too directly, student/invoice/payment-sensitive elements could leak into what should remain admission-only informational space.
- Admission-related copy can drift if public and authenticated informational surfaces are authored separately instead of sharing one small approved content set.
- Later implementation must be careful not to blur admission information with enrollment, student creation, or payment collection just because the legacy repo already has student-admission and fee-entry management screens.
- Because no internal admission-information page exists yet, later implementation prompts must add content without accidentally implying that the Laravel app owns the external application workflow.
