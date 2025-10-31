import { NextResponse } from "next/server";
import { getDataDir } from "@/lib/data";
import { promises as fs } from "fs";
import path from "path";

export const runtime = 'nodejs';
export const dynamic = 'force-dynamic';

function unauthorized() {
  return new NextResponse("Unauthorized", {
    status: 401,
    headers: { "WWW-Authenticate": "Basic realm=\"Logs\"" }
  });
}

function verifyBasicAuth(authHeader?: string | null) {
  if (!authHeader?.startsWith("Basic ")) return false;
  const b64 = authHeader.slice(6);
  try {
    const [user, pass] = Buffer.from(b64, "base64").toString("utf8").split(":");
    return user === process.env.BASIC_AUTH_USER && pass === process.env.BASIC_AUTH_PASS;
  } catch {
    return false;
  }
}

export async function GET(req: Request) {
  if (!verifyBasicAuth(req.headers.get("authorization"))) {
    return unauthorized();
  }
  const dataDir = getDataDir();
  const leadsPath = path.join(dataDir, "leads.json");
  const msgsPath = path.join(dataDir, "messages.json");

  async function readJson(p: string) {
    try {
      const txt = await fs.readFile(p, "utf8");
      return JSON.parse(txt) as unknown[];
    } catch {
      return [] as unknown[];
    }
  }

  const [leads, messages] = await Promise.all([readJson(leadsPath), readJson(msgsPath)]);

  const html = `<!doctype html>
  <html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Logs</title>
    <style>
      body { font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Inter, Arial; margin: 20px; }
      h1 { font-size: 20px; }
      pre { background:#f8fafc; padding:12px; border-radius:8px; overflow:auto; }
      .grid { display:grid; grid-template-columns: 1fr; gap:20px; }
      @media (min-width: 900px) { .grid { grid-template-columns: 1fr 1fr; } }
    </style>
  </head>
  <body>
    <h1>Trash2Cash NZ – Admin Logs</h1>
    <div class="grid">
      <section>
        <h2>Leads (${leads.length})</h2>
        <pre>${escapeHtml(JSON.stringify(leads.slice(-50), null, 2))}</pre>
      </section>
      <section>
        <h2>Messages (${messages.length})</h2>
        <pre>${escapeHtml(JSON.stringify(messages.slice(-50), null, 2))}</pre>
      </section>
    </div>
  </body>
  </html>`;

  return new NextResponse(html, { headers: { "Content-Type": "text/html; charset=utf-8" } });
}

function escapeHtml(s: string) {
  return s
    .replaceAll("&", "&amp;")
    .replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;")
    .replaceAll('"', "&quot;")
    .replaceAll("'", "&#039;");
}


