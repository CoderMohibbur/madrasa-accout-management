# Decisions

- The affected screen map will distinguish current screens, approved future screens, and shared utility screens so prompts 22 and 42 can plan and verify consistently.
- Auth, public information, donor, guardian informational, guardian protected, guest donation, admission information, and multi-role home can all be expressed through a small set of shared template families instead of bespoke layouts per domain.
- The core reusable template families are `AuthFormShell`, `InformationalContentTemplate`, `PortalOverviewTemplate`, `PortalListTemplate`, `PortalDetailTemplate`, `PaymentOutcomeTemplate`, `DonationEntryTemplate`, `AccountStateTemplate`, and `ContextChooserTemplate`.
- The minimum shared component library must prioritize shells, page headers, cards, tables, forms, state banners, and cross-domain sections like `ExternalHandoffCard`, `PaymentOutcomeSummary`, and `LinkageStatusPanel`.
- Prompt-19 remains preserved inside the map: admission handoff is modeled as a shared external-handoff section for approved public and guardian-informational screens only, not as an auth or protected-guardian screen family.
