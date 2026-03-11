# Prompt 30 Account-State Read-Path Adaptation

## Shared Read Rules

- `approval_status`
  - use the explicit prompt-29 column when present
  - otherwise fall back to legacy approval compatibility from `email_verified_at`
- `account_status`
  - use the explicit prompt-29 column when present
  - otherwise fall back to `active`
- `deleted_at`
  - when set, it overrides otherwise-approved or active access

## Shared Access Predicate

- prompt-30 introduces one shared derived account-state gate:
  - approved account state
  - active lifecycle
  - not soft-deleted at the user-account level

## Read Paths Updated

- login approval reads shared account-state rather than raw `email_verified_at`
- role-gated routes and management-surface checks fail closed on pending, inactive, suspended, or deleted account state
- donor and guardian portal service entry checks require shared account-state before domain-profile eligibility
- `/dashboard` redirects to donor/guardian only when the matching portal profile is actually eligible

## Deliberate Deferrals

- `verified` middleware remains in place for now
- donor no-portal behavior and guardian informational/no-portal behavior remain later prompt work
- guardian linkage-state adoption remains later guardian rollout work
- donor settlement tables and payment-domain redesign remain later donor rollout work
