# Contact and SMS Consent Ledger

The contact form records every SMS decision before it sends the notification email. The ledger is append-only so North Shore Red can show what a visitor chose, when they chose it, where they submitted it, and the exact disclosure displayed at that time.

## What is stored

- Submission UUID and UTC timestamp.
- Source URL.
- Selected contact interests.
- Opt-in or declined status.
- Mobile number only when SMS is selected.
- Program identifier.
- Disclosure version and exact disclosure text.

Names, email addresses, and message bodies continue to travel in the notification email but are not copied into the consent database. This limits the database to the information needed for the audit trail.

## Server setup

1. Create a MySQL database and run `database/schema.mysql.sql` once.
2. Set `NSR_DB_DSN`, `NSR_DB_USER`, and `NSR_DB_PASSWORD` in the hosting environment. Use `.env.example` as a naming reference; never commit real values.
3. Give the database account only `INSERT` and the minimum `SELECT` access required for administration and export.
4. Confirm `public/config/sms-consent.json` is present in the built site. The build copies it from the canonical `src/data/sms-consent.json` file.
5. Submit one declined and one opted-in test entry, then verify both tables before accepting real submissions.

The handler fails closed when the ledger is unavailable: no form submission is reported as successful unless its consent decision has been stored.

## Disclosure changes

Edit `src/data/sms-consent.json` and change its `version` whenever any displayed consent language changes. The browser sends the version it displayed; the server checks that version against its canonical copy and records the canonical exact text, not browser-supplied text.

## Opt-outs and provider events

The future SMS provider webhook must append an `opt_out` event to `sms_consent_events` with the phone number, program ID, provider event time, and provider message/event identifier in `metadata_json`. HELP, re-opt-in, delivery, and error events should follow the same append-only pattern when retained. Sending code must determine current status from the newest consent event and suppress any number whose newest status is `opt_out`.

## Before SMS launch

- Have counsel and the selected messaging provider approve the disclosure, Privacy Policy, Terms, retention period, and deletion process.
- Restrict database and export access to named administrators and enable encrypted backups.
- Add authenticated, signature-verified provider webhooks for STOP and other carrier events.
- Define retention and deletion schedules, incident response, rate limiting, and an access-review cadence.
- Complete carrier/10DLC registration using the same program description and disclosure shown on the production URL.

This implementation records consent but does not send text messages and does not by itself establish legal or carrier compliance.
