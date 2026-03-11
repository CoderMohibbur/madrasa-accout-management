# Prompt 43 Tests And Rollout Readiness

## Release-Gate Checklist

- run the cumulative blocker-pack regression suite across:
  - `tests/Feature/Phase1/FoundationSchemaTest.php`
  - `tests/Feature/Phase1/PortalRoleAccessTest.php`
  - `tests/Feature/Phase2/GuardianPortalTest.php`
  - `tests/Feature/Phase3/DonorPortalTest.php`
  - `tests/Feature/Phase4/ManagementReportingTest.php`
  - `tests/Feature/Phase5/PaymentIntegrationTest.php`
  - `tests/Feature/Phase12/*` release-slice tests
  - `tests/Unit/Policies/StudentFeeInvoicePolicyTest.php`
  - `tests/Feature/Auth/EmailVerificationTest.php`
- rerun the full suite and classify failures as baseline-only or new regressions
- sweep for prompt-41 admission-handoff violations
- sweep for prompt-42 dark-dialect regressions on the touched auth/profile/donor/guardian surfaces

## Pass Criteria

- blocker pack `RB-01` through `RB-10` passes
- `UI-01` through `UI-03` smoke remains green
- prompt-41's shared admission handoff stays the only live external-admission path
- prompt-42's shared `ui-*` patterns remain intact on the approved affected surfaces
- the full-suite failure set stays confined to the known auth-suite baseline area

## Same-Phase Correction Rule

- if the release gate exposes a stale test expectation that contradicts later approved prompts, correct the test only
- rerun the failing test first
- rerun the full release-gate pack after the test correction
- do not reopen application behavior unless a real product contradiction is proven

## Current Prompt-43 Runtime Note

- release-gate pack result: `71 passed (643 assertions)`
- full suite result: `10 failed, 86 passed (689 assertions)`
- remaining failures stay confined to the auth-suite baseline area
- `Tests\Feature\ProfileTest` now passes, so the observed baseline has improved versus the earlier 14-failure record
