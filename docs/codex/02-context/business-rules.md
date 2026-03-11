# Business Rules

## Core access
- Anyone should be able to register.
- Donor must be able to self-register.
- Guardian must be able to self-register.

## Verification
- Both email verification and phone verification must be supported.
- Donors may register and log in without verification.
- Guardians may log in without verification.

## Donation
- Donation must not require prior registration.
- Guest donation must be allowed.
- A donor may donate directly by amount without mandatory account creation.
- Name, phone, and email may be optional during donation, subject to safe payment and reporting rules.
- If phone or email is provided, the system may create or attach a lightweight donor identity according to approved rules.
- If no usable identity data is provided, the donation must still be accepted and safely recorded according to approved terminology.

## Guardian informational portal
- Unverified or unlinked guardians may view non-sensitive institution information after login.
- Guardians may view admission-related information.
- The system must not become a full admission application platform.
- The system should provide information and a link/button to an external admission application.

## Multi-role
- One authenticated user may hold both donor and guardian roles.
- The same authenticated account may access both donor and guardian surfaces if legitimately entitled.

## Safety
- Student-linked academic, invoice, receipt, and payment-sensitive access must remain authorization-controlled.
- Existing accounting, reporting, guardian invoice payment, donor/payment safety, and legacy management logic must remain protected.
