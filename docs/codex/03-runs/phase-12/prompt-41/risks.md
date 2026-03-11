# Risks

- Until a real production-safe `ADMISSION_EXTERNAL_URL` replaces the placeholder, the public and guardian informational admission surfaces will intentionally keep their fallback messaging and suppress the live CTA.
- The MySQL-backed testing database can produce false migration collisions if multiple Laravel test commands run at the same time; validation for this prompt was rerun sequentially to avoid that operational noise.
