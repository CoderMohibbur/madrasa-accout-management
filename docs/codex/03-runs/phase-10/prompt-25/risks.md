# Risks

- If the matrix is written only for the final target state and not for the transition period, prompts 29 through 32 can break legacy login or registration before the new eligibility model is ready.
- If migration-safety cases are not automated, ambiguous donor, guardian, phone, or linkage rows may be auto-linked and later policy tests will pass on invalid data.
- If donor tests conflate receipt access with donor portal eligibility, releases can leak donor history or wrongly treat payment completion as portal access.
- If guardian tests omit pivot-versus-invoice mismatch cases, protected guardian access can broaden incorrectly in multi-guardian families.
- If Google and multi-role tests omit fail-closed conflict cases, alternate auth can become an authorization bypass instead of a safe onboarding path.
- If UI smoke skips shared navigation and route-name preservation, additive portal work can regress legacy entry points before prompt-43 catches it.
- Existing auth and profile baseline failures already documented in `docs/codex-autopilot/state/validation_manifest.json` mean future implementation validation must keep baseline-vs-regression classification explicit.
