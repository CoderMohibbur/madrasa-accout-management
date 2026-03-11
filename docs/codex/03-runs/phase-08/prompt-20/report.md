# Report

This prompt sets the global UI/UX baseline without implementing code. Current-state review shows the repository has four different UI dialects: a generic public welcome page, stock Breeze auth pages, a newer dark-slate donor/guardian portal family, and a management dashboard that partially modernizes itself with page-local tokens. Prompt-20 therefore needs to define one product-wide baseline that future prompts can apply consistently while preserving prompt-19's admission handoff boundary and avoiding accidental CTA duplication on auth or protected guardian surfaces.

## UI Audit Summary

### Fragmentation

- Public, auth, guardian, donor, and management surfaces do not currently feel like one product.
- `resources/views/welcome.blade.php` uses a separate marketing-style layout, blue accents, Merriweather, hard-coded external links, and generic placeholder metadata.
- `resources/views/layouts/guest.blade.php` plus the auth pages still use stock Breeze card styling, gray surfaces, indigo focus states, and a much more basic component vocabulary.
- `resources/views/components/guardian-layout.blade.php` and `resources/views/components/donor-layout.blade.php` already form a clearer portal family, but they duplicate structure and differ mainly by accent color rather than product behavior.
- `resources/views/dashboard/index.blade.php` has stronger hierarchy and spacing than older legacy pages, but its tokens are defined inline inside the page, so the design direction is not reusable yet.

### Component Inconsistency

- Radii vary between `rounded-md`, `rounded-lg`, `rounded-xl`, `rounded-full`, and `rounded-3xl` with no shared rule.
- Accent colors vary across blue, indigo, emerald, orange, and sky depending on page instead of semantic role.
- Auth components (`primary-button`, `text-input`, `input-label`, `nav-link`) still encode an older indigo/gray style that does not match the portal layouts.
- Portal tables, cards, and empty states are visually stronger than auth and public surfaces, but they are not backed by shared components or global tokens.

### UX Quality

- Donor and guardian portals already have readable structure: title, description, summary cards, profile panel, and scoped history tables.
- Empty states exist in the portal surfaces, but they are text-only and do not yet follow one shared action/help pattern.
- Tables currently rely on horizontal overflow as the main mobile strategy; there is no baseline card fallback for narrower screens.
- No clear shared loading state or no-access state pattern is visible across the sampled surfaces.
- The welcome page is visually separated from the rest of the app and does not establish the same trust, information architecture, or action hierarchy as the portals.

### Accessibility / Responsiveness Gaps

- Focus-ring colors and visibility are inconsistent between public, auth, and portal views.
- Typography systems are split between Merriweather and Figtree, with no shared heading hierarchy.
- All-caps micro-labels are used well in some portals, but the system is not formally bounded.
- Portal pages are reasonably responsive in layout, but management and auth surfaces still depend heavily on default framework behaviors instead of deliberate mobile-first rules.

### Prompt-19 Carry-Forward Note

- The only currently live hard-coded admission URL remains the welcome page link.
- There is still no admission CTA on auth or guardian views.
- The UI baseline must preserve that absence until future approved public or guardian-informational surfaces intentionally add the handoff through the canonical prompt-19 config approach.

## Selected Design Direction

The selected baseline is a light-first, institutional product family: calm, trustworthy, editorial in public presentation, and operationally clear inside logged-in surfaces.

### Direction Summary

- Base mood: warm light surfaces with slate text, subtle depth, and controlled accent use.
- Primary accent: emerald for action, focus, and positive states.
- Secondary accent: muted gold/amber for institutional warmth and section emphasis.
- Support accents: sky for informational states and rose for destructive/error states.
- Role identity: donor, guardian, and later multi-role contexts may use small accent markers or badges, but not wholly separate visual systems.
- Typography: one UI sans family across product surfaces, with one optional editorial serif reserved only for select public hero or institutional display moments.
- Dark mode may remain supported later, but it is not the design baseline. New work should not default to dark-first screens.

### Why This Fits The Project

