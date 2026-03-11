# Prompt 08 Google OAuth Placeholders

Use dummy placeholders only during analysis or scaffolding. None of the values below are production-ready.

## `GOOGLE_CLIENT_ID`

- Placeholder: `replace-me-google-client-id`
- What it is: Google OAuth web application client ID
- Where it is used: `.env`, future `config/services.php` Google provider block, future Google redirect/callback auth flow
- Whether it blocks real testing: yes; real Google redirect initiation and callback validation cannot be tested without it
- Replace before production: yes

## `GOOGLE_CLIENT_SECRET`

- Placeholder: `replace-me-google-client-secret`
- What it is: Google OAuth web application client secret
- Where it is used: `.env`, future `config/services.php` Google provider block, future Google callback token exchange
- Whether it blocks real testing: yes; live token exchange with Google cannot work without it
- Replace before production: yes

## `GOOGLE_REDIRECT_URI`

- Placeholder: `https://example.test/auth/google/callback`
- What it is: registered Google OAuth callback URL for this application
- Where it is used: `.env`, future `config/services.php` Google provider block, Google Cloud console OAuth client configuration
- Whether it blocks real testing: yes; callback mismatch will break live Google sign-in
- Replace before production: yes

## `APP_URL`

- Placeholder: `https://example.test`
- What it is: application base URL used to construct or validate environment-specific redirect behavior
- Where it is used: `.env`, future local/preview/prod callback alignment, Google Cloud console redirect whitelist coordination
- Whether it blocks real testing: yes if it does not match the actual environment and callback host
- Replace before production: yes
