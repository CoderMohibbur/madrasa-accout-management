# Report

This prompt maps the affected screens and reusable UI building blocks without implementing code. The map inherits prompt-20's light-first institutional product family and keeps prompt-19's external-admission guardrails intact. It also separates three kinds of screens clearly: live current screens, approved future screens, and shared utility screens that support multiple domains.

## Full Screen Inventory

Status labels used below:

- `current` = already present in the repository
- `approved future` = approved by prior prompts but not yet implemented
- `shared utility` = reused across multiple domains

### Auth

- `AUTH-01` Login
  - status: `current`
  - current file/route: `resources/views/auth/login.blade.php`, `login`
  - template family: `AuthFormShell`
  - core sections: identity form, password field, remember me, recovery link
- `AUTH-02` Register
  - status: `current`
  - current file/route: `resources/views/auth/register.blade.php`, `register`
  - template family: `AuthFormShell`
  - core sections: registration form, password confirmation, cross-link to login
- `AUTH-03` Forgot Password
  - status: `current`
  - current file/route: `resources/views/auth/forgot-password.blade.php`, `password.request`
  - template family: `AuthFormShell`
  - core sections: helper copy, email field, submit action
- `AUTH-04` Reset Password
  - status: `current`
  - current file/route: `resources/views/auth/reset-password.blade.php`, `password.reset`
  - template family: `AuthFormShell`
  - core sections: token-backed form, email, password reset fields
- `AUTH-05` Verify Email Notice
  - status: `current`
  - current file/route: `resources/views/auth/verify-email.blade.php`, `verification.notice`
  - template family: `AuthNoticeShell`
  - core sections: explanation copy, resend action, logout action
- `AUTH-06` Confirm Password
  - status: `current`
  - current file/route: `resources/views/auth/confirm-password.blade.php`, `password.confirm`
  - template family: `AuthFormShell`
  - core sections: secure-area explanation, password confirmation form
- `AUTH-07` Profile Settings
  - status: `shared utility`
  - current file/route: `resources/views/profile/edit.blade.php`, `profile.edit`
  - template family: `AccountSettingsShell`
  - core sections: profile details form, password form, delete account form

### Guest Donation

- `GD-01` Public Donation Entry Shell
  - status: `approved future`
  - source approval: prompt-12 `G1`, prompt-18 public donation route bucket
  - template family: `DonationEntryTemplate`
  - core sections: mission/value intro, amount selection, guest vs identified path choice, trust/support copy
- `GD-02` Guest Donation Checkout
  - status: `approved future`
  - source approval: prompt-11, prompt-12 `G2`
  - template family: `DonationCheckoutTemplate`
  - core sections: amount summary, optional contact fields, payer preferences, payment method selection, consent copy
- `GD-03` Guest Donation Return / Outcome
  - status: `approved future`
  - source approval: prompt-12 `G2`, prompt-18 narrow status/receipt surface
  - template family: `PaymentOutcomeTemplate`
  - core sections: result summary, provider/reference metadata, next-step guidance
- `GD-04` Guest Status / Receipt Lookup
  - status: `approved future`
  - source approval: prompt-12 `H1`, prompt-18 narrow status/receipt lookup
  - template family: `LookupStatusTemplate`
  - core sections: lookup form, match result, receipt/status summary, help fallback

### Donor

- `DON-01` Identified Account-Linked Donation Entry
  - status: `approved future`
  - source approval: prompt-12 `A1`
  - template family: `DonationEntryTemplate`
  - core sections: amount selection, account-linked donor context, payment method selection, completion guidance
- `DON-02` Donor No-Portal Account State
  - status: `approved future`
  - source approval: prompt-12 `A2`
  - template family: `AccountStateTemplate`
  - core sections: donor-domain status message, next eligible actions, no-portal explanation
- `DON-03` Donor Dashboard
  - status: `current`
  - current file/route: `resources/views/donor/dashboard.blade.php`, `donor.dashboard`
  - template family: `PortalOverviewTemplate`
  - core sections: page header, stat cards, donor profile panel, recent donations, recent receipts
