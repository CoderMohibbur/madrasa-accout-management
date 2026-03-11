# Prompt 33 Guest Donation Entry

Approved implementation decisions from prompt-33:

- Activate only the additive public guest-donation entry shell in this prompt.
- Use `GET /donate` and `POST /donate/start` as the prompt-33 guest-donation entry routes.
- Require only `amount`; keep `name`, `email`, and `phone` optional guest-only snapshots.
- Normalize guest email and phone input, but keep guest contact capture fully separate from account verification, account creation, donor profile creation, and portal eligibility.
- Store only a session-scoped guest draft in prompt-33; durable donor payable persistence belongs to prompt-34.
- Allow authenticated users to use the guest-donation entry route without changing prompt-31 registration state or prompt-32 email/phone verification state.
- Treat `anonymous-display donor` as a visibility preference only, not as a separate donor identity class.
- Point the public home page Donate CTA at the new internal guest-donation entry surface.
