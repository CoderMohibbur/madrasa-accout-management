# Risks

- Real SMS delivery is still deferred; the prompt-32 phone verification flow uses a documented development-only placeholder in local and `testing` environments.
- Legacy verified routes still depend on email verification until later prompts explicitly replace that boundary.
- Email delivery can now fail soft in the current environment, but production rollout still depends on valid mail transport configuration.
- Verified-phone ownership now fails closed against existing active owners, so any future household-shared-phone exception policy would need explicit product approval.
