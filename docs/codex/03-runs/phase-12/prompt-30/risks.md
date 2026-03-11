# Risks

- Auth-only self-service routes still use legacy `auth` behavior, so prompt-31 and prompt-32 must decide whether any of those flows need the new shared account-state enforcement before broader rollout.
- Because prompt-30 intentionally keeps `verified` middleware in place, approved-but-unverified users can authenticate when explicit account-state fields allow it, but they still depend on later verification-flow work to reach verified-only surfaces cleanly.
- Role-only guardian/donor users now fail closed on `/dashboard`, but their later no-portal/informational experiences are still deferred to the donor and guardian auth slices.
- Local validation still depends on the Laragon PHP 8.2 runtime because the local PHP 8.4 runtime in this environment does not currently load `mbstring`.
