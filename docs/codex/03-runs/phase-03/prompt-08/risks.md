# Risks

- The live repo still has no Google auth dependency, no config surface, and no external identity persistence, so later implementation will require new package, schema, controller, and route work.
- The live repo still overloads `email_verified_at`, so provider-verified email could accidentally become an approval bypass if later prompts do not separate those states first.
- Incorrect auto-linking by email is the highest account-takeover risk; provider subject conflicts must fail closed.
- Guardian Google onboarding is riskier than donor/public onboarding because the live repo still lacks the planned guardian informational portal and still routes guardian role holders toward protected surfaces.
- The current guardian-first `/dashboard` redirect remains incompatible with a safe dual-role Google post-login experience until later multi-role work lands.
- Real provider credentials are unavailable in the repo. Dummy placeholders are sufficient for analysis, but real end-to-end testing will remain blocked until production-replace items are filled.
