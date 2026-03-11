# Prompt 14 Admission Information Boundary

## Public Admission-Information Scope

- institution overview and contact basics
- general class/program overview
- high-level admission availability and requirements
- high-level process summary
- generic required-document checklist
- external application CTA only

## Authenticated Admission-Information Scope

- all approved public admission information
- guardian-specific onboarding/help messaging
- self-only status/reminder messaging inside the light guardian informational portal
- no student, invoice, receipt, payment, or protected guardian data

## External Application Link Rules

- explicit external handoff only
- one canonical destination later configured centrally
- approved placements only on public and guardian informational surfaces
- clear external wording
- no hidden internal workflow or embedded full application flow

## Hard Non-Goals

- no internal admission application forms
- no draft/resume workflow
- no document upload
- no internal admission status tracking
- no seat assignment or enrollment workflow
- no admission-payment workflow owned by this Laravel app

## Content Approach

- keep admission content small, curated, and mostly public
- reuse the same core content across public and guardian informational surfaces
- reserve authenticated-only content for self-only help/status messaging
- keep the external application link as the clear product boundary
