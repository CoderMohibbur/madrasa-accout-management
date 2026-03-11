# Risks

- If prompts 21, 22, or 28 reuse the current donor and guardian dark-only styling as the baseline instead of the selected shared light-first direction, the product family will stay split.
- If the shared design language is implemented only as per-page class strings, the current token drift in `dashboard/index.blade.php` will spread to new surfaces.
- If mobile table behavior is left as generic horizontal scrolling everywhere, guardian, donor, and future multi-role record views will remain harder to use on phones.
- If auth forms keep the Breeze indigo system while portals move to the new baseline, onboarding and logged-in experiences will continue to feel disconnected.
- If prompt-19's admission CTA boundaries are ignored during later public or guardian UI work, the new baseline could accidentally normalize duplicated or misplaced admission-entry links.
