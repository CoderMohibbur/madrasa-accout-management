# Risks

- Prompt-39 intentionally stops short of prompt-40's broader route/middleware/policy cleanup, so `/dashboard` is chooser-aware but the rest of the final route alignment remains deferred.
- The chooser and switcher do not yet persist a remembered preferred context; multi-role switching remains explicit links only.
- Verified shared-account users without donor or guardian context still use the existing onboarding fallback rather than a broader neutral no-context dashboard.
