# Social Feed

The homepage uses one unified, static feed rather than three embedded social-media widgets. A scheduled GitHub Action fetches posts from the official APIs, normalizes them into `src/data/social.generated.json`, and commits only when the feed changes. The repository change then triggers the normal site build.

This approach keeps the public page fast, avoids third-party scripts and tracking in visitors' browsers, and lets the organization keep the site current by continuing to manage its existing social channels.

## GitHub configuration

The public account identifiers are recorded in `.github/workflows/sync-social.yml` and `.env.example`:

- `SOCIAL_META_PAGE_ID`: `158689294189935`.
- `SOCIAL_INSTAGRAM_USER_ID`: `17841429608306451`.
- `SOCIAL_X_USER_ID`: `2045973729260298240` for `@northshore_red`.

Add only these credentials as GitHub Actions repository secrets:

- `SOCIAL_META_ACCESS_TOKEN`: long-lived Meta access token with permission to read the Facebook Page and, if the accounts are linked, the Instagram account.
- `SOCIAL_X_BEARER_TOKEN`: X API bearer token with read access.

Optional repository variable:

- `META_GRAPH_VERSION`: Meta Graph API version, such as `v23.0`. The script has a default, but setting this deliberately makes API upgrades easier to track.

The workflow runs every six hours and can also be started manually from GitHub Actions. If any provider is unavailable, the script preserves the last successful feed. If no credentials are configured yet, the homepage shows direct profile links instead of empty or broken frames.

Access tokens are generated through provider developer applications; they are not available in the normal Facebook, Instagram, or X profile settings. Never send tokens by email or commit them to the repository.

Instagram is a Professional account but is not linked to the Facebook Page. The current synchronization script uses Meta's Facebook-linked Instagram API path, so the Instagram portion will remain disabled until either the accounts are linked or the integration is changed to use Instagram Login with a separate `SOCIAL_INSTAGRAM_ACCESS_TOKEN` secret. Facebook and X can be enabled independently. API permissions, token lifetime, and X API plan access should be confirmed before enabling the workflow.
