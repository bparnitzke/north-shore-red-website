# Social Feed

The homepage uses one unified, static feed rather than three embedded social-media widgets. A scheduled GitHub Action fetches posts from the official APIs, normalizes them into `src/data/social.generated.json`, and commits only when the feed changes. The repository change then triggers the normal site build.

This approach keeps the public page fast, avoids third-party scripts and tracking in visitors' browsers, and lets the organization keep the site current by continuing to manage its existing social channels.

## GitHub configuration

Add these repository secrets:

- `SOCIAL_META_PAGE_ID`: Facebook Page ID.
- `SOCIAL_INSTAGRAM_USER_ID`: Instagram Professional account ID linked to the Page.
- `SOCIAL_META_ACCESS_TOKEN`: long-lived Meta access token with the permissions required to read both accounts.
- `SOCIAL_X_USER_ID`: X user ID for `@northshore_red`.
- `SOCIAL_X_BEARER_TOKEN`: X API bearer token with read access.

Optional repository variable:

- `META_GRAPH_VERSION`: Meta Graph API version, such as `v23.0`. The script has a default, but setting this deliberately makes API upgrades easier to track.

The workflow runs every six hours and can also be started manually from GitHub Actions. If any provider is unavailable, the script preserves the last successful feed. If no credentials are configured yet, the homepage shows direct profile links instead of empty or broken frames.

Instagram must be a Professional account for the supported Meta API path. API permissions, token lifetime, and X API plan access should be confirmed before enabling the workflow.
