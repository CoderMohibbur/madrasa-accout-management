# Report

This prompt defines guest donation onboarding on top of the prompt-09 and prompt-10 donor boundaries. The live repo still has no internal guest donation surface, no donor-side payment initiation routes, and a public site that links externally to `https://attawheedic.com/donation/`. Prompt-11 therefore defines the safe target guest-onboarding model without treating the current external donation link, the current `verified` middleware usage, or the current `users.email_verified_at` login gate as the final rule.

## Repo-Grounded Current-State Constraints

- Public landing and donation entry are currently external-link based; the internal repo does not yet expose a guest donation form or public donor payment status page.
- `payments.user_id` and `receipts.issued_to_user_id` are nullable, which is compatible with a true guest donation path.
- `User` remains the single base account model and still uses email as the current login identifier.
- `Donor` is a separate domain profile with its own `portal_enabled`, `isActived`, and `isDeleted` flags, and the donor portal still reads legacy donation history through `doner_id`.
- Current public registration creates only a base `User`, not a donor profile, and current login remains narrower than the target rules because it still blocks when `email_verified_at` is null.
- Prompt-10 already froze that guest and identified donations should share the same safe donor payable framework where possible, but guest contact capture must not automatically create accounts, portal eligibility, or donor linkage.

## Terminology Rules

- `guest donor`
  - donates without an authenticated account
  - may provide no name, phone, email, or any combination of them
  - receives only transaction-specific status and receipt access
  - does not receive donor portal access by default
- `identified donor`
  - donates from or into a known account-linked identity
  - may later have donor-domain profile linkage and donor portal eligibility, but those remain separate decisions
- `anonymous-display donor`
  - a public-display preference, not a third identity system
  - can apply to either a guest donor or an identified donor
  - never removes internal traceability or reconciliation data

## Guest Donation Flow

### 1. Public entry

- The public donor flow must allow direct amount-based donation entry without prior registration.
- The user should choose between clearly distinct paths:
  - donate as guest
  - sign in / register and donate as an identified donor
- `anonymous-display` should be a visibility preference inside either path, not a separate third entry path.

### 2. Guest donation form

The minimal safe guest form should capture:

- required:
  - donation amount
  - currency if multi-currency is ever enabled, otherwise use the system default
  - donor mode = `guest`
  - display mode = `identified` or `anonymous_display`
- optional:
  - name
  - email
  - phone
  - message / dedication / note if later needed

The form should explicitly explain:

- registration is optional
- supplied email or phone is unverified by default
- supplying contact information does not create an account
- guest donation does not create donor portal access

### 3. Intent creation

- Create one `donation_intent` as frozen in prompt-10.
- Store:
  - donor mode = `guest`
  - display mode
  - amount and currency
  - `name_snapshot`, `email_snapshot`, `phone_snapshot`
  - public reference
  - guest access secret or token hash
  - operational traceability metadata such as IP, user agent, session or device fingerprint, timestamps, and consent flags
- Do not create a `users` row here by default.
- Do not create a `donors` row here by default.

### 4. Payment attempt and checkout

- Create a `payments` attempt against the `donation_intent`.
- Keep `payments.user_id = null` for guest donation attempts.
- Redirect into the verified donor-payable flow from prompt-10.
- Do not create a legacy `transactions` row or donor portal history row at initiation time.

### 5. Return, verify, and finalize

- Browser success, fail, and cancel pages remain informational only.
- Final settlement still requires authoritative verification or manual reconciliation per prompt-10.
- On authoritative success:
  - finalize the payment attempt
  - create one `donation_record`
  - create one receipt
  - keep receipt access transaction-specific
- On failure, timeout, or ambiguity:
  - do not create a `donation_record`
  - do not create a receipt
  - keep the outcome retryable or manually reviewable according to prompt-10 rules

### 6. Guest completion state

On successful guest completion, the user should receive:

- receipt number
- public status / receipt reference
- clear reminder that no account was created automatically
- optional next actions:
  - download receipt
  - send receipt to supplied contact channel if consented and later supported
  - create an account or claim this donation later

## Identity-Capture Rules

### Field rules

- `amount`
  - required
- `name`
  - optional
  - affects receipt presentation or acknowledgements only
  - does not change the donor into an identified donor by itself
- `email`
  - optional
  - operational contact only unless later used in an explicit account flow
- `phone`
  - optional
  - operational contact only unless later used in an explicit account flow
- `anonymous_display`
  - optional preference
  - allowed for guest or identified donations

### Conditional rules

- No name, email, or phone should be required merely to allow guest payment.
- If neither email nor phone is supplied:
  - donation is still allowed
  - self-service recovery later depends entirely on the saved transaction-specific reference
- If email or phone is supplied:
  - the channel remains unverified by default
  - the data may be used for operational receipt delivery or support only if the donor was told that clearly and, where needed, consented
- If the user wants immediate account-based receipt history instead of transaction-specific access:
  - the flow should switch to identified donation or later claim flow, not silently mutate the guest flow in place

## Lightweight Account-Creation Rules

### When the system should create no account

Create no account in the default guest path, including when the guest supplies:

- no identity fields
- name only
- email only
- phone only
- name plus email
- name plus phone
- email plus phone
- name plus email plus phone

The presence of contact data alone must not create a `users` row, must not create donor portal eligibility, and must not attach a donor profile.

### When the system should only store payment-side contact information

The system should only store payment-side contact information when:

- the donor selected the guest path
- the donor did not explicitly choose account creation
- the donor did not complete a later authenticated claim flow

In that case, store contact only on `donation_intent`, `donation_record`, receipt-delivery metadata, and audit/reconciliation surfaces as operational snapshots.

