# Contact and Communication Consent Ledger

The contact and early-vote commitment forms record every email and SMS enrollment decision before sending the notification email. The ledgers are append-only so North Shore Red can show what a visitor chose, when they chose it, where they submitted it, and the exact disclosure displayed at that time.

## What is stored

- Submission UUID and UTC timestamp.
- Source URL.
- Form type.
- Selected contact interests.
- Early-vote commitment and selected early-voting method when submitted on the commitment card.
- Separate email and SMS opt-in or declined statuses.
- Email address in the email ledger only when email updates are selected.
- Mobile number in the SMS ledger only when SMS is selected.
- Program identifiers.
- Disclosure versions and exact disclosure text for both channels.

Names, non-opted-in email addresses, optional non-opted-in mobile numbers, and message bodies continue to travel in the notification email but are not copied into the consent database. This limits the database to the information needed for the audit trail and approved communication lists.

## Server setup

1. Create a MySQL database and run `database/schema.mysql.sql` once. For a database created before October 14, 2026, run `database/migrations/2026-10-14-email-and-early-vote.sql` instead.
2. Set `NSR_DB_DSN`, `NSR_DB_USER`, and `NSR_DB_PASSWORD` in the hosting environment. Use `.env.example` as a naming reference; never commit real values.
3. Give the database account only `INSERT` and the minimum `SELECT` access required for administration and export.
4. Confirm `public/config/sms-consent.json` and `public/config/email-consent.json` are present in the built site. The build copies them from the canonical files under `src/data/`.
5. Submit declined and opted-in test entries for both channels, plus one early-vote commitment, then verify all three tables before accepting real submissions.

The handler fails closed when the ledger is unavailable: no form submission is reported as successful unless its consent decision has been stored.

## Disclosure changes

Edit the applicable consent JSON file and change its `version` whenever displayed consent language changes. The browser sends the version it displayed; the server checks that version against its canonical copy and records the canonical exact text, not browser-supplied text.

## Opt-outs and provider events

The future SMS provider webhook must append an `opt_out` event to `sms_consent_events` with the phone number, program ID, provider event time, and provider message/event identifier in `metadata_json`. HELP, re-opt-in, delivery, and error events should follow the same append-only pattern when retained. Sending code must determine current status from the newest consent event and suppress any number whose newest status is `opt_out`.

The future email provider webhook must append unsubscribe and re-subscribe events to `email_consent_events`. Export or sending code must determine current status from the newest event and suppress any address whose newest status is unsubscribed.

## Before SMS launch

- Have counsel and the selected messaging provider approve the disclosure, Privacy Policy, Terms, retention period, and deletion process.
- Restrict database and export access to named administrators and enable encrypted backups.
- Add authenticated, signature-verified provider webhooks for STOP and other carrier events.
- Define retention and deletion schedules, incident response, rate limiting, and an access-review cadence.
- Complete carrier/10DLC registration using the same program description and disclosure shown on the production URL.

This implementation records consent but does not send email campaigns or text messages and does not by itself establish legal, carrier, or provider compliance.
