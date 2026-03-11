# Risks

- Guest donation start is still session-only; the next prompt must add the dedicated donor payable persistence before checkout can become durable or resumable.
- The internal guest-entry CTA now replaces the old external Donate link on the welcome page, so any need for a dual-path donation entry would require an explicit later decision.
- Guest contact snapshots are normalized but intentionally not deduplicated or linked in this slice; later claim or identified-donation work must keep that fail-closed posture.
- The local runtime does not expose direct `git` or `php` executables in the default bash environment, so validation relied on the approved Windows PHP path and `.git/HEAD` inspection.
