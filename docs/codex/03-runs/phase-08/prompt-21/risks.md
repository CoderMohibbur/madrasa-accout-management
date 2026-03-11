# Risks

- If prompt-22 plans components directly from current file boundaries instead of the shared template families defined here, donor and guardian layout duplication will continue.
- If future public/admission screens are mapped separately from `InformationalContentTemplate`, prompt-42 will face avoidable consistency cleanup across public and guardian-informational content.
- If payment result surfaces are treated as guardian-only one-offs, later guest or donor narrow status/receipt access will require redundant UI work instead of reusing `PaymentOutcomeTemplate`.
- If the shared component library omits mobile record-card fallbacks, list-heavy screens will remain dependent on horizontal-scroll tables on phones.
- If the external admission handoff is reinterpreted later as a standalone internal workflow instead of a shared external CTA section, prompt-19 and prompt-14 boundaries will be broken.
