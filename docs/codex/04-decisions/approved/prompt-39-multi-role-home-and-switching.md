# Prompt 39 Multi-Role Home And Switching

Approved implementation decisions from prompt-39:

- Multi-role rollout is introduced through one shared eligible-context resolver built on the existing donor and guardian access services.
- `/dashboard` remains the shared route name and now chooses between:
  - direct redirect for one eligible context
  - neutral chooser for multiple eligible donor/guardian contexts
  - existing onboarding fallback for verified `registered_user` accounts with zero eligible contexts
- Explicit donor/guardian switching is additive only and links only to already-eligible donor or guardian homes plus shared home.
- Google callback, password login, and email-verification completion now reuse the same chooser-aware post-auth routing rules.
- Management-compatible dashboard behavior remains outside the chooser path.
- Final route/middleware/policy cleanup remains deferred to prompt-40.
