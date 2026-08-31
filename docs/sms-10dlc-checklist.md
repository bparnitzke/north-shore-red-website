# Future SMS and 10DLC Acceptance Checklist

This checklist is for the future voting-plan/reminder tool. It is not legal advice and must be reviewed by counsel and the messaging provider before production use.

Mandatory future criteria:

- SMS enrollment is optional.
- The SMS checkbox is separate from any required action.
- The checkbox is unchecked by default.
- Declining SMS does not block the voting-plan result or official MyVote link.
- The disclosure appears immediately adjacent to the checkbox.
- Consent is not buried exclusively in the Privacy Policy or Terms page.
- The disclosure identifies North Shore Red as the sender.
- It accurately identifies the messages as voting reminders and election-related informational messages unless the final registered use case is different.
- It states the expected frequency or that frequency varies.
- It states: "Message and data rates may apply."
- It explains that replying STOP ends messages.
- It explains how to obtain help, including HELP and an organization contact method.
- It links directly to the Privacy Policy and Terms and Conditions.
- It explicitly states whether donation solicitations will or will not be sent.
- It does not claim the messages are merely informational if political, campaign, fundraising, or promotional messages will actually be sent.
- No consent control is preselected.
- Consent is not bundled with general terms acceptance.
- The form and policy state that mobile information and SMS opt-in data will not be sold.
- Counsel- and provider-reviewed language addresses whether mobile information, opt-in data, or consent may be shared with third parties, affiliates, and operational service providers.
- Privacy and Terms links are readily visible and directly reachable from both the form and global footer.
- The public production URL visibly demonstrates the complete opt-in flow for 10DLC review.

Operational requirements before launch:

- Approved storage system.
- Approved retention rules.
- Approved access controls.
- Approved deletion process.
- Approved messaging provider.
- Server-side validation.
- Bot protection.
- Rate limiting.
- HTTPS in production.
- Minimal logging that avoids unnecessary personal information.
- No advertising pixels or session-replay tools on personal-information collection pages without explicit review.
