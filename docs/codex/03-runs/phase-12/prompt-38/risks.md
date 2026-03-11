# Risks

- Real Google OAuth client credentials and registered callback URLs still need to replace the prompt-08 placeholder values before live testing.
- Multi-role chooser and explicit context switching remain deferred to prompt-39, so Google sign-in currently lands through the existing compatibility redirect order.
- Prompt-38 intentionally leaves Google unlink, provider-subject reassignment, and broader recovery tooling disabled to avoid unsafe merge behavior.
