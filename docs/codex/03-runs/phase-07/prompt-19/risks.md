# Risks

- Leaving the admission destination hard-coded in `resources/views/welcome.blade.php` while adding future guardian-informational placements would recreate the duplication problem prompt-19 is meant to prevent.
- Putting the admission CTA into the current protected `/guardian` portal would blur the approved informational-versus-protected guardian boundary and invite later policy confusion.
- Using raw `env()` calls directly in Blade or controllers would bypass a single validation/fallback path and increase config-cache inconsistency risk.
- Falling back to a guessed URL or to protected internal routes when the config is missing would create broken or misleading navigation at exactly the public handoff boundary that should stay explicit.
- Adding admission CTAs to generic auth forms would imply that login/registration owns admission onboarding, which conflicts with the approved public-plus-guardian-informational placement model.

Record risks, warnings, and technical debt here.
