# Report

This prompt plans the smallest safe UI implementation slices without implementing code. The slice plan inherits prompt-20's institutional product family and prompt-21's durable screen/component map, then sequences work so shared foundations land before any feature-page styling changes. The key safety rule is additive-first: build reusable UI primitives in prompt-28, then apply them to new or already-approved feature surfaces in later prompts, and leave broader public/auth/management normalization until later targeted prompts or final consistency cleanup.

## UI Slice Order

1. `UF1` design-token contract
2. `UF2` shell and header foundation
3. `UF3` feedback and state pattern foundation
4. `UF4` form and data-display primitive foundation
5. `AU1` auth and account-state UI adoption
6. `GD1` public donor-entry and guest-donation UI activation
7. `DO1` donor portal normalization
8. `GI1` guardian informational shell and content activation
9. `GP1` guardian protected normalization
10. `MR1` multi-role chooser and switcher UI
11. `AD1` public/guardian admission handoff activation
12. `FC1` final cross-product consistency sweep

## Shared-Foundation-First Sequence

### `UF1` Design-Token Contract

- scope:
  - semantic color tokens
  - spacing scale
  - typography scale
  - radius/shadow scale
  - focus-ring token
  - content-width tokens
- uses prompt-20 baseline directly
- primary implementation target: prompt-28
- safest delivery rule:
  - additive tokens first
  - no mass page rewrites yet

### `UF2` Shell And Header Foundation

- scope:
  - `PublicShell`
  - `AuthShell`
  - `AppShell`
  - `PortalShell`
  - `PageHeader`
  - `SectionHeader`
  - `ActionBar`
  - shared footer and navigation framing primitives
- depends on `UF1`
- primary implementation target: prompt-28
- safety note:
  - donor and guardian should converge on one shared portal shell pattern here
  - do not rewrite all existing screens in the same slice

### `UF3` Feedback And State Pattern Foundation

- scope:
  - `AlertBanner`
  - `SuccessBanner`
  - `ValidationSummary`
  - `EmptyState`
  - `LoadingSkeleton`
  - `NoAccessPanel`
  - `StatusBadge`
- depends on `UF1`
- primary implementation target: prompt-28
- independent value:
  - these can be reused immediately by later donor, guardian, multi-role, and admission work

### `UF4` Form And Data-Display Primitive Foundation

- scope:
  - `PrimaryButton`
  - `SecondaryButton`
  - `DestructiveButton`
  - `FormFieldStack`
  - `TextInput`
  - `Textarea`
  - `CheckboxRow`
  - `StatCard`
  - `InfoCard`
  - `KeyValuePanel`
  - `DataTable`
  - `MobileRecordCard`
  - `PaginationBar`
- depends on `UF1`
- primary implementation target: prompt-28
- safety note:
  - ship as reusable primitives before touching dense feature screens

Together, `UF1` through `UF4` are the shared-foundation-first sequence. Prompt-28 should focus primarily on these four slices and stop before broad feature-page restyling.

## Feature-Page Sequence

### `AU1` Auth And Account-State UI Adoption

- scope:
  - adopt `AuthFormShell` and `AuthNoticeShell`
  - normalize login, register, forgot/reset password, verify email, confirm password, and profile settings
  - align account-state and notice pages with shared feedback patterns
- depends on:
  - `UF1` -> `UF4`
  - implementation prompts 31 and 32 for account-state and verification foundations
  - prompt-38 where Google sign-in UI appears
- safest rollout:
  - only touch auth pages when the related auth/account-state prompt is already making those pages relevant

### `GD1` Public Donor-Entry And Guest-Donation UI Activation

- scope:
  - public donor entry shell
  - guest checkout UI
  - narrow outcome / status lookup surfaces
- depends on:
  - `UF1` -> `UF4`
  - prompt-33 and prompt-34 donor-domain work
- reason for ordering:
  - this is the earliest additive public feature family that can benefit from the shared baseline without reopening protected guardian or donor portal surfaces

### `DO1` Donor Portal Normalization

- scope:
  - donor dashboard
  - donation history
  - receipt history
  - donor no-portal state screens when applicable
- depends on:
  - `UF1` -> `UF4`
  - prompt-35 donor auth and portal access
- safety rule:
  - keep donor portal read-only semantics intact while normalizing shell, cards, lists, and feedback states

### `GI1` Guardian Informational Shell And Content Activation

- scope:
  - informational guardian home
  - institution information
  - admission information content blocks
  - linkage / eligibility status
- depends on:
  - `UF1` -> `UF4`
  - prompt-36 guardian informational portal
- safety rule:
  - keep this additive and distinct from the live protected `/guardian` space

### `GP1` Guardian Protected Normalization

- scope:
  - protected guardian dashboard
  - student detail
  - invoice list/detail
  - payment history
  - payment outcome/status screens
- depends on:
  - `UF1` -> `UF4`
  - prompt-37 guardian protected gating
