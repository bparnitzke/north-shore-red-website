# Early-Vote Commitment Tool

The `/make-a-plan/` route now presents North Shore Red's early-vote message, an early-vote commitment card, and a direct link to the official MyVote Wisconsin website. `/vote/` continues to redirect to this route.

## Current scope

The commitment card records only:

- Whether the visitor affirmed an intent to vote before Election Day.
- The visitor's current intended method: early in person, absentee by mail, or unsure.
- Name and email address for handling the submission.
- An optional mobile number.
- Separate, optional email and SMS enrollment decisions.
- Submission timestamp, source URL, consent-program identifiers, and the exact versioned consent language displayed.

The form does not request a birth date, street address, voter-registration record, candidate preference, or information entered on MyVote Wisconsin. North Shore Red does not receive information entered on the state website.

## Product boundaries

- Do not scrape, imitate, or visually masquerade as MyVote Wisconsin.
- Clearly label MyVote links as official Wisconsin election resources.
- Do not pass personal information to MyVote through URL parameters.
- The MyVote link remains available without submitting the commitment card or joining either communication program.
- Email and SMS controls remain separate and unchecked by default.
- Do not add exact voting dates, addresses, or other voting-plan details to storage unless they become necessary for an approved reminder program.
- Do not add advertising pixels or session-replay tools to this page without explicit privacy review.

## Current interface states

1. Early-voting-method selection.
2. Required early-vote commitment affirmation.
3. Optional email enrollment.
4. Optional SMS enrollment with an always-visible mobile field.
5. Success without either communication enrollment.
6. Success with email, SMS, or both.
7. Validation error.
8. Submission or ledger error.
9. Direct access to Privacy, Terms, and MyVote on mobile.

## Before production collection

- Apply `database/schema.mysql.sql` for a fresh database or the dated migration for an existing consent database.
- Configure database credentials outside the repository and verify encrypted backups and access restrictions.
- Test opted-in and declined records for both email and SMS.
- Approve retention, deletion, export, and incident-response procedures.
- Have counsel and the selected providers review the public disclosures and policies.
- Add rate limiting and production bot protection in the hosting layer.
- Require HTTPS.

The current code records consent but does not send email-list campaigns or text messages. Provider integrations, unsubscribe/STOP webhooks, and production message delivery remain separate work.
