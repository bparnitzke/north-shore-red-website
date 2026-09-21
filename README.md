# North Shore Red Website

Astro static site for North Shore Red, a Wisconsin-based 501(c)(4) conservative grassroots
organization. Built to "The Civic Ledger" design system from the Claude Design handoff
(persistent left rail on desktop, numbered ruled sections, permanent Donate).

## Tech Stack

- Astro with static output.
- TypeScript.
- Reusable Astro components, page-scoped CSS for bespoke section layouts.
- Design tokens and shared chrome (rail, mobile menu, buttons, forms) in `src/styles/global.css`.
- Editable content in `src/data`.
- Static assets in `public/assets`, client photo in `public/uploads`.
- `public/contact.php`: the contact form's submission and consent-ledger handler (see below).
- Scheduled static social-feed refreshes through GitHub Actions.

## Routes

- `/` — homepage
- `/about/` — About us (also the destination for "Read the full story")
- `/donate/` — Why donate to us
- `/contact/` — the site's single conversion funnel (volunteer, mailing list, fundraiser
  invitation list, or a general message); supports `?interest=volunteer` and `?interest=event`
  to pre-check the matching box
- `/privacy-policy/`, `/terms-and-conditions/` — legal copy carried verbatim from the live site
- `/make-a-plan/`, `/vote/` — reserved for the future voting-plan tool (see
  `docs/future-voting-tool.md`)
- `/events/`, `/volunteer/` — thin redirects (`/#next-event`, `/contact/?interest=volunteer`);
  there is no standalone events or volunteer page by design

## Setup

```bash
pnpm install
pnpm dev       # local development
pnpm build     # type-check + build to dist/
pnpm preview   # preview the static build
```

## Contact form

The form posts to `/contact.php`, which Astro copies into `dist/` unchanged. On cPanel, upload
the built `dist/` contents to `public_html`. PHP records the consent decision in MySQL before
using `mail()` for delivery. It:

- Emails both `brian@orbiterstrategies.com` and `NorthShoreRedFund@gmail.com`, with `Reply-To`
  set to the submitter and the checked interests in the subject/body.
- Rejects the submission if the honeypot field is filled or if it arrives less than ~2 seconds
  after the form rendered (both silent to the caller, so bots don't learn why they failed).
- Validates name, email, and comments server-side in addition to the client-side checks.
- Records an append-only SMS decision with a UTC timestamp, source URL, program ID, selected
  interests, disclosure version, and the exact disclosure text shown. A mobile number is stored
  only when SMS is selected.
- Redirects a plain `GET` (e.g., an old bookmark to the previous site's `contact.php`) to
  `/contact/` instead of returning an empty API response.

Run `database/schema.mysql.sql` once and configure `NSR_DB_DSN`, `NSR_DB_USER`, and
`NSR_DB_PASSWORD` on the host. The form fails closed if the ledger cannot be written. See
`docs/contact-consent-ledger.md` for setup, testing, and future opt-out handling. The site records
consent but does not send SMS messages.

## Social feed

The homepage reads a normalized static feed from `src/data/social.generated.json`. GitHub Actions
runs `scripts/sync-social.mjs` every six hours and commits new posts when provider credentials are
configured. Visitors receive ordinary static HTML instead of third-party feed scripts. See
`docs/social-feed.md` for required Meta and X repository secrets.

## Editing content

- Site-wide nav, social links, donation/MyVote/voting-plan URLs: `src/data/site.ts`
- Homepage copy (letter, record figures, event, follow-along): `src/data/homepage.ts`,
  `src/data/record.ts`
- About page copy: `src/data/about.ts`
- Why donate page copy: `src/data/whydonate.ts`
- Contact form interest options and success-state copy: `src/data/contact.ts`
- Privacy/Terms clauses: `src/data/legal.ts`
- SMS disclosure text and version: `src/data/sms-consent.json`
- Design tokens (colors, type scale, spacing) and shared chrome: `src/styles/global.css`

## Still needed from the client

See `docs/content-confirmation-checklist.md`, plus, per the design handoff:

1. The voting-plan tool URL (`site.votingPlanUrl` in `src/data/site.ts` — currently `null`,
   so the nav and CTAs fall back to the internal `/make-a-plan/` page until it's set).
2. Garden Party venue and program details.
3. Photography for remaining content needs.
4. Internal verification of figures in `src/data/record.ts` and `src/data/whydonate.ts`.
5. Final fundraising, Privacy, Terms, and SMS language reviewed by counsel and the messaging provider.
6. Meta and X API credentials for the scheduled social feed.
7. A social share image and a favicon beyond the placeholder `public/favicon.svg`.

## Deployment (cPanel)

- `pnpm build` produces static output in `dist/`; upload its contents to `public_html`.
  `public/contact.php` ships through unchanged.
- cPanel's Git Version Control can clone this repo on the server so a deploy is a
  `git pull` + `pnpm build` rather than a manual file upload.
- Use cPanel's Directory Privacy (htpasswd) for a password-protected staging URL, and set
  `noindex` on any non-production page (see `BaseLayout`'s `noindex` prop).

## Documentation

- Existing site inventory: `docs/existing-site-inventory.md`
- Asset inventory: `docs/asset-inventory.md`
- Content confirmation checklist: `docs/content-confirmation-checklist.md`
- Future voting tool architecture: `docs/future-voting-tool.md`
- Future SMS/10DLC checklist: `docs/sms-10dlc-checklist.md`
- Contact consent ledger: `docs/contact-consent-ledger.md`
- Social feed setup: `docs/social-feed.md`
