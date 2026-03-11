# Decisions

- `/donor` now uses explicit donor-eligibility middleware instead of a plain authenticated route edge.
- `/guardian/info*` now uses explicit guardian-informational eligibility middleware instead of a plain authenticated route edge.
- `payments.manual-bank.show` and the shurjoPay browser return routes no longer depend on blanket `verified`; payment detail access now resolves through reusable payment ownership policy checks.
- `StudentFeeInvoicePolicy::pay()` is the reusable object-level rule for guardian invoice payment initiation, and both payment form requests plus the payable resolver now use it.
- `StudentPolicy`, `StudentFeeInvoicePolicy`, and `ReceiptPolicy` now align with the protected guardian eligibility model instead of only profile-flag checks.
- Legacy management route names and dashboard behavior remain unchanged, but donor-profile users without donor roles are now blocked from falling through to legacy management surfaces.
- Prompt-39's chooser, switching, donor/guardian isolation, and management-dashboard separation remain intact.
