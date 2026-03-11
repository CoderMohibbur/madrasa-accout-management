# Prompt 40 Route Middleware Policy Finalization

Approved implementation decisions from prompt-40:

- `/donor` is now finalized on explicit donor-eligibility middleware.
- `/guardian/info*` is now finalized on explicit guardian-informational eligibility middleware.
- The protected `/guardian` route space remains on dedicated `guardian.protected` middleware, and `/dashboard` keeps the chooser-aware shared-home middleware path.
- `payments.manual-bank.show` and the shurjoPay browser return routes no longer depend on blanket `verified`; reusable payment ownership policy checks now control payment detail access.
- `StudentFeeInvoicePolicy::pay()` is the reusable invoice-payment authorization rule, and payment initiation now uses that policy from both form requests and the payable resolver.
- Protected student, invoice, and receipt policies now align with the actual protected guardian eligibility boundary rather than only profile flags.
- Legacy management route names and behavior remain preserved, while donor-profile users without donor roles are now blocked from falling through to legacy management surfaces.
