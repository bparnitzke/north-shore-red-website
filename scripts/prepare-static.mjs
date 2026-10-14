import { copyFile, mkdir } from "node:fs/promises";
import { dirname, resolve } from "node:path";

const files = ["sms-consent.json", "email-consent.json"];

for (const file of files) {
  const source = resolve("src/data", file);
  const destination = resolve("public/config", file);
  await mkdir(dirname(destination), { recursive: true });
  await copyFile(source, destination);
}
