# Decisions

- Keep guest donation as a distinct path from identified donor donation; do not silently transform guest checkout into account onboarding.
- In the minimal safe rollout, default guest donation creates no account and no donor profile, even when optional name, email, or phone is supplied.
- Guest contact data should be stored as payment-side operational snapshots unless the donor later enters an explicit account-claim flow.
- Create a lightweight base `User` only in a separate explicit post-donation or later-claim flow, not automatically during guest checkout.
- Do not create or attach a donor profile during default guest checkout; donor profile linkage belongs to a later explicit claim or donor-domain onboarding step.
- Unverified guest email or phone must never auto-verify, auto-link to an account, auto-link to a donor profile, or auto-grant donor portal eligibility.
- Later guest-donation claims must require authenticated intent plus donation-specific proof; loose contact matching alone is insufficient.
- `anonymous-display donor` remains a visibility preference that can apply to guest or identified donations and does not create a third identity class.
