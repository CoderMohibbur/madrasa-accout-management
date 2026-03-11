# Risks

- The repository working tree remains broadly dirty outside the prompt-43 test/doc touch, which is an operational isolation risk even though the validated release-candidate behavior passed the blocker pack.
- The full suite is still not completely green; 10 auth-suite baseline failures remain and must continue to be treated as known baseline rather than new regression noise.
- Older management/reporting and legacy screens outside the prompt-42 affected-surface list still carry pre-foundation visual patterns and remain post-release cleanup debt rather than prompt-43 blockers.
