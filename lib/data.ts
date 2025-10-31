import path from "path";
import { promises as fs } from "fs";

export function getDataDir() {
  // Allow overriding the data directory location for hosts with specific writable paths
  // e.g., set DATA_DIR=/home/user/apps/trash2cash/data in Hostinger.
  const configured = process.env.DATA_DIR;
  return configured && configured.trim().length > 0
    ? configured
    : path.join(process.cwd(), "data");
}

export async function appendJsonRecord<T = unknown>(fileName: string, record: T) {
  const dataDir = getDataDir();
  const filePath = path.join(dataDir, fileName);
  await fs.mkdir(dataDir, { recursive: true });
  let existing: unknown[] = [];
  try {
    const raw = await fs.readFile(filePath, "utf-8");
    existing = JSON.parse(raw);
    if (!Array.isArray(existing)) existing = [];
  } catch {}
  existing.push(record as unknown as object);
  await fs.writeFile(filePath, JSON.stringify(existing, null, 2));
}


