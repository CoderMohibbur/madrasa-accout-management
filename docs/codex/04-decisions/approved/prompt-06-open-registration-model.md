# Prompt 06 Open Registration Model

Approved baseline decisions from prompt-06:

- Use one public registration backend with optional donor- and guardian-branded entry pages that only preselect intent.
- Registration creates only a base account plus domain intent or a non-eligible draft profile; it does not auto-grant portal eligibility, guardian linkage, or protected access.
- Do not auto-assign the current donor or guardian route-driving roles during open registration while the live repo still routes by `verified` and `role:*`.
- Guardian self-registration may create only an unlinked informational-state domain record; protected guardian access still requires later linkage and authorization.
- Later donor and guardian expansion must happen on the same account through duplicate-safe linking and explicit multi-context separation.
