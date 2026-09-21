import { writeFile } from "node:fs/promises";
import { resolve } from "node:path";

const outputPath = resolve("src/data/social.generated.json");
const graphVersion = process.env.META_GRAPH_VERSION || "v23.0";
const maxPosts = 12;

const posts = [];
const failures = [];

const cleanText = (value = "") => value.replace(/\s+/g, " ").trim();

async function getJson(url, options = {}) {
  const response = await fetch(url, options);
  if (!response.ok) throw new Error(`${response.status} ${response.statusText}`);
  return response.json();
}

async function fetchFacebook() {
  const pageId = process.env.SOCIAL_META_PAGE_ID;
  const token = process.env.SOCIAL_META_ACCESS_TOKEN;
  if (!pageId || !token) return;
  const fields = "id,message,created_time,permalink_url,full_picture";
  const url = new URL(`https://graph.facebook.com/${graphVersion}/${pageId}/posts`);
  url.searchParams.set("fields", fields);
  url.searchParams.set("limit", "10");
  url.searchParams.set("access_token", token);
  const data = await getJson(url);
  for (const item of data.data || []) {
    if (!item.message || !item.permalink_url) continue;
    posts.push({
      id: `facebook:${item.id}`,
      platform: "Facebook",
      publishedAt: item.created_time,
      text: cleanText(item.message),
      url: item.permalink_url,
      ...(item.full_picture ? { imageUrl: item.full_picture } : {})
    });
  }
}

async function fetchInstagram() {
  const userId = process.env.SOCIAL_INSTAGRAM_USER_ID;
  const token = process.env.SOCIAL_META_ACCESS_TOKEN;
  if (!userId || !token) return;
  const fields = "id,caption,media_type,media_url,thumbnail_url,permalink,timestamp";
  const url = new URL(`https://graph.facebook.com/${graphVersion}/${userId}/media`);
  url.searchParams.set("fields", fields);
  url.searchParams.set("limit", "10");
  url.searchParams.set("access_token", token);
  const data = await getJson(url);
  for (const item of data.data || []) {
    if (!item.permalink) continue;
    const imageUrl = item.media_type === "VIDEO" ? item.thumbnail_url : item.media_url;
    posts.push({
      id: `instagram:${item.id}`,
      platform: "Instagram",
      publishedAt: item.timestamp,
      text: cleanText(item.caption || "A new post from North Shore Red."),
      url: item.permalink,
      ...(imageUrl ? { imageUrl } : {})
    });
  }
}

async function fetchX() {
  const userId = process.env.SOCIAL_X_USER_ID;
  const token = process.env.SOCIAL_X_BEARER_TOKEN;
  if (!userId || !token) return;
  const url = new URL(`https://api.x.com/2/users/${userId}/tweets`);
  url.searchParams.set("max_results", "10");
  url.searchParams.set("exclude", "retweets,replies");
  url.searchParams.set("tweet.fields", "created_at,attachments");
  url.searchParams.set("expansions", "attachments.media_keys");
  url.searchParams.set("media.fields", "type,url,preview_image_url");
  const data = await getJson(url, { headers: { Authorization: `Bearer ${token}` } });
  const media = new Map((data.includes?.media || []).map((item) => [item.media_key, item]));
  for (const item of data.data || []) {
    const attachment = item.attachments?.media_keys?.map((key) => media.get(key)).find(Boolean);
    const imageUrl = attachment?.url || attachment?.preview_image_url;
    posts.push({
      id: `x:${item.id}`,
      platform: "X",
      publishedAt: item.created_at,
      text: cleanText(item.text),
      url: `https://x.com/northshore_red/status/${item.id}`,
      ...(imageUrl ? { imageUrl } : {})
    });
  }
}

for (const [name, fetcher] of [["Facebook", fetchFacebook], ["Instagram", fetchInstagram], ["X", fetchX]]) {
  try {
    await fetcher();
  } catch (error) {
    failures.push(`${name}: ${error.message}`);
  }
}

if (posts.length === 0) {
  console.log("No social posts fetched; preserving the last known feed.");
  if (failures.length) console.warn(failures.join("\n"));
  process.exit(0);
}

const next = {
  updatedAt: new Date().toISOString(),
  posts: [...new Map(posts.map((post) => [post.id, post])).values()]
    .sort((a, b) => Date.parse(b.publishedAt) - Date.parse(a.publishedAt))
    .slice(0, maxPosts)
};

await writeFile(outputPath, `${JSON.stringify(next, null, 2)}\n`);
if (failures.length) console.warn(`Feed updated with partial results:\n${failures.join("\n")}`);
console.log(`Wrote ${next.posts.length} social posts.`);
