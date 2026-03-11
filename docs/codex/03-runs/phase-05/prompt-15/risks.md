# Risks

- `GA2` can easily re-entrench the current `email_verified_at` and `verified` coupling if it is attempted before prompts 29-32 establish the shared account and verification foundations.
- Reusing the existing protected guardian dashboard as the informational portal would risk leaking linked-student, invoice, and payment summaries into a surface that must remain non-sensitive.
- `GP2` carries direct data-leakage risk because student, invoice, receipt, and payment history surfaces already exist live and must stay linkage-controlled during any route split.
- `GP3` carries the highest safety risk because guardian payment initiation and history are already live and tightly coupled to invoice ownership, payment workflow logic, and management review.
- External admission URL centralization is intentionally deferred; later implementation must avoid spreading more hard-coded links before prompt-41 formalizes the safe config pattern.
- Multi-role routing remains a later concern, so guardian slice implementation must avoid hard-coding guardian-first behavior as if it were the final cross-role model.
