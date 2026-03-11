# Risks

- Donor-only redirect behavior still intentionally avoids early multi-role chooser logic; donor-plus-guardian combined home behavior remains deferred to prompt-39.
- Donor receipt scoping still depends on the current `DonationIntent` payable typing and legacy null-payable donor receipt pattern, which may need tighter provenance rules later.
- Prompt-35 keeps donor history read-only; donor manual-bank, claim/link, and legacy donor-history conversion remain deferred.
- Runtime Git validation remains limited because `git` is unavailable in the current shell environment.