- `DON-04` Donor Donation History
  - status: `current`
  - current file/route: `resources/views/donor/donations/index.blade.php`, `donor.donations.index`
  - template family: `PortalListTemplate`
  - core sections: summary cards, donations data table, pagination, empty state
- `DON-05` Donor Receipt History
  - status: `current`
  - current file/route: `resources/views/donor/receipts/index.blade.php`, `donor.receipts.index`
  - template family: `PortalListTemplate`
  - core sections: summary cards, receipts data table, pagination, empty state
- `DON-06` Optional Guest Claim / Account Link
  - status: `approved future, optional`
  - source approval: prompt-12 `C1`
  - template family: `ClaimProofTemplate`
  - core sections: proof entry, match review, account-link confirmation, no-auto-link guardrails

### Guardian Informational

- `GI-01` Guardian Informational Home
  - status: `approved future`
  - source approval: prompt-15 `GI1`
  - template family: `PortalOverviewTemplate`
  - core sections: page header, institution summary, safe next actions, account/linkage status summary
- `GI-02` Guardian Institution Information
  - status: `approved future`
  - source approval: prompt-13, guardian informational requirements
  - template family: `InformationalContentTemplate`
  - core sections: institution overview, program highlights, help/contact pathways
- `GI-03` Guardian Admission Information / External Handoff
  - status: `approved future`
  - source approval: prompt-14, prompt-15 `GI2`, prompt-19
  - template family: `InformationalContentTemplate`
  - core sections: admission summary, requirements/process blocks, external CTA card, help/status note
- `GI-04` Guardian Linkage / Eligibility Status
  - status: `approved future`
  - source approval: prompt-13, prompt-15 `GA2` + `GI1`
  - template family: `AccountStateTemplate`
  - core sections: current guardian state, eligibility explanation, safe next actions, support/help guidance

### Guardian Protected

- `GP-01` Guardian Dashboard
  - status: `current`
  - current file/route: `resources/views/guardian/dashboard.blade.php`, `guardian.dashboard`
  - template family: `PortalOverviewTemplate`
  - core sections: summary cards, linked students list, guardian profile panel, recent invoices, recent payments
- `GP-02` Guardian Student Detail
  - status: `current`
  - current file/route: `resources/views/guardian/student.blade.php`, `guardian.students.show`
  - template family: `PortalDetailTemplate`
  - core sections: student identity summary, safe profile facts, invoice list, student payment table
- `GP-03` Guardian Invoice List
  - status: `current`
  - current file/route: `resources/views/guardian/invoices/index.blade.php`, `guardian.invoices.index`
  - template family: `PortalListTemplate`
  - core sections: summary cards, invoice data table, pagination, empty state
- `GP-04` Guardian Invoice Detail + Payment Entry
  - status: `current`
  - current file/route: `resources/views/guardian/invoices/show.blade.php`, `guardian.invoices.show`
  - template family: `PortalDetailTemplate`
  - core sections: summary cards, invoice facts, invoice items table, protected payment options, payment history table
- `GP-05` Guardian Payment History
  - status: `current`
  - current file/route: `resources/views/guardian/history.blade.php`, `guardian.history.index`
  - template family: `PortalListTemplate`
  - core sections: summary cards, payment data table, pagination, empty state
- `GP-06` Manual Bank Submission Review / Status
  - status: `current`
  - current file/route: `resources/views/payments/manual-bank/show.blade.php`, `payments.manual-bank.show`
  - template family: `PaymentOutcomeTemplate`
  - core sections: status summary, submitted evidence panel, bank instruction panel, optional receipt confirmation
- `GP-07` shurjoPay Outcome Screen
  - status: `current`
  - current file/route: `resources/views/payments/shurjopay/status.blade.php`, `payments.shurjopay.return.*`
  - template family: `PaymentOutcomeTemplate`
  - core sections: outcome stat cards, result explanation, verification summary, back-to-invoice/history actions

### Public Institution Info

- `PUB-01` Public Landing / Welcome
  - status: `current`
  - current file/route: `resources/views/welcome.blade.php`, `/`
  - template family: `PublicHomeTemplate`
  - core sections: hero, top navigation, primary entry links, institutional credibility/footer
