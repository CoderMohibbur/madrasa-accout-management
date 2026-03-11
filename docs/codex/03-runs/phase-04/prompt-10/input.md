# Input

Use the docs/codex-autopilot workflow from my project.

This task is PHASE_4_DONOR_PAYABLE_MODEL_ANALYSIS_ONLY.
Do not implement code yet.

Adapt this run using approved prompt-01 to prompt-09 outputs:
- preserve guest donation as a required target behavior
- preserve donor login and donor donation as verification-independent
- preserve the prompt-09 split between payment ability and donor portal eligibility
- preserve the prompt-06 rule that registration alone does not grant portal eligibility, linkage, or protected access
- preserve the canonical donor terms: `guest donor`, `identified donor`, and `anonymous-display donor`
- keep verification, approval, role assignment, portal eligibility, and guardian linkage separate
- treat Google sign-in as a planned delta only, not part of this payable model
- do not assume legacy `transactions` are safe for donor live-payment finalization
- do not assume legacy `transactions` are safe for direct donor live-payment posting

Read:
1. `app/Models/Payment.php`
2. `app/Models/Transactions.php`
3. `app/Models/Receipt.php`
4. donor and payment routes/controllers/services
5. `docs/codex-autopilot/state/risk_register.md`

Do only this:
1. analyze why legacy `transactions` are unsafe or insufficient for donor live-payment finalization
2. design the safest donor payment-domain model for:
   - guest donation
   - identified donation
   - anonymous-display donation
3. decide whether the system needs:
   - donation intent
   - donation payable
   - donation record
   - receipt record
   - payment attempt record
4. define how payment success, failure, retry, callback, cancellation, timeout, and reconciliation should work
5. define how receipts should work for guest vs identified donors
6. define what must remain separate from legacy accounting posting
7. define the smallest safe live-payment rollout model

Explicitly account for:
- guest donation
- identified donation
- anonymous-display donation
- donation intent / donation payable / donation record separation if needed
- receipt model boundaries
- payment success / failure / retry / callback / reconciliation flow
- separation from legacy accounting posting
- the rule that legacy `transactions` must not be assumed safe for direct donor live-payment finalization

Do not implement code.

End with:
- legacy donor payment risks
- target donor payable model
- guest/identified donation record model
- receipt model
- posting separation rules
- minimal safe live-payment rollout model
