# Decisions

- Multi-role home behavior is now derived from one shared resolver built on the existing donor and guardian access services, not from raw role ordering.
- The `dashboard` route name remains unchanged, but `/dashboard` now behaves as a neutral chooser for users independently eligible for more than one donor/guardian context.
- Single-context users still redirect straight to their only eligible donor or guardian home.
- Zero-context `registered_user` accounts keep the existing `registration.onboarding` fallback instead of inventing a new portal landing.
- In-portal switching is additive only: links expose only already-eligible donor or guardian homes plus shared home, and switching does not grant access by itself.
- Password login, Google callback, and email-verification flows now reuse the same chooser-aware post-auth routing logic.
- Management-compatible dashboard behavior remains outside the chooser path, and prompt-40 still owns the final route/middleware cleanup.
