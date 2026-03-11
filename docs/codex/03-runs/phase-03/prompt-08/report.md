# Report

This prompt re-runs Google sign-in onboarding analysis against the live repository plus approved prompt-01 to prompt-07 outputs. The repo currently has no Google auth package, no Google config surface, no Google auth routes/controllers, no external identity persistence, and no safe separation between email verification and approval in the live login flow. Prompt-08 therefore defines the target onboarding model without treating Google sign-in as already implemented behavior.

## Readiness Gap List

- `composer.json` does not include `laravel/socialite` or any Google provider package, so the repo has no installed Google OAuth client integration.
- `config/services.php` has no `google` configuration block, and there are no `.env`-backed Google OAuth settings wired into the app.
- `routes/auth.php` and the auth controllers expose only password/Breeze email-verification flows. There are no Google redirect, callback, link, or unlink routes.
- There is no account-level external identity persistence such as a dedicated provider-link table or provider columns on `users`.
- The live repo still overloads `email_verified_at` as both email verification and login approval. Google sign-in cannot be added safely on top of that coupling without keeping email verification separate from approval in the target design.
- The live repo still uses broad `verified` middleware and role-based redirects. A new Google auth path would currently inherit those incorrect boundary assumptions unless later prompts separate verification from portal eligibility and context routing.
- There is no neutral post-login home for multi-context users. The live repo still redirects guardians before donors on `/dashboard`, which is not safe as the final Google post-login rule for donor-plus-guardian accounts.
- The repo has no explicit guardian informational portal yet, which makes first-release guardian Google onboarding riskier than donor/public onboarding.
- Real Google OAuth credentials and callback values are absent. Dummy placeholders were recorded under `docs/codex/06-production-replace/prompt-08-google-oauth-placeholders.md`.

## Target Google Sign-In Model

- Google sign-in should be an optional alternate onboarding and sign-in method layered on top of the same single authenticated `users` account model.
- Google sign-in must not create a separate auth table, separate guard, or separate donor/guardian account system.
- The safest target persistence model is a dedicated external-identity linkage record rather than overloading `users` with one provider-specific column. The linkage record should be able to store at minimum:
  - provider name
  - provider subject / external user id
  - normalized provider email snapshot
  - provider-reported email verification flag
  - linked user id
  - linked / last-used timestamps
- Provider-asserted verification applies only to the email channel:
  - if Google returns a verified email, the email verification axis may be marked verified in the target model
  - it does not imply phone verification
  - it does not imply approval
  - it does not imply donor or guardian role assignment
  - it does not imply portal eligibility
  - it does not imply guardian linkage or protected access
- Google sign-in must follow the same open-registration rule as prompt-06:
  - first-time Google onboarding may create only a base identity or capture donor/guardian intent
  - it must not automatically grant donor portal eligibility, guardian portal eligibility, guardian linkage, or protected access
- If Google does not return a usable verified email, the smallest safe rollout should not auto-create or auto-link an account. The user should fall back to local onboarding or a later explicit linking step.

## First-Time Google Sign-In Behavior

- First-time public Google sign-in:
  - if Google returns a verified email and no existing linked identity or conflicting local account exists, create one base `User`
  - seed `name` from Google only as a convenience value
  - treat the email channel as verified in the target verification model
  - leave phone verification untouched
  - apply the same approval policy as local registration instead of silently bypassing it
  - land the account in `public_only` or equivalent neutral onboarding state
- First-time donor-branded Google sign-in:
  - create the same base account
  - capture donor intent only, or at most create one inactive donor profile
  - do not auto-enable donor portal access
  - donor donation rules remain separate from Google sign-in itself
- First-time guardian-branded Google sign-in:
  - create the same base account
  - capture guardian intent only, or at most create one unlinked guardian profile
  - do not auto-link students
  - do not auto-expose invoices, receipts, or payments
  - do not auto-enable guardian protected access
- If Google returns no email or an unverified email:
  - do not auto-create a new account in the minimal rollout
  - require fallback to local registration or a later explicit account-link flow

## Account-Linking Rules

- Safest preferred link path:
  - an already-authenticated user explicitly links Google from inside their account settings
  - this removes most ambiguity about account ownership
- Safe unauthenticated match path:
  - if Google returns a verified normalized email
  - and that email matches exactly one existing `users.email`
  - and no conflicting Google provider identity is already linked elsewhere
  - then link Google to that existing account instead of creating a new user
- Never create a second `users` row when a verified-email match already exists on the canonical account table.
- Never auto-link based on donor email, guardian email, donor mobile, or guardian mobile alone.
- Never auto-link based on guest donation contact data alone.
- If the Google provider subject is already linked to the same user, sign in that same user.
- If the Google provider subject is already linked to a different user, stop and require recovery or manual support rather than silently reassigning the identity.

