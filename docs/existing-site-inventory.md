# Existing Site Inventory

Inspection date: 2026-08-29

Sources inspected:

- `http://northshorered.com/`
- `http://northshorered.com/about.html`
- `http://northshorered.com/contact.php`
- `http://northshorered.com/privacy.html`
- `http://northshorered.com/terms.html`
- `http://northshorered.com/css/style.css`
- `http://northshorered.com/js/script.js`
- `http://northshorered.com/logo.jpg`

Notes:

- The current public site is a small static/PHP site with a shared logo, simple CSS, and a small client-side validation script for the contact form.
- Current top-level navigation includes Home, About Us, Make a Donation, and Contact Us.
- The current donation link points to `https://secure.winred.com/friends-of-north-shore-red/donate`.
- The homepage leads with organization-formation conflict. The redesign intentionally moves that topic away from the homepage and reserves it for a restrained About section.
- The current homepage reports these provisional accomplishments: about 5,000 handwritten postcards, about $14,300 donated to candidates during the 2024-2025 campaign season, 23 campaign events, and a strong/county-leading canvassing effort.
- The existing contact page includes fields for name, address, city, state, ZIP, phone, SMS authorization, email, and comments. The first-phase rebuild does not reproduce this form because the data destination, SMS consent language, storage, and compliance requirements are not approved.
- No verified Facebook or Instagram URLs were found on the inspected site pages. The client later provided Facebook and Instagram profile URLs on 2026-08-29, and those are now stored in `src/data/site.ts`.
- Existing Privacy and Terms pages appear generic and should not be copied into the new site without North Shore Red-specific legal review.
