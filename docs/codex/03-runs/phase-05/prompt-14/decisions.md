# Decisions

- Admission scope is approved as informational-only with an external application handoff; this Laravel project must not become a full admission application system.
- Public admission information may include only generic, non-personalized institution and admission content plus the external application CTA.
- Authenticated admission information belongs inside the light guardian informational portal and may add only self-only onboarding/help/status messaging, not protected student or application workflow data.
- The external admission button/link is an explicit handoff boundary and must remain separate from guardian protected access, donor flows, and internal student/invoice/payment data.
- One canonical external admission destination should be reused across approved placements later; hard-coded duplicate links are a current-state detail, not the final pattern.
- Prompt-13 guardian informational/protected separation remains preserved and was not reopened by prompt-14.
