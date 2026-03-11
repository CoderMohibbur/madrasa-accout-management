# Risks

- The live repo still gates donor portal routes with `verified`, which conflicts with the frozen rule that donor login and donor donation cannot depend on universal verification.
- The live repo still has no guest donation flow, no donor payment initiation route, and no donor payable model, so later prompts must avoid accidentally using legacy `transactions` as a shortcut.
- Guest donations with no contact data are allowed by target rules, but support recovery becomes fragile if the user loses the opaque payment or receipt reference.
- If anonymous-display behavior is not modeled carefully, teams may accidentally mix public-display hiding with internal donor identity or permission decisions.
- Transaction-specific receipt/status access for guests and non-portal donors will need a secure opaque-reference design so those pages do not become an account-history leak.
- Donor portal history currently depends on legacy `transactions` semantics, so later prompts must distinguish historical read access from the future safe write/finalization model.
