# Decisions

- Prompt-33 activates only the additive public guest-donation entry shell and start action; it does not activate donor payable persistence or payment finalization.
- The public guest-donation route space is `GET /donate` plus `POST /donate/start`.
- `amount` is required for guest donation entry; `name`, `email`, and `phone` remain optional guest-only snapshots.
- Guest email is normalized to lowercase and guest phone is normalized with the same phone-normalization helper introduced in prompt-32, but neither value is linked to account verification.
- Guest donation start stores only a session-scoped draft in this slice; it does not create `users`, `donors`, `donation_intents`, `payments`, `donation_records`, or receipts.
- Authenticated accounts may still use the guest-donation route without affecting their prompt-31 registration state or prompt-32 email/phone verification state.
- `anonymous-display donor` remains a visibility preference only and does not create a separate donor identity class.
- The public home page Donate CTA now targets the internal prompt-33 guest-donation entry screen.
