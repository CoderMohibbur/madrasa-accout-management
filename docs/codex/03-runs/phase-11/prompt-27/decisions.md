# Decisions

- The final implementation packet is approved around one shared `users` account model, additive donor and guardian profiles, explicit account-state axes, and eligibility-driven routing rather than raw role ordering.
- The mandatory schema foundation remains limited to shared user account-state columns, guardian linkage-state fields, and donor-domain `donation_intents` plus `donation_records`, with existing `payments` and `receipts` reused.
- Implementation should begin only in the exact prompt-28 through prompt-43 order, with prompt-26's seven rollout waves and prompt-25's cumulative blocker packs preserved.
- Guardian informational rollout must precede guardian protected rollout, and donor payment-domain foundation must precede donor portal history rollout.
- Google sign-in, multi-role switching, and route/middleware finalization remain later additive layers and must not precede independent donor and guardian correctness.
- Implementation may begin with prompt-28 only, and it must stop immediately if any no-go warning from the final packet is triggered.