- safety rule:
  - preserve invoice/payment ownership cues and existing protected flow continuity

### `MR1` Multi-Role Chooser And Switcher UI

- scope:
  - neutral eligible-context chooser
  - no-eligible-context fallback
  - in-portal context switcher
- depends on:
  - `UF1` -> `UF4`
  - prompt-39 multi-role home and switching
- safety rule:
  - additive chooser/switch controls only
  - no donor/guardian data mixing

### `AD1` Public / Guardian Admission Handoff Activation

- scope:
  - external handoff card on approved public admission surfaces
  - external handoff card on approved guardian-informational admission surfaces
  - welcome-page/public admission surface update to use the canonical config path
- depends on:
  - `UF1` -> `UF4`
  - prompt-41 external admission URL implementation
- safety rule:
  - keep admission handoff external-only
  - do not introduce auth or protected-guardian admission entry

### `FC1` Final Cross-Product Consistency Sweep

- scope:
  - management compatibility polish where safe
  - leftover auth/public/portal inconsistencies
  - component usage cleanup
  - spacing/heading/button/table/form consistency verification
- depends on:
  - all prior UI slices
  - prompt-42 final UI consistency pass
- safety rule:
  - this is cleanup, not a place to invent new screens or flows

## Independently Shippable UI Slices

### Safely Independent

- `UF1`
  - can ship as additive tokens without page conversion
- `UF2`
  - can ship if shared shells are introduced opt-in rather than by global forced swap
- `UF3`
  - can ship as reusable feedback/state components before feature pages adopt them everywhere
- `UF4`
  - can ship as primitive controls/data-display components before page migration
- `GD1`
  - can ship independently because guest/public donor entry is an additive public feature family
- `DO1`
  - can ship independently once prompt-35 donor portal behavior is ready
- `GI1`
  - can ship independently because guardian informational is additive and distinct from protected guardian routes
- `GP1`
  - can ship independently after prompt-37 because it normalizes already-approved protected guardian pages
- `MR1`
  - can ship independently as an additive chooser/switch layer on top of direct `/donor` and `/guardian` access
- `AD1`
  - can ship independently once prompt-41 wires the canonical external admission configuration

### Not Ideal As Standalone Releases

- `AU1`
  - should land alongside the related auth/account-state implementation prompts rather than as a purely visual-only phase
- `FC1`
  - is intentionally a closeout cleanup slice, not a standalone milestone

## Rollback Checkpoints

1. After `UF1`
   - tokens exist, but screens may still use legacy classes
   - rollback is low risk because no page family must depend on them yet
2. After `UF2`
   - shared shells/header patterns exist, but live legacy layouts are still available
   - donor and guardian shell convergence remains reversible
3. After `UF3` and `UF4`
   - reusable state, form, and data primitives exist
   - feature pages have not all been migrated yet
4. After `AU1`
   - auth/account-state surfaces match the product family while behavior stays governed by auth prompts, not UI work
5. After `GD1`
   - additive public donor entry and guest checkout UI are live without changing donor portal or guardian boundaries
6. After `DO1`
   - donor read-only portal is visually normalized with no change to donor eligibility semantics
7. After `GI1`
   - guardian informational portal is live and still clearly separate from protected `/guardian`
8. After `GP1`
   - protected guardian read/payment screens are normalized while direct protected flows remain intact
9. After `MR1`
   - chooser and switcher are additive; direct `/donor` and `/guardian` access remain fallback routes
10. After `AD1`
    - external admission handoff is centralized across approved placements with config/fallback rules intact
11. After `FC1`
    - remaining inconsistencies are cleaned up, and prompt-42 can verify there is no orphan legacy UI dialect left among affected surfaces

## Prompt Mapping Summary

- prompt-28 should primarily deliver `UF1` -> `UF4`
- prompt-31 / prompt-32 / prompt-38 should absorb `AU1` where auth/account-state surfaces are touched
- prompt-33 and prompt-34 should absorb `GD1`
- prompt-35 should absorb `DO1`
- prompt-36 should absorb `GI1`
- prompt-37 should absorb `GP1`
- prompt-39 should absorb `MR1`
- prompt-41 should absorb `AD1`
- prompt-42 should absorb `FC1`

This keeps prompt-22's slice plan aligned with the approved implementation order instead of creating a competing rollout.

## Contradiction / Blocker Pass

- No contradiction with prompt-21 was found.
- The prompt-21 screen inventory, shared component inventory, and layout reuse map were reused directly.
- This remained strictly implementation-slice planning and did not drift into code or design implementation.
- Prompt-20's institutional product family remains the baseline.
- Prompt-19's external-admission guardrails remain preserved:
  - no internal admission workflow slice was created
  - admission handoff activation remains limited to approved public and guardian-informational surfaces
- No correction pass is required.
- No hard blocker prevents prompt-23.
