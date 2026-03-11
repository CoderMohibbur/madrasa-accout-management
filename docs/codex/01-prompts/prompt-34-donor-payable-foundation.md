Use the docs/codex-autopilot workflow from my project.

This task is PHASE_12_DONOR_PAYABLE_FOUNDATION_IMPLEMENTATION.
Implement only the approved donor payable foundation slice.

Before coding:
1) restate the exact approved slice
2) restate the approved donor payment-domain model
3) restate what legacy behavior must not be reused unsafely

Implement only:
- dedicated donation intent/payable/record foundation as approved
- guest donation support as approved
- identified donation support as approved
- optional donor identity capture as approved
- no unsafe direct finalization against legacy transactions
- no unrelated guardian changes
- no broad accounting redesign beyond approved separation

End with:
- files changed
- donor payable foundation implemented
- guest/identified donation behavior implemented
- legacy safety preserved
- next safe slice
