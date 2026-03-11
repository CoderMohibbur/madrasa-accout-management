# Decisions

- Prompt-31 kept one shared registration backend and added only donor- and guardian-branded entry pages that preselect intent.
- Open registration now creates a base account with `approval_status = approval_not_required`, `account_status = active`, and `email_verified_at = null`.
- Self-registered accounts receive the compatibility role `registered_user`; prompt-31 still does not auto-assign donor or guardian route-driving roles during registration.
- Donor intent creates a linked donor foundation row with `portal_enabled = false`, `isActived = false`, and no donor role.
- Guardian intent creates a linked guardian foundation row with `portal_enabled = false`, `isActived = true`, and no guardian role or protected linkage.
- The neutral post-registration landing is `registration.onboarding`, accessible to `registered_user` accounts without widening legacy verified-route behavior.
- Legacy management surfaces now fail closed for `registered_user` accounts.
- The prompt-28 shared UI foundation remains authoritative for prompt-31 auth views, including the anonymous Blade component convention `<x-ui.card>` and `<x-ui.alert>`.
- Validation used the local PHP 8.2 runtime because the local PHP 8.4 runtime still lacked `mbstring`; the broader suite still failed only in the documented auth/profile baseline set.
