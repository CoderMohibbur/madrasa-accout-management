Use the docs/codex-autopilot workflow from my project.

This task is PHASE_1_BUSINESS_RULE_FREEZE_ONLY.
Do not implement code yet.

Business requirements to analyze and normalize:
- anyone should be able to register
- donor must be able to self-register
- guardian must be able to self-register
- verification must support both email verification and phone verification
- both verification options should exist in the system
- donors must be able to register and log in even if they do not verify email or phone
- donors must be able to donate even if they do not verify email or phone, subject to the safest allowed payment-domain rules
- donation must not require prior registration
- guest donation must be allowed
- a donor must be able to donate directly by amount without mandatory account creation
- donor identity fields such as name, phone, and email may be optional during donation, subject to safe payment, reconciliation, and abuse-control rules
- if phone or email is provided during donation, the system may create or attach a lightweight donor/user identity according to approved rules
- if no usable identity data is provided, the donation must still be accepted and recorded safely as guest / anonymous / hidden donor according to approved canonical terminology
- anonymous or guest donation must not require portal access
- receipt, payment traceability, anti-abuse controls, reconciliation safety, and reporting safety must be preserved for guest or anonymous donations
- guardian users may also log in without completing verification
- after login, an unverified or unlinked guardian should still be able to view non-sensitive institution/panel information
- after login, a guardian should be able to view admission-related information
- this project should NOT implement a full admission application system
- this project only needs to provide information and a button/link to an external admission application
- one user may be both donor and guardian at the same time
- the same authenticated account must be able to access both donor and guardian portal surfaces if it legitimately holds both roles
- Google sign-in must be supported as an optional one-click sign-in / registration method
- Google sign-in should work especially for donor/public onboarding and should be designed safely for guardian onboarding too
- student-linked academic, invoice, receipt, and payment-sensitive access must remain linkage- or authorization-controlled
- existing accounting, reporting, guardian invoice payment, donor/payment safety, and legacy management logic must remain protected
- UI/UX must follow one global-standard, modern, consistent design system
- existing weak or inconsistent UI patterns do not need to be preserved

Do only this:
1) normalize these into exact business rules
2) separate hard must-have rules from optional or later-phase enhancements
3) identify ambiguous points that need interpretation
4) choose the safest interpretation for each ambiguous point
5) identify forbidden scope expansions
6) produce the final frozen business-rule list that all later prompts must follow

Do not propose schema or code changes yet.

End with:
- frozen business rules
- hard must-haves
- optional or later-phase items
- ambiguities and chosen interpretations
- forbidden scope expansions
- whether business rules are sufficiently frozen to proceed