- `PUB-02` Institution Overview
  - status: `approved future`
  - source approval: guardian informational requirements, public info bucket from prompt-18
  - template family: `InformationalContentTemplate`
  - core sections: institution overview, programs, values, contact/help blocks
- `PUB-03` Public Contact / Help
  - status: `approved future`
  - source approval: public institution information scope
  - template family: `InformationalContentTemplate`
  - core sections: contact methods, location/hours, inquiry/help guidance

### Admission Info

- `ADM-01` Public Admission Overview
  - status: `approved future`
  - source approval: prompt-14
  - template family: `InformationalContentTemplate`
  - core sections: admission intro, high-level process, document list, external CTA
- `ADM-02` Public Admission Requirements / FAQ
  - status: `approved future`
  - source approval: prompt-14
  - template family: `InformationalContentTemplate`
  - core sections: requirements, checklist, frequently asked questions, support/help note
- `ADM-03` Guardian Informational Admission Screen
  - status: `approved future`
  - source approval: prompt-14, prompt-15 `GI2`
  - template family: `InformationalContentTemplate`
  - core sections: same public admission core, guardian-specific guidance, external CTA, self-only informational note
- `ADM-04` External Admission Handoff Section
  - status: `shared section, not standalone page`
  - source approval: prompt-19
  - template family: `ExternalHandoffCard`
  - core sections: external CTA, disclosure, fallback/help copy when config is missing

### Multi-Role Home

- `MR-01` Shared Home Compatibility Anchor
  - status: `current`
  - current file/route: `resources/views/dashboard/index.blade.php`, `dashboard`
  - template family: `CurrentCompatibilityShell`
  - core sections: management dashboard body today; later replaced behind same route name
- `MR-02` Neutral Eligible-Context Chooser
  - status: `approved future`
  - source approval: prompt-16, prompt-17 `MR2`
  - template family: `ContextChooserTemplate`
  - core sections: eligible context cards, lightweight status copy, context actions, no mixed donor/guardian data
- `MR-03` No Eligible Context Fallback
  - status: `approved future`
  - source approval: prompt-16
  - template family: `AccountStateTemplate`
  - core sections: explanation, current account state, safe next actions
- `MR-04` In-Portal Context Switcher
  - status: `approved future shared section`
  - source approval: prompt-17 `MR3`
  - template family: `ContextSwitcher`
  - core sections: current context label, alternate eligible context links, no merged data view

## Shared Component Inventory

### Shells And Structural Sections

- `PublicShell`
- `AuthShell`
- `AppShell`
- `PortalShell`
- `PageHeader`
- `SectionHeader`
- `ActionBar`
- `Footer / institutional footer block`

### Navigation And Context Sections

- `PrimaryNav`
- `PortalSubnav`
- `Breadcrumb / back-link row`
- `ContextSwitcher`
- `ContextChooserCard`

### Summary And Display Components

- `StatCard`
- `InfoCard`
- `KeyValuePanel`
- `ProfileSummaryPanel`
- `StatusBadge`
- `MetadataPill`
- `ExternalHandoffCard`

### Data And Record Components

- `DataTable`
- `MobileRecordCard`
- `ListRow`
- `PaginationBar`
- `EmptyState`
- `LoadingSkeleton`
- `NoAccessPanel`

### Form And Feedback Components

- `FormFieldStack`
- `TextInput`
- `Textarea`
- `CheckboxRow`
- `PrimaryButton`
- `SecondaryButton`
- `DestructiveButton`
- `AlertBanner`
- `SuccessBanner`
- `ValidationSummary`

### Domain-Specific Shared Sections

- `DonationAmountSelector`
- `DonationIdentityCaptureBlock`
- `PaymentMethodBlock`
- `PaymentOutcomeSummary`
- `InvoiceSummaryPanel`
- `InvoiceItemsTable`
- `ReceiptStatusPanel`
- `LinkageStatusPanel`
- `AdmissionRequirementsBlock`
- `HelpContactBlock`

