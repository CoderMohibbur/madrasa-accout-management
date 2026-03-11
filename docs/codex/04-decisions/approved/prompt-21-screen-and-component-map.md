# Prompt 21 Screen And Component Map

Approved baseline decisions from prompt-21:

- The screen map must explicitly separate current screens, approved future screens, and shared utility screens so later prompts can sequence implementation cleanly.
- The product can be delivered through a small shared template set instead of domain-by-domain bespoke layouts: auth form, informational content, portal overview, portal list, portal detail, payment outcome, donation entry, account state, and context chooser.
- Donor, guardian informational, guardian protected, guest donation, and multi-role home all depend on the same minimum shared component library defined here, with domain differences expressed through content and small token variations only.
- Payment outcome/status surfaces should be treated as one reusable family across guardian, guest, and donor narrow-access use cases.
- Admission handoff remains a shared external-handoff section for approved public and guardian-informational screens only; it is not a separate internal application workflow screen family.
