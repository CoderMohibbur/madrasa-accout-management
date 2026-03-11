# Prompt 28 Shared UI Foundation Implementation

## Shared Shells Landed

- `resources/views/layouts/app.blade.php`
  - shared app shell wrapper around the preserved navigation partial
- `resources/views/layouts/guest.blade.php`
  - shared auth shell with institutional presentation and no admission CTA
- `resources/views/components/public-shell.blade.php`
  - shared public shell for public/home/informational surfaces
- `resources/views/components/portal-shell.blade.php`
  - shared protected portal shell for donor and guardian wrappers

## Shared Primitives Landed

- alerts
  - `resources/views/components/ui/alert.blade.php`
- badges and pills
  - `resources/views/components/ui/badge.blade.php`
- cards and stat cards
  - `resources/views/components/ui/card.blade.php`
  - `resources/views/components/ui/stat-card.blade.php`
- page header
  - `resources/views/components/ui/page-header.blade.php`
- tables
  - `resources/views/components/ui/table.blade.php`
- states
  - `resources/views/components/ui/empty-state.blade.php`
  - `resources/views/components/ui/loading-skeleton.blade.php`
  - `resources/views/components/ui/no-access-panel.blade.php`
- shared Blade primitives updated in place
  - primary/secondary/danger buttons
  - text input / label / error
  - nav link / responsive nav link
  - status select
  - auth session status
  - toast success/error

## Shared CSS Contract

- `resources/css/app.css` now defines the shared token and component layer for:
  - shell backdrops and containers
  - page header rhythm
  - cards, list rows, tables
  - badge and pill styles
  - button family
  - field family
  - alert family
  - empty/loading/no-access families
  - public/auth surface styling

## Adoption Guardrails For Later Prompts

- reuse these shared shells and primitives before creating page-local visual tokens
- keep the light-first institutional product family as the default
- keep donor and guardian differences subtle and contextual, not separate design systems
- do not introduce admission CTAs onto auth or protected guardian surfaces
- later prompts may normalize feature pages onto this foundation, but they should not replace route names or change protected business behavior while doing so
