# Risks

- The live repository still uses blanket `verified` middleware for all guardian routes, so later implementation prompts must avoid accidentally keeping verification as the global substitute for informational eligibility or linkage.
- The current guardian portal dashboard already contains linked-student, invoice, and payment summaries, so later implementation must not repurpose it as the informational portal without stripping protected data first.
- The current guardian invoice and payment flows are tightly coupled to `StudentFeeInvoice`, `PaymentWorkflowService`, and guardian ownership rules; later prompts must preserve those protections while widening access only for the new informational portal.
- If linkage state stays implicit in pivot rows and invoice ownership alone, later implementation could blur unlinked informational guardians with linked protected guardians.
- Multi-role users currently redirect to guardian first on `/dashboard`, which is narrower than the approved eligibility-driven model and could hide donor/guardian context separation if copied forward.
- Google sign-in remains a later delta; implementing guardian onboarding around it prematurely would risk bypassing the still-unbuilt informational-vs-protected separation.
