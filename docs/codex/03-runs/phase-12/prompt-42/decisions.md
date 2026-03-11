# Decisions

- Prompt-42 remained presentation-only; no route, controller, model, service, policy, middleware, or migration changes were allowed or made.
- Shared auth/profile/public/donor/guardian surfaces now converge on the shared `ui-*` card, stat-card, alert, button, checkbox, form, and table primitives from prompt-28.
- Table-heavy donor and guardian pages now use `x-ui.table` plus the built-in mobile slot so responsive fallback is not limited to horizontal scroll.
- Prompt-41's external admission behavior remains unchanged: public and guardian informational admission pages still render through the shared `x-admission.external-handoff` path backed by `ExternalAdmissionUrlResolver`.
- Remaining visual debt after prompt-42 is limited to management/reporting and older legacy screens outside the approved affected-surface scope; prompt-43 is the next release gate.