## Layout / Template Reuse Map

### `AuthFormShell`

- `AUTH-01`, `AUTH-02`, `AUTH-03`, `AUTH-04`, `AUTH-06`
- shared body pattern: intro copy, field stack, primary action, secondary link

### `AuthNoticeShell`

- `AUTH-05`
- same shell as auth, but with notice-and-actions body instead of dense form

### `AccountSettingsShell`

- `AUTH-07`
- reusable later for donor, guardian, and multi-role shared settings

### `PublicHomeTemplate`

- `PUB-01`
- reusable for the future public institution landing refresh

### `InformationalContentTemplate`

- `PUB-02`, `PUB-03`, `ADM-01`, `ADM-02`, `ADM-03`, `GI-02`, `GI-03`
- same structure: intro header, content blocks, help block, optional external CTA section

### `PortalOverviewTemplate`

- `DON-03`, `GP-01`, `GI-01`
- same structure: page header, summary cards, profile/context panel, recent activity or guidance panels

### `PortalListTemplate`

- `DON-04`, `DON-05`, `GP-03`, `GP-05`
- same structure: summary cards, panel-wrapped table/list, pagination, empty state

### `PortalDetailTemplate`

- `GP-02`, `GP-04`
- same structure: header, summary facts, related records, secondary sections, contextual actions

### `PaymentOutcomeTemplate`

- `GP-06`, `GP-07`, `GD-03`, `GD-04`
- same structure: status summary, reference metadata, next-step actions, optional receipt/result panel

### `DonationEntryTemplate`

- `GD-01`, `DON-01`
- same structure: giving intent intro, amount selection, identity context, next action block

### `DonationCheckoutTemplate`

- `GD-02`
- reusable later for identified and guest checkout variants with different identity blocks only

### `AccountStateTemplate`

- `DON-02`, `GI-04`, `MR-03`
- same structure: current state summary, explanation, eligible actions, support/help guidance

### `ClaimProofTemplate`

- `DON-06`
- reusable only if optional claim flow is later approved

### `ContextChooserTemplate`

- `MR-02`
- dedicated neutral chooser template; not reused for donor or guardian dashboards

## Minimum Shared Component Library

Before prompt-22 component planning and later prompt-28 implementation, the minimum shared library should include:

- shell layer:
  - `PublicShell`
  - `AuthShell`
  - `AppShell`
  - `PortalShell`
- page structure:
  - `PageHeader`
  - `SectionHeader`
  - `ActionBar`
  - `Footer`
- controls:
  - `PrimaryButton`
  - `SecondaryButton`
  - `DestructiveButton`
  - `TextInput`
  - `Textarea`
  - `CheckboxRow`
  - `FormFieldStack`
- display primitives:
  - `StatCard`
  - `InfoCard`
  - `KeyValuePanel`
  - `StatusBadge`
  - `MetadataPill`
- data presentation:
  - `DataTable`
  - `MobileRecordCard`
  - `PaginationBar`
- state handling:
  - `AlertBanner`
  - `SuccessBanner`
  - `ValidationSummary`
  - `EmptyState`
  - `LoadingSkeleton`
  - `NoAccessPanel`
- cross-domain sections:
  - `ExternalHandoffCard`
  - `PaymentOutcomeSummary`
  - `InvoiceSummaryPanel`
  - `ReceiptStatusPanel`
  - `LinkageStatusPanel`
  - `HelpContactBlock`

This is the smallest library that lets prompts 22 and 42 inherit a consistent product family without forcing donor, guardian, public, auth, and multi-role pages into separate design systems.

## Contradiction / Blocker Pass

- No contradiction with prompt-20 was found.
- The institutional product family direction remains unchanged and is reused directly.
- This run stayed in screen-and-component mapping scope only; no implementation work was proposed.
- Prompt-19's external-admission guardrails remain preserved:
  - no auth admission screen was introduced
  - no current protected guardian screen was repurposed for admission entry
  - the external handoff remains a shared section, not an internal application flow
- No correction pass is required.
- No hard blocker prevents prompt-22.