- It matches the institutional trust needs of a madrasa/public information experience.
- It supports data-heavy portal and management surfaces without feeling cold or generic.
- It allows donor, guardian, auth, public, and multi-role views to feel related without erasing domain differences.
- It preserves the strongest current direction already visible in the portal information architecture while removing color and layout drift.

## Unified UI Rules

### Page Layout

- Use one content-width system:
  - public pages: up to `1280px`
  - app/portal pages: up to `1200px`
  - auth pages: centered narrow column around `440px` to `520px`
- Every page uses the same structural rhythm:
  - shell
  - page header
  - primary action zone
  - content sections
  - feedback states
- Public pages may use hero sections, but logged-in pages should use compact headers and content-first layouts.
- Guardian, donor, management, and future multi-role pages should share one app-shell structure with tokenized variants, not separate bespoke shells.

### Spacing

- Adopt an 8-point spacing system with common steps: `4`, `8`, `12`, `16`, `24`, `32`, `48`, `64`.
- Page padding:
  - mobile: `16px`
  - tablet: `24px`
  - desktop: `32px`
- Standard section gap: `24px`
- Standard card padding: `20px` on mobile, `24px` on desktop
- Standard header-to-body gap inside sections: `16px`

### Typography

- Use one primary UI sans family across public, auth, and app surfaces.
- If a serif is used, reserve it for short public-facing display headlines only.
- Default body size: `16/24`
- Dense data body size: `14/20`
- Avoid long all-caps headings; reserve uppercase only for small eyebrow labels and compact metadata chips.
- Keep paragraph width around `60ch` to `75ch` for readability.

### Headings

- Page eyebrow: `12/16`, uppercase, light tracking, muted accent
- Page title: `32/40` desktop, `28/36` tablet, `24/32` mobile
- Section title: `20/28`
- Card title: `16/24`
- Supporting description copy should sit directly under the relevant title and explain the page or section in one sentence.

### Button Hierarchy

- Primary: solid emerald, white text, highest emphasis
- Secondary: neutral outlined button with slate text
- Tertiary: text-only action link
- Destructive: rose-tinted filled or outlined destructive button
- External-link buttons must visibly communicate off-site behavior when used later for the admission handoff
- Minimum tap target: `44px`

### Cards

- Use large-radius surface cards as the default content container:
  - feature/stat card radius: `24px`
  - standard panel radius: `20px`
  - compact inner surface radius: `14px`
- Default cards should use solid or near-solid surfaces with subtle shadow and border, not heavy transparency as the standard.
- Decorative gradients should be restrained to hero or emphasis zones, not every card.

### Lists

- Use stacked list rows with clear title, supporting metadata, and optional action zone.
- Use dividers lightly; avoid dense border noise.
- Status chips and small metadata pills should be consistent in size, corner radius, and color semantics.

### Tables

- Tables must sit inside a panel with a clear header and empty state.
- Use consistent column padding, left-aligned text by default, and right-aligned numeric values.
- Table headers should be short, sentence-case or restrained uppercase, and highly legible.
- On small screens, critical tables should have a defined card/list fallback; horizontal scroll is a fallback, not the first-choice experience for every surface.
- Pagination, filters, and summaries should follow one placement pattern across donor, guardian, and management pages.

### Forms And Inputs

- All forms should use one shared field stack:
  - label
  - field
  - helper text if needed
  - validation text
- Inputs, selects, and textareas should share one height, border, radius, and focus-ring system.
- Use top-aligned labels.
- Placeholder text may support, but never replace, labels.
- Checkbox and radio controls must visually align with the same focus and validation system.

### Validation And Error States

- Invalid fields must show:
  - visible error border
  - inline error message
  - accessible association between input and message
- Errors must use icon plus text, not color alone.
- Page-level errors should appear in a standard alert banner near the top of the form or page.

### Success States

- Success feedback should use one banner pattern with icon, concise message, and optional next action.
- Avoid relying only on brief session flashes with no visual hierarchy.
- Success colors remain emerald/green and should not be reused for generic emphasis.

### Empty States

- Empty states should include:
  - a title
  - one sentence of explanation
  - an optional safe next action when applicable
