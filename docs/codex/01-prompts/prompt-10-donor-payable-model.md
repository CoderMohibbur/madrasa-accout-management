Use the docs/codex-autopilot workflow from my project.

This task is PHASE_4_DONOR_PAYABLE_MODEL_ANALYSIS_ONLY.
Do not implement code yet.

Read:
1) app/Models/Payment.php
2) app/Models/Transactions.php
3) donor and payment routes/controllers/services
4) docs/codex-autopilot/state/risk_register.md

Do only this:
1) analyze why legacy transactions are unsafe or insufficient for donor live-payment finalization
2) design the safest donor payment-domain model for:
   - guest donation
   - identified donation
   - anonymous-display donation
3) decide whether the system needs:
   - donation intent
   - donation payable
   - donation record
   - receipt record
   - payment attempt record
4) define how payment success, failure, retry, callback, cancellation, timeout, and reconciliation should work
5) define how receipts should work for guest vs identified donors
6) define what must remain separate from legacy accounting posting
7) define the smallest safe live-payment rollout model

Do not implement code.

End with:
- legacy donor payment risks
- target donor payable model
- guest/identified donation record model
- receipt model
- posting separation rules
- minimal safe live-payment rollout model
