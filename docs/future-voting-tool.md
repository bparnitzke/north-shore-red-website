# Future Voting-Plan Tool Architecture

This project reserves `/make-a-plan/` and `/vote/` for a future Wisconsin voting-plan tool. The first phase intentionally does not build a functional lookup, form submission, database, or SMS integration.

## Purpose

The future tool should help a Wisconsin voter:

- Choose how they prefer to vote.
- Build a personal voting plan.
- Reach the appropriate official MyVote Wisconsin destination at `https://myvote.wi.gov/en-us/`.
- Optionally opt in to automated A2P text reminders after receiving a clear, adjacent disclosure.

## Product boundaries

- Do not scrape MyVote Wisconsin.
- Do not imitate or visually masquerade as MyVote Wisconsin.
- Clearly label MyVote links as official Wisconsin election resources.
- Do not assume voter data can be passed to MyVote through URL parameters unless that behavior is verified and authorized.
- The voter must be able to complete the planning flow and receive the official MyVote link without consenting to SMS reminders.
- Do not collect personal information merely because MyVote asks for it.

## Suggested implementation path

- Keep the marketing site static until the form is approved.
- Add a dedicated route at `/make-a-plan/` with isolated components under `src/components/voting-plan/`.
- Keep plain content and options in TypeScript data files or content collections.
- Add server-side handling only after storage, provider, security, retention, and deletion requirements are approved.
- Keep secrets out of the repository and document required environment variables in `.env.example`.
- Do not expose submissions to client-side analytics.
- Use HTTPS only in production.
- Plan bot protection, server-side validation, rate limiting, and minimal logging.
- Avoid storing full voting-plan details unless needed to provide reminders.
- Document every third party receiving submitted data.
- Do not add advertising pixels or session-replay tools to pages that collect voter information without explicit review.

## Anticipated interface states

1. Voting-method selection.
2. Voting-plan details.
3. Official MyVote destination.
4. Optional reminder enrollment.
5. Unchecked SMS consent.
6. Success without SMS enrollment.
7. Success with SMS enrollment.
8. Validation error.
9. Submission error.
10. Privacy/Terms access on mobile.

The SMS checkbox must never be preselected, disabled in a way that implies it is required, or bundled with general terms acceptance.

## Future consent record fields

If SMS enrollment is approved, preserve auditable consent records including:

- Phone number.
- Submission timestamp.
- Source URL.
- Consent status.
- Exact version of the consent language displayed.
- Program or campaign identifier.
- Relevant voting-plan selections needed to provide the reminders.
- Any later opt-out status.

Do not implement production storage or collect real personal information until the storage system, retention rules, access controls, security requirements, messaging provider, and deletion process are approved.
