# Report

This prompt defines the smallest safe multi-role implementation slices on top of the approved donor slice plan, guardian slice plan, and prompt-16 multi-role account analysis. No contradiction was found with prompt-12, prompt-13, prompt-14, prompt-15, or prompt-16. The key planning constraint is that multi-role behavior must layer on top of already-correct donor and guardian contexts rather than being smuggled into donor or guardian implementation as a side effect.

## Multi-Role Slice Order

1. `MR1` - Shared eligible-context resolver foundation
2. `MR2` - Neutral multi-role home and chooser entry
3. `MR3` - In-portal donor/guardian context switching affordances
4. `MR4` - Final route/middleware redirect alignment

## Multi-Role Slices

### `MR1` - Shared eligible-context resolver foundation

- Area: shared multi-role eligibility logic
- Primary type: service-first
- Goal: derive eligible contexts for one authenticated account without mixing scopes:
  - donor portal eligible
  - guardian informational eligible
  - guardian protected eligible
- Must preserve:
  - one shared `users` account model
  - donor-owned and guardian-owned data isolation
  - no auto-linking or role-claim side effects
  - no dependence on raw role ordering
- Dependencies:
  - prompt-29 account-state schema foundation
  - prompt-30 account-state read-path adaptation
  - prompt-32 email/phone verification foundation where applicable
  - prompt-35 donor auth and portal access
  - prompt-36 guardian informational portal
  - prompt-37 guardian protected portal gating for protected-guardian eligibility derivation
- Rollback-safe checkpoint:
  - the resolver exists as an internal derivation layer, but direct `/donor` and `/guardian` entry still behave independently if the shared home wiring is not enabled yet

### `MR2` - Neutral multi-role home and chooser entry

- Area: shared home/dashboard behavior
- Primary type: route-first
- Goal: replace raw guardian-first shared-home routing with eligibility-based entry behavior:
  - one eligible context -> direct redirect
  - multiple eligible contexts -> neutral chooser
  - no eligible portal contexts -> existing non-portal fallback
- Must preserve:
  - no donor/guardian mixed data on the chooser
  - no management-route breakage
  - no donor-boundary reopening
  - no weakening of protected guardian gating
- Dependencies:
  - `MR1`
  - prompt-18 route/middleware/policy analysis
  - prompt-20 through prompt-22 UI analysis outputs
  - prompt-28 shared UI foundation for consistent shared-home presentation
- Rollback-safe checkpoint:
  - the chooser route can be removed and `/dashboard` can temporarily revert to prior behavior while direct donor and guardian routes remain intact

### `MR3` - In-portal donor/guardian context switching affordances

- Area: cross-context navigation
- Primary type: UI-first
- Goal: add explicit, eligibility-aware switching inside donor and guardian portal chrome so multi-role users can move between eligible contexts without URL guessing.
- Must preserve:
  - switching is explicit and not permission-granting
  - deep links stay domain-local
  - donor pages never display guardian summaries
  - guardian pages never display donor summaries
- Dependencies:
  - `MR2`
  - prompt-35 donor portal surfaces
  - prompt-36 guardian informational portal surfaces
  - prompt-37 guardian protected portal surfaces where protected switching is available
  - prompt-28 shared UI foundation
- Rollback-safe checkpoint:
  - switch controls can be removed while keeping the neutral chooser and direct portal routes as safe fallback navigation

### `MR4` - Final route/middleware redirect alignment

- Area: shared routing and access cleanup
- Primary type: integration-first
- Goal: retire the remaining guardian-first redirect assumptions and align shared-home middleware/redirect behavior with the approved eligibility model after the home and switching slices are proven safe.
- Must preserve:
  - existing management route names and management behavior
  - donor and guardian object-level ownership protections
  - backward-compatible direct `/donor` and `/guardian` route entry
- Dependencies:
  - `MR1` through `MR3`
  - prompt-18 route/middleware/policy analysis
  - prompt-40 route/middleware/policy finalization
- Rollback-safe checkpoint:
  - final shared-home redirect logic can be temporarily rolled back to the pre-finalization behavior without removing direct donor/guardian routes or the underlying eligibility resolver

## Dependencies

### Auth Dependencies

- prompt-29 and prompt-30 must establish the shared account-state foundation so multi-role eligibility is derived from stable read-path rules instead of the current overloaded auth shortcuts.
- prompt-32 remains relevant because guardian informational access and later shared eligibility logic must not be trapped inside blanket `verified` assumptions.

### Donor Dependencies

- prompt-35 must finish donor auth/account behavior, donor portal gating, and donor read-path eligibility before multi-role home logic can safely advertise donor context as available.
- Prompt-12 donor slices remain preserved: multi-role planning does not reopen guest donation, donor payable foundation, or donor receipt/history boundaries.

### Guardian Dependencies

- prompt-36 must land the additive, non-sensitive guardian informational surface before multi-role switching can safely target guardian informational context.
- prompt-37 must preserve protected guardian gating and linkage-sensitive reads before donor-plus-guardian-protected coexistence can be surfaced at shared home.
- Prompt-15 guardian slices remain preserved: multi-role planning does not collapse informational and protected guardian boundaries back together.

### Routing And Policy Dependencies

- prompt-18 should analyze the current route, middleware, and policy structure before multi-role implementation starts so the guardian-first `/dashboard` redirect is corrected safely rather than ad hoc.
- prompt-40 is the right place for the final route/middleware/policy alignment after the dedicated multi-role home/switching slice is live.

### UI Dependencies

- prompt-20 through prompt-22 must define the shared UI direction, screen map, and component plan so the multi-role chooser is intentionally low-scope rather than another mixed dashboard.
- prompt-28 should provide the shared UI foundation used by the chooser and switch affordances.

## Earliest Safe Introduction Phase

- The earliest safe roadmap phase for live multi-role introduction is `docs/codex/01-prompts/prompt-39-multi-role-home-and-switching.md`.
- Multi-role should not be introduced earlier inside prompt-35, prompt-36, or prompt-37, because doing so would widen donor or guardian slices beyond their approved scope.
- The minimum prerequisites for prompt-39 are:
  - prompt-29
  - prompt-30
  - prompt-32
  - prompt-35
  - prompt-36
  - prompt-37
- At prompt-39, the smallest safe live rollout is `MR1` through `MR3` for users who are already independently eligible for more than one approved context.
- `MR4` is the follow-on hardening/alignment step and fits prompt-40 better than prompt-39.

## Rollback Checkpoints

1. After `MR1`: shared eligibility derivation exists, but shared-home behavior can still fall back without affecting direct donor or guardian routes.
2. After `MR2`: the neutral chooser is live for multi-eligible users, but removing it restores prior entry behavior without changing donor or guardian domain gates.
3. After `MR3`: in-portal switching is live, but removing switch controls leaves chooser-based or direct-route navigation intact.
4. After `MR4`: final redirect/middleware cleanup is live, but route-level fallback can still restore the earlier shared-home behavior while keeping separate donor and guardian routes operational.

## Completion Status

- No contradiction with prompt-12 through prompt-16 was found.
- No correction pass was required.
- Prompt-17 is complete.
- No hard blocker prevents prompt-18.
