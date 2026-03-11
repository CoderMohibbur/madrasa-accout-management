# Input

Use the docs/codex-autopilot workflow from my project.

This task is PHASE_4_GUEST_DONATION_ONBOARDING_ANALYSIS_ONLY.
Do not implement code yet.

Adapt this run using approved prompt-01 to prompt-10 outputs:
- preserve guest donation as a required target behavior
- preserve donor login and donor donation as verification-independent
- preserve donor portal access and donation capability as separate boundaries
- preserve the prompt-10 donor payable model:
  - one dedicated pre-settlement `donation_intent`
  - `payments` as the attempt table
  - `donation_record` as post-settlement truth
  - receipts as payment-specific and transaction-scoped first
- preserve the rule that guest contact capture does not auto-create portal eligibility, donor linkage, or protected access
- preserve the rule that legacy `transactions` are not safe for donor live-payment finalization
- preserve the canonical donor terms: `guest donor`, `identified donor`, and `anonymous-display donor`
- keep verification, approval, role assignment, portal eligibility, and guardian linkage separate
- treat Google sign-in as a planned delta only, not part of this prompt
- do not collapse guest donation, identified donation, and anonymous-display donation into one undifferentiated flow

Read:
1. donor routes/controllers/views
2. payment routes/controllers/views
3. `app/Models/User.php`
4. `app/Models/Donor.php`
5. `app/Models/Payment.php`
6. prior prompt-06 to prompt-10 reports and approved decisions

Do only this:
1. define the exact guest-donation flow from entry to completion
2. define which donor identity fields are optional, required, or conditionally required
3. define when the system should:
   - create no account
   - create a lightweight user
   - create or attach a donor profile
   - only store payment-side contact information
4. define what should happen when phone or email is present but unverified
5. define whether and how a guest donor can later claim or convert donation history into a portal account
6. define the safest anti-abuse and traceability rules for guest donation

Explicitly account for:
- donation without prior registration
- direct amount-based donation entry
- optional name, phone, and email capture
- when the system should create no account
- when the system should create a lightweight user
- when the system should create or attach a donor profile
- when the system should only store payment-side contact information
- what happens when phone or email is present but unverified
- whether and how a guest donor may later claim or convert donation history into a portal account
- anti-abuse, traceability, receipt, and reconciliation safety requirements
- `guest donor` vs `identified donor` vs `anonymous-display donor` terminology
- the rule that guest donation must not silently create portal access

Do not implement code.

End with:
- guest donation flow
- identity-capture rules
- lightweight account-creation rules
- later account-claim rules
- anti-abuse and traceability rules
