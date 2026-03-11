# Decisions

- Prompt-28 was treated strictly as `UF1` through `UF4`; no donor, guardian, payment, schema, or route/business-logic changes were allowed into this run.
- Shared foundation work was limited to `resources/css/app.css`, shared Blade layouts, shared Blade components, the public landing page, and prompt-28 docs/artifacts.
- Existing protected navigation logic in `resources/views/layouts/navigation.blade.php` was preserved; only shared link/button primitives around it were updated.
- Donor and guardian wrappers were consolidated onto one shared portal shell so later prompt-35 and prompt-37 work can adopt a single product family instead of parallel role-specific shells.
- The public landing page was rebuilt onto a shared light-first shell while preserving approved public external handoff links and without introducing auth/protected guardian admission CTAs.
- Validation used the local Laragon PHP 8.2 runtime because the local 8.4 runtime lacked `mbstring`; the resulting full-suite failures matched the pre-existing auth/profile baseline manifest exactly.
