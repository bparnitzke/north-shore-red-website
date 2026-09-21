import { copyFile, mkdir } from "node:fs/promises";
import { dirname, resolve } from "node:path";

const source = resolve("src/data/sms-consent.json");
const destination = resolve("public/config/sms-consent.json");

await mkdir(dirname(destination), { recursive: true });
await copyFile(source, destination);
