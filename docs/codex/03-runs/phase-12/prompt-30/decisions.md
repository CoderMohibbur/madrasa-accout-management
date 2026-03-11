# Decisions

- Prompt-30 implemented shared account-state reads on `users` and adapted existing checks to use them, rather than widening scope into guardian linkage, donor settlement schema, or final route redesign.
- The authoritative read order is now: explicit `approval_status` / `account_status` / `deleted_at` when present, else legacy-compatible fallback from `email_verified_at` and implicit `active`.
- Login approval is no longer derived from raw `email_verified_at` alone; it now requires approved account state, active lifecycle, and no user-level deletion marker.
- Existing `verified` middleware remains in place for now; prompt-30 adds separated account-state reads underneath the current route stack instead of doing the final blanket-`verified` removal.
- `/dashboard` redirect logic now requires a matching eligible donor/guardian portal profile before redirecting by role order; role-only portal rows fail closed until later donor/guardian auth slices add their no-portal experiences.
- Validation used the local Laragon PHP 8.2 runtime because the local 8.4 runtime lacked `mbstring`; the broader suite still failed only in the documented auth/profile baseline set.
