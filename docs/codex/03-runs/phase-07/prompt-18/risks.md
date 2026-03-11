# Risks

- Blanket `verified` currently doubles as an approval gate, so any later route cleanup that removes it without a replacement account-state middleware could create access regressions.
- The live `/guardian` prefix is already in use for protected invoice and history links; repurposing it too early for informational access would risk broken links or protected-data ambiguity.
- `dashboard` is a compatibility-heavy route name with multiple auth redirect dependencies, so shared-home changes must preserve the name even if the behavior behind it changes.
- Payment status/detail authorization is currently split across routes, controllers, and services; if that stays fragmented, later donor or guest payment-status paths will be harder to secure consistently.
- The current guardian-only payment views and links are safe for the live scope but would leak the wrong context if reused for future donor or guest payment-status surfaces.
- Hard-coded external admission usage on the welcome page shows that public-info routing and config consistency are not yet centralized, which creates duplication risk for the next phases.
