# Risks

- If multi-role home behavior keeps using raw role order, donor-plus-guardian users will continue to be routed guardian-first and the second eligible context will remain hidden behind manual URL entry.
- If a neutral chooser shows live donor totals or guardian balances, it will collapse two approved scopes into one mixed-sensitivity surface.
- If switching logic keys off role membership instead of full context eligibility, unlinked or informational-only guardian users could be sent toward protected guardian routes too early.
- Donor and guardian receipts use different entitlement paths today; any future merged receipt presentation would risk leaking invoice-derived guardian receipts into donor-facing history or donor receipts into guardian-facing views.
- The live repo still relies on blanket `verified` middleware, so implementation must not mistake today’s narrower access chain for the final target model.
- Any later identity-claim or account-link flow that matches by email alone would risk silently merging donor ownership, guardian linkage, or portal access in ways the approved boundaries do not allow.
