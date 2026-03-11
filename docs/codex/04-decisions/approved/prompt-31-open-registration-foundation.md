# Prompt 31 Open Registration Foundation

Approved implementation decisions from prompt-31:

- Keep one unified registration backend and support donor and guardian entry pages only as intent-preselection surfaces.
- Open registration creates a shared base account plus optional donor or guardian foundation data; it does not auto-grant donor or guardian portal-driving roles.
- Self-registered accounts receive the compatibility role `registered_user` so they remain distinct from management, donor, and guardian role checks while later prompts complete the no-portal rollout.
- New self-registered accounts are created with `approval_status = approval_not_required`, `account_status = active`, and `email_verified_at = null`.
- Donor intent creates a linked donor draft profile with portal access disabled and donor activation still off.
- Guardian intent creates a linked guardian draft profile with portal access disabled and no protected linkage or sensitive data access.
- The safe post-registration destination is `registration.onboarding`, while existing legacy verified routes keep their current verification boundary until later prompts explicitly change that stack.
- `registered_user` accounts must fail closed on legacy management surfaces.