## Duplicate-Email Handling Rules

- Use normalized `users.email` as the canonical duplicate check.
- If Google returns a verified email that matches an existing local account:
  - link to that account
  - do not create a second account
- If Google returns a verified email and no local account exists:
  - create one base account only
- If Google returns an email that matches donor or guardian profile data but not `users.email`:
  - do not auto-link
  - require explicit authenticated linking or manual claim flow later
- If the matched local account is already linked to another Google identity:
  - block automatic linking
  - require recovery or manual support
- If Google returns no usable verified email:
  - do not trust the email for duplicate resolution in the minimal rollout

## Matched Local Account Handling

- Unverified local account:
  - if Google asserts the same email as verified, Google may satisfy the email-verification axis in the target model
  - do not auto-approve the account
  - do not auto-enable donor or guardian portal access
  - because the live repo still overloads `email_verified_at`, this case is a readiness gap until verification and approval are separated
- Unapproved local account:
  - Google may link to the same account
  - approval state must remain unchanged
  - the user should see the same pending-approval outcome that local auth would enforce for that state
  - Google sign-in must never serve as an approval bypass
- Inactive or suspended local account:
  - do not auto-reactivate the account
  - do not grant a full active session
  - show a support or inactive-state message instead
- Donor-only local account:
  - link Google to the same user
  - preserve donor profile and donor history
  - do not create a guardian profile
  - route to donor context only if donor portal eligibility already exists; otherwise route to the neutral/no-portal surface
- Guardian-only local account:
  - link Google to the same user
  - preserve guardian profile
  - if the guardian is unlinked, limit to guardian informational access only
  - if the guardian is linked and otherwise eligible, later prompts may allow the guardian protected portal, but Google sign-in itself does not create that eligibility
- Dual-role local account:
  - link Google to the same user
  - preserve both domain contexts
  - do not create duplicate profiles
  - post-login routing should be eligibility-driven and eventually use a neutral chooser rather than the current guardian-first redirect

## Donor-Only, Guardian-Only, And Dual-Role Expansion

- Google sign-in must reuse the same account for later donor or guardian expansion exactly as prompt-06 defined for local registration.
- A Google-created public account may later add donor intent, guardian intent, or both on the same user.
- Donor expansion after Google bootstrap:
  - create or attach only one donor profile
  - do not auto-enable donor portal eligibility
  - preserve separation between donor onboarding and donor portal access
- Guardian expansion after Google bootstrap:
  - create or attach only one guardian profile
  - keep it unlinked until a separate linkage or claim step succeeds
  - do not expose protected student-owned data before linkage
- Dual-role expansion after Google bootstrap:
  - keep both contexts on the same account
  - keep donor and guardian scopes separate
  - require later context switching rules instead of collapsing contexts into one default protected surface

## Risk Areas

- The highest takeover risk is incorrect auto-linking by email. Google sign-in must never overwrite or silently reassign an existing linked external identity.
- The live `email_verified_at` coupling is still the biggest implementation hazard because provider-verified email and admin approval are separate concepts.
- Current `verified` middleware usage is too broad for the frozen donor and guardian rules, so later implementation must not simply plug Google into the current route stack.
- Guardian onboarding remains riskier than donor/public onboarding because the separate informational guardian surface is not implemented yet.
- The current guardian-first `/dashboard` redirect is unsafe as the final post-Google behavior for dual-role accounts.
- If a user changes their Google account email later, the system must trust the stored provider subject link more than a changed email snapshot to avoid accidental relinking.
- Password-based recovery and Google-based sign-in must coexist without allowing Google linking to become a shortcut around existing account-safety controls.

## Minimal Safe Rollout Scope

- Add Google sign-in only as an optional public and donor-friendly onboarding/sign-in path first.
- Support explicit authenticated Google linking for existing local accounts.
- Support unauthenticated auto-link only when Google returns a verified normalized email that matches exactly one existing `users.email` and no provider-link conflict exists.
- Treat Google first-time sign-in as base-account creation plus optional donor intent capture only.
- Allow guardian existing-account linking, but defer broad first-time guardian Google onboarding until the guardian informational surface and approval/verification separation are in place.
- Do not use Google sign-in to create donor portal eligibility, guardian portal eligibility, guardian linkage, or protected access.
- Use only dummy placeholders for provider configuration during planning:
  - `GOOGLE_CLIENT_ID=replace-me-google-client-id`
  - `GOOGLE_CLIENT_SECRET=replace-me-google-client-secret`
  - `GOOGLE_REDIRECT_URI=https://example.test/auth/google/callback`
- Record all replace-later OAuth items under `docs/codex/06-production-replace/` and require real credentials before any live testing.
