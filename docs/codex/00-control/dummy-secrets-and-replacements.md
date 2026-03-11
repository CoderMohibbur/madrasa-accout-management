# Dummy Secrets and Replace-Later Register

This project may require placeholder values during Codex analysis or scaffolding work.

## Rule
Use dummy placeholders only. Never treat them as production-ready.

## Must replace before production
- Google OAuth client ID
- Google OAuth client secret
- Google OAuth redirect URI if environment-specific
- Payment gateway merchant IDs
- Payment gateway secret keys
- Payment callback URLs if environment-specific
- SMS provider API key
- SMS sender ID
- Email transport credentials
- Any environment URLs or webhook secrets

## Placeholder examples
- `GOOGLE_CLIENT_ID=replace-me-google-client-id`
- `GOOGLE_CLIENT_SECRET=replace-me-google-client-secret`
- `GOOGLE_REDIRECT_URI=https://example.test/auth/google/callback`
- `PAYMENT_MERCHANT_ID=replace-me-merchant-id`
- `PAYMENT_SECRET=replace-me-payment-secret`
- `SMS_API_KEY=replace-me-sms-key`
- `MAIL_USERNAME=replace-me-mail-user`
- `MAIL_PASSWORD=replace-me-mail-password`

## Required note format
Whenever a placeholder is introduced, also record:
- what it is
- where it is used
- whether it blocks real testing
- that it must be replaced before production
