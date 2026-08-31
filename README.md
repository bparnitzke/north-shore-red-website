# North Shore Red Website

Initial Astro static-site prototype for North Shore Red, a Wisconsin-based 501(c)(4) conservative grassroots organization.

This first phase builds the maintainable marketing-site foundation only. It does not deploy publicly, collect personal information, submit forms, connect to a database, perform voter lookup, or send SMS messages.

## Tech Stack

- Astro with static output.
- TypeScript.
- Reusable Astro components.
- Plain CSS design tokens in `src/styles/global.css`.
- Editable content in `src/data`.
- Static assets in `public/assets`.
- No database.
- No client-side framework.

## Routes

- `/`
- `/about/`
- `/events/`
- `/volunteer/`
- `/donate/` as "Why Donate To Us"
- `/make-a-plan/`
- `/vote/`
- `/privacy-policy/`
- `/terms-and-conditions/`
- custom `404`

## Setup

Install dependencies:

```bash
pnpm install
```

Start local development:

```bash
pnpm dev
```

Run checks and build:

```bash
pnpm build
```

Preview the static build:

```bash
pnpm preview
```

## Cloudflare Pages

This project is portable static output and can be deployed through a client-owned Cloudflare Pages account later.

Recommended Cloudflare Pages settings:

- Build command: `pnpm build`
- Output directory: `dist`
- Framework preset: Astro
- Node version: use an active LTS supported by Cloudflare Pages
- Environment variables: none for phase one

Do not connect a production domain or publish publicly until content, assets, legal language, donation links, privacy/terms, and future form requirements are approved.

## Editing Content

- Impact numbers: `src/data/impact.ts`
- Header, footer, donation URL, contact, and social profile links: `src/data/site.ts`
- Homepage future work, volunteer areas, and social section content: `src/data/homepage.ts`
- Global design tokens and responsive rules: `src/styles/global.css`

Primary donation CTAs link directly to the external donation platform stored in `src/data/site.ts`. The `/donate/` route remains available as "Why Donate To Us" for supporting copy, disclosures, and future SEO, but it is not a required intermediate step for ready donors.

## Documentation

- Existing site inventory: `docs/existing-site-inventory.md`
- Asset inventory: `docs/asset-inventory.md`
- Content confirmation checklist: `docs/content-confirmation-checklist.md`
- Future voting tool architecture: `docs/future-voting-tool.md`
- Future SMS/10DLC checklist: `docs/sms-10dlc-checklist.md`

## First-Phase Guardrails

- No live form submissions.
- No real personal-information collection.
- No production database.
- No SMS sending.
- No MyVote scraping or impersonation.
- No synthetic documentary photos.
- No public deployment in this phase.
- All provisional figures and legal/compliance language require client, counsel, and provider review as applicable.

