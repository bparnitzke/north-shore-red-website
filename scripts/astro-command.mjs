import { spawnSync } from "node:child_process";

process.env.ASTRO_TELEMETRY_DISABLED ||= "1";

const args = process.argv.slice(2);
const executable = process.platform === "win32" ? "astro.cmd" : "astro";
const result = spawnSync(executable, args, {
  env: process.env,
  shell: true,
  stdio: "inherit"
});

process.exit(result.status ?? 1);