### When the system should create a lightweight user

For the smallest safe rollout, guest checkout itself should not create a lightweight user automatically.

A lightweight user should be created only in a separate explicit post-donation or later-claim flow, when all of the following are true:

- the donor intentionally chooses to create or claim an account
- the system has a duplicate-safe base-account path available
- the donor supplies or confirms an email because email remains the canonical login identifier in the smallest safe rollout
- the system does not auto-grant donor portal eligibility, donor role-driven access, or donor profile linkage as part of that account creation

If the public donation page later offers a “create account and donate” option before payment, that should branch out of the guest flow and become an identified-donor onboarding path, not a silent guest-flow side effect.

### When the system should create or attach a donor profile

Do not create or attach a donor profile during default guest checkout.

Create or attach a donor profile only when:

- the guest later authenticates or creates a base account
- the guest explicitly claims settled donations
- the system has duplicate-safe proof for donor-profile creation or linkage
- donor-domain features actually need that profile

Even then:

- the created or attached donor profile should start as non-portal by default unless a separate donor eligibility rule later enables portal access
- profile creation or attachment must not itself grant donor portal eligibility

## Unverified Contact Rules

When email or phone is present but unverified:

- store it as operational contact data only
- do not auto-create an account
- do not auto-verify the channel
- do not auto-link to an existing account or donor profile
- do not auto-grant donor portal access
- do not treat contact similarity as ownership proof

Email-specific rules:

- an unverified email may later be used for:
  - receipt delivery
  - claim invitation messaging if explicitly requested
  - prefilled account creation during explicit claim
- it must not silently become the owner of all matching guest donations

Phone-specific rules:

- an unverified phone may later be used for:
  - operational support
  - optional SMS receipt or claim notices when that channel exists
- it must not be treated as a canonical login identifier in the minimal rollout
- phone-only guest donation must not silently create a phone-based user account

## Later Account-Claim Rules

Guest donors should be allowed to claim past settled donations later, but only through an explicit duplicate-safe flow.

### Claim prerequisites

The safest claim path requires:

- a settled `donation_record`
- an authenticated account or an explicit base-account creation step
- at least one donation-specific proof item, such as:
  - public reference plus guest access secret
  - receipt number plus another opaque claim token
  - provider-confirmed donation reference captured at settlement

Raw name matching or raw email / phone matching alone is not sufficient.

### Claim behavior

On a successful claim:

- attach the specific proved donation to the new or existing base account
- optionally create or attach one donor profile only if donor-domain logic needs it
- keep donor portal eligibility separate
- allow transaction-specific receipt access immediately through account ownership
- do not silently grant full donation-history portal browsing unless later donor eligibility rules allow it

### Bulk claim behavior

The first safe self-service claim should attach only the specifically proved donation.

Broader history grouping may be allowed later only when:

- the user is authenticated
- at least one contact channel has been explicitly verified or otherwise strongly proved
- duplicate conflicts are absent
- the system can explain which donations are being attached and why

### No-contact guest donations

If a guest donation was made with no email and no phone:

- self-service claim should rely entirely on the saved public reference and guest secret
- if the donor loses those artifacts, recovery may require manual support

### Phone-only guest donations

If a guest donation has only phone captured:

- it should remain guest-only until the donor either adds email for a base account or the product later supports a safe phone-account model
- phone similarity alone must not auto-claim the donation into an account

## Anti-Abuse And Traceability Rules

Guest donation must stay easy to use without becoming untraceable or abuse-friendly.

### Mandatory traceability

Every guest donation intent and attempt should record:

- internal intent id
- public reference
- hashed guest access secret
- payment attempt id and idempotency key
- IP address
- user agent
- session or device fingerprint where available
- timestamps for intent creation, redirects, callbacks, and finalization
- provider metadata and gateway event trail
- operational contact snapshots
- display mode

### Abuse controls

- Rate-limit donation-intent creation by IP and device fingerprint.
- Rate-limit repeated payment-attempt creation for the same intent.
- Use idempotency keys to prevent duplicate checkout rows.
- Add CAPTCHA or other challenge only as a risk-based control, especially after repeated failed or high-volume public attempts.
- Do not reveal whether a supplied email or phone already belongs to an account, donor profile, or prior donation history.
- Keep guest access tokens opaque, high-entropy, and stored hashed where practical.
- Trigger manual review for suspicious patterns such as:
  - many attempts from one IP or device
  - repeated mismatched callback data
  - abnormal high-value or burst donation attempts
  - repeated guest claims against different accounts

### Receipt and reconciliation safety

- Receipt issuance remains post-settlement only.
- Guest receipt access remains transaction-specific, not portal-wide.
- Receipt delivery to email or phone must not be mistaken for account verification.
- Reconciliation continues to act on `donation_intent`, `payments`, and `donation_record`, not on legacy `transactions`.
- Anonymous-display donations must hide public identity display while preserving full internal auditability.

## Minimal Safe Guest Donation Rollout

- public direct amount-based guest donation
- optional name, email, and phone capture
- no required registration
- no automatic account creation
- no automatic donor-profile creation
- no automatic donor portal eligibility
- guest receipt and status access through opaque transaction-specific references
- later explicit account-claim flow for donors who want account-linked history
- guest and identified donations sharing the same prompt-10 donor payable framework

## Prompt-12 Readiness

No hard blocker was discovered inside prompt-11 scope. Prompt-12 should next plan the donor implementation slices around:

- distinct guest and identified donor entry paths
- explicit post-payment claim or conversion instead of automatic account creation
- donor payable safety remaining separate from legacy posting
- donor portal eligibility remaining separate from payment completion and account creation
