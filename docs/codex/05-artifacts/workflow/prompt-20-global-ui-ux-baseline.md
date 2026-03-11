# Prompt 20 Global UI/UX Baseline

## Selected Direction

- light-first institutional product family
- warm neutral surfaces
- slate text hierarchy
- emerald primary action/focus color
- gold/amber secondary warmth
- sky informational accent
- rose destructive/error accent
- optional serif only for limited public-facing display moments

## Product-Family Rule

- public, auth, donor, guardian, management, and future multi-role surfaces must share one visual language
- role identity may appear in badges, tabs, or micro-accents, not in fully separate page systems

## Preserve

- portal page structure:
  - title
  - description
  - summary cards
  - scoped detail panels
  - calm empty-state tone
- management dashboard movement toward better hierarchy and action grouping

## Normalize

- donor and guardian shells
- page headers
- metric cards
- status badges
- data tables
- pagination
- alerts
- profile/detail panels
- auth and profile forms

## Replace

- public welcome-page visual language
- stock Breeze auth look
- page-local inline token definitions
- full-page role recoloring as the main differentiation strategy

## Avoid

- per-view hard-coded palettes and type systems
- dark-first as the default baseline
- shell duplication between donor and guardian layouts
- using auth or current protected guardian surfaces as admission CTA destinations
- stale duplicate view files as baseline references

## Shared Rules Snapshot

- spacing: 8-point scale
- page widths:
  - public `1280px`
  - app `1200px`
  - auth `440px` to `520px`
- page rhythm:
  - shell
  - page header
  - actions
  - content sections
  - feedback states
- buttons:
  - primary
  - secondary
  - tertiary
  - destructive
- forms:
  - top label
  - field
  - help text
  - validation text
- tables:
  - panel wrapper
  - readable headers
  - right-aligned numeric values
  - mobile fallback beyond simple horizontal scroll
- states:
  - success banner
  - error banner
  - structured empty state
  - skeleton loading state
  - calm no-access state

## Admission Guardrail Carry-Forward

- prompt-19 remains in force
- external admission handoff stays external-only
- future admission CTA styling must use the canonical config approach later
- no admission CTA should be introduced on auth views or the current protected guardian portal as part of the baseline

## Minimum Foundation Before Feature UI Work

- token layer
- public/auth/app shells
- page-header pattern
- button family
- field family
- alert family
- card family
- table wrapper
- badge system
- empty/loading/no-access states
