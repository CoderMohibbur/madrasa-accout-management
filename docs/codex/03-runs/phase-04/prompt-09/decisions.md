# Decisions

- Freeze donor payment ability and donor portal eligibility as separate permissions; donors may be allowed to donate without automatically being allowed into donor history/receipt portal surfaces.
- Freeze donor login and donor donation as not universally dependent on email or phone verification; approval and lifecycle remain separate gates.
- Freeze guest donation as allowed without prior registration and with optional human identity fields, while operational traceability remains mandatory.
- Freeze transaction-specific payment status and receipt access as a narrower permission than full donor portal history access.
- Treat `guest donor`, `identified donor`, and `anonymous-display donor` as the canonical donor terms; treat `hidden donor` only as a legacy synonym for `anonymous-display donor`, not as a separate permission class.
- Block recurring donation and saved payment methods from the initial safe donor rollout until a dedicated donor payable and provider-risk model exists.
- Do not treat legacy `transactions` posting as the approved donor live-payment finalization path; prompt-10 must define a dedicated donor payable model next.