- Empty states should not invent sensitive example data.
- Portal empty states may stay calm and text-first, but should become more structured than one bare sentence.

### Loading States

- Use skeleton placeholders for stat cards, panel headers, list rows, and table rows.
- Loading placeholders should preserve layout height to prevent jumpy reflow.
- Spinners may be used only for compact inline actions, not as the only full-page loading pattern.

### No-Access States

- No-access surfaces should use a calm explanation panel instead of raw error-only language.
- Each no-access state should explain what is unavailable, why at a high level, and what safe next action exists.
- No-access messaging must not leak protected student, invoice, donor, or payment details.

### Mobile Responsiveness

- Default to one-column page flow on small screens.
- Summary cards should collapse from 4-up to 2-up to 1-up cleanly.
- Action rows should wrap without clipping or horizontal squeeze.
- Tables with more than four meaningful columns should define either priority columns or stacked-card rendering on mobile.
- Sticky navigation should never hide essential content or create double-scroll traps on smaller devices.

### Accessibility Expectations

- Text contrast should meet at least WCAG AA.
- Focus rings must be visible, consistent, and shared across buttons, links, fields, and menu triggers.
- Heading levels should be semantic and sequential.
- Status meaning must not rely on color alone.
- External destinations, including the later admission CTA, should be labeled as external/off-site.
- Decorative icons should be hidden from assistive tech; informative icons must have accessible labels.

## Preserve / Normalize / Replace / Avoid List

### Preserve

- The donor and guardian portal information architecture:
  - clear page title and description
  - summary metrics
  - scoped history/detail panels
  - calm empty-state tone
- The management dashboard's move toward stronger hierarchy, sectioning, and quick actions
- The existing use of readable descriptive copy at the top of portal screens

### Normalize

- Donor and guardian shell structure into one shared app-shell baseline with small token differences only
- Summary cards, section headers, badges, tables, pagination blocks, and alert banners
- Profile panels and record-detail layouts
- Auth forms and profile settings so they use the same product-level field and button system as the portals

### Replace

- The current public welcome page styling, generic metadata, and disconnected visual language
- Stock Breeze auth styling and indigo-centric component palette
- Page-local token definitions embedded directly in individual views such as `dashboard/index.blade.php`
- Role-by-role full-page recoloring as the primary way to differentiate donor and guardian contexts

### Avoid

- Treating the existing inconsistent UI as the target standard
- Hard-coded per-view color decisions and typography pairings
- Dark-first portal styling as the default product baseline
- Copy-pasted layout duplication between donor and guardian shells
- Adding admission CTA placement to auth views or the current protected guardian views before the approved public and guardian-informational surfaces exist
- Using stale duplicate view variants or copy files as design sources of truth

## Minimum Shared Design Foundation

Before feature-page UI implementation work, the project needs one minimal design-language foundation:

### Token Layer

- semantic color tokens
- spacing scale
- radius scale
- shadow scale
- typography scale
- content widths
- shared focus-ring token

### Shell Layer

- public shell
- auth shell
- app shell
- compact page-header pattern
- section-header pattern

### Core Components

- button family
- link treatment
- form fields
- validation banner
- success banner
- status badges
- summary/stat card
- content card/panel
- table wrapper
- empty state
- loading skeleton
- no-access panel

### Content And Behavior Rules

- one heading hierarchy
- one metadata-chip system
- one mobile table fallback strategy
- one feedback-state vocabulary
- one external-link disclosure pattern
- keep the admission handoff external and reserve its future CTA component for only the prompt-19 approved placements

This foundation should guide prompt-21 screen mapping, prompt-22 component planning, prompt-28 shared UI implementation, and prompt-42 final consistency cleanup.

## Contradiction / Blocker Pass

- No contradiction with prompt-19 was found.
- The admission handoff remains external-only and was not expanded into an internal application workflow.
- Prompt-19's placement, fallback, and config approach remains preserved.
- No new admission entry was approved for auth or current protected guardian views.
- No correction pass is required.
- No hard blocker prevents prompt-21.
