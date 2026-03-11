# Prompt 10 Donor Payable Model

## Core Entity Rules

- `donation_intent` is the pre-settlement donor checkout record
- `donation_intent` is the minimal safe donor payable target for `payments.payable`
- `payments` stores individual gateway or manual attempts
- `donation_record` is created only after authoritative settlement
- `receipts` remain payment-specific and post-settlement

## Donor Classification Rules

- `guest donation`: no required account, no automatic account linking
- `identified donation`: linked to a known `user_id`, with optional donor profile linkage
- `anonymous-display donation`: a visibility flag on guest or identified donation, not a third ownership class

## Settlement Rules

- Browser success pages are informational only
- Server-side verification or manual approval is required before creating a `donation_record`
- Failed, cancelled, expired, or ambiguous attempts do not create receipts or completed donation rows
- Retry creates a new `payments` attempt under the same still-open `donation_intent`

## Receipt And Access Rules

- Receipt access is transaction-specific and narrower than donor portal history access
- Guest receipt access must use an opaque public reference or equivalent secret
- Identified donors may access their own transaction-specific receipt without requiring donor portal eligibility
- Receipt-history browsing remains a separate donor portal boundary

## Posting Separation Rules

- Donor payment settlement must not depend on legacy `transactions` posting
- Legacy `transactions` is not the donor live-payment source of truth
- Accounting posting may run later from the settled donor record in a separate retryable step

## Minimal Safe Rollout

- one-time guest donation
- one-time identified donor donation
- anonymous-display option
- one verified online provider flow
- narrow status / receipt access
- no recurring billing
- no saved payment methods
- no automatic portal eligibility from payment completion
