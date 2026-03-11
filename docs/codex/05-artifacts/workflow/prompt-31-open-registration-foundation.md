# Prompt 31 Open Registration Foundation

## Entry Points

- `GET /register`
- `GET /register/donor`
- `GET /register/guardian`
- `POST /register`
- `GET /registration/onboarding`

## Registration Contract

- One shared request validates:
  - `name`
  - `email`
  - `password`
  - `password_confirmation`
  - optional `intent` in `public`, `donor`, or `guardian`
- The backend normalizes unknown intent values back to `public`.

## Base Account Outcome

- Create one shared `users` row
- set `approval_status = approval_not_required`
- set `account_status = active`
- keep `email_verified_at = null`
- assign the compatibility role `registered_user`

## Intent-Specific Foundation Outcomes

### Public

- create no donor profile
- create no guardian profile
- send the user to neutral onboarding

### Donor

- create one linked donor draft profile
- keep `portal_enabled = false`
- keep donor activation off
- do not assign the `donor` role
- send the user to neutral onboarding

### Guardian

- create one linked guardian draft profile
- keep `portal_enabled = false`
- do not link students or protected records
- do not assign the `guardian` role
- send the user to neutral onboarding

## Post-Registration Boundary

- `registration.onboarding` is the safe authenticated landing for `registered_user` accounts
- legacy management surfaces deny `registered_user`
- legacy verified routes still keep their existing verification boundary
- donor portal access, guardian linkage, guest donation flow, and payment-domain changes remain later prompts
