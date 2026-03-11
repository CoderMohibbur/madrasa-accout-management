# Prompt 21 Screen And Component Map

## Screen Families

### Auth

- login
- register
- forgot password
- reset password
- verify email notice
- confirm password
- profile settings

### Guest Donation

- public donation entry shell
- guest checkout
- donation outcome / return
- status / receipt lookup

### Donor

- identified donation entry
- donor no-portal account state
- donor dashboard
- donation history
- receipt history
- optional claim / account-link flow

### Guardian Informational

- informational home
- institution information
- admission information / external handoff
- linkage / eligibility status

### Guardian Protected

- dashboard
- student detail
- invoice list
- invoice detail + payment entry
- payment history
- manual bank review / status
- shurjoPay outcome

### Public Institution Info

- public landing
- institution overview
- contact / help

### Admission Info

- public admission overview
- public admission requirements / FAQ
- guardian informational admission screen
- external admission handoff section

### Multi-Role Home

- shared home compatibility anchor
- neutral chooser
- no-eligible-context fallback
- in-portal context switcher

## Shared Template Families

- `AuthFormShell`
- `AuthNoticeShell`
- `AccountSettingsShell`
- `PublicHomeTemplate`
- `InformationalContentTemplate`
- `PortalOverviewTemplate`
- `PortalListTemplate`
- `PortalDetailTemplate`
- `PaymentOutcomeTemplate`
- `DonationEntryTemplate`
- `DonationCheckoutTemplate`
- `AccountStateTemplate`
- `ClaimProofTemplate`
- `ContextChooserTemplate`

## Reusable Components / Sections

- shells:
  - `PublicShell`
  - `AuthShell`
  - `AppShell`
  - `PortalShell`
- structure:
  - `PageHeader`
  - `SectionHeader`
  - `ActionBar`
  - `PrimaryNav`
  - `PortalSubnav`
  - `Footer`
- display:
  - `StatCard`
  - `InfoCard`
  - `KeyValuePanel`
  - `ProfileSummaryPanel`
  - `StatusBadge`
  - `MetadataPill`
- data:
  - `DataTable`
  - `MobileRecordCard`
  - `ListRow`
  - `PaginationBar`
- forms and feedback:
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
  - `EmptyState`
  - `LoadingSkeleton`
  - `NoAccessPanel`
- domain-shared sections:
  - `ExternalHandoffCard`
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
  - `ContextSwitcher`
  - `ContextChooserCard`

## Minimum Shared Component Library

- shells + navigation
- page headers + section headers + action bars
- button family
- field family
- alert / validation family
- card family
- data table + mobile record-card fallback
- pagination
- empty / loading / no-access states
- external handoff card
- payment outcome summary
- invoice summary panel
- linkage status panel
- help/contact block

## Guardrails Carried Forward

- prompt-20 product family direction remains the baseline
- prompt-19 remains in force
- no auth or current protected guardian admission-entry screen is introduced here
- admission handoff stays external-only
