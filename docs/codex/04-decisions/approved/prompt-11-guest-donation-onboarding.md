# Prompt 11 Guest Donation Onboarding

Approved baseline decisions from prompt-11:

- Guest donation stays a distinct path from identified donor donation.
- In the minimal safe rollout, default guest donation creates no account and no donor profile, even when optional contact fields are supplied.
- Guest contact fields remain payment-side operational data until the donor enters an explicit account-claim or conversion flow.
- A lightweight base account may be created only in a separate explicit post-donation or later-claim step, not automatically during guest checkout.
- Donor profile creation or attachment belongs to later explicit donor-domain onboarding or claim linkage, not default guest checkout.
- Unverified guest email or phone must never auto-verify, auto-link to an account, auto-link to a donor profile, or auto-grant donor portal eligibility.
- Later guest-donation claims require authenticated intent plus donation-specific proof; loose contact matching alone is insufficient.
- `anonymous-display donor` remains a visibility preference on guest or identified donations, not a third identity class.
