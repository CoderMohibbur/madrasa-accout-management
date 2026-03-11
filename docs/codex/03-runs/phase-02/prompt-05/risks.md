# Risks

- The current access chain still mixes `verified` middleware, role middleware, profile flags, and linkage policies, which can hide where a denial is really coming from.
- `email_verified_at` remains overloaded, so current portal eligibility still reflects approval/verification coupling instead of the frozen boundary model.
- The current repository has no implemented guardian informational portal, so later prompts must avoid assuming that guardian role automatically means protected guardian access.
- The current guardian-first `/dashboard` redirect can misrepresent final multi-role boundary behavior if later prompts treat it as the approved design.
- Donor portal data still depends on legacy donation transactions and explicit receipt binding, so later prompts must not infer a broader donor authorization model from the current read-only surface.
