# Decisions

- Use one unified public registration backend with optional donor- and guardian-branded entry pages that only preselect intent.
- Registration creates a base account plus optional domain intent or a non-eligible draft profile; it does not automatically create portal eligibility, guardian linkage, or protected access.
- Do not auto-assign the current donor or guardian route-driving roles during open registration while the live repo still routes those roles into protected portal surfaces.
- Donor self-registration may create only donor intent or one inactive donor profile; donor portal eligibility remains a later derived state.
- Guardian self-registration may create only guardian intent or one unlinked guardian profile; guardian protected access still requires later linkage and authorization.
- Later donor-to-guardian or guardian-to-donor expansion must happen on the same account through duplicate-safe linking and explicit context separation.
