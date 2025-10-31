import { NextResponse } from "next/server";

export const dynamic = "force-static";

export async function GET() {
  const base = "https://trash2cash.nz";
  const urls = ["/", "/how-it-works", "/rewards", "/schedule-pickup", "/partners", "/faq", "/contact", "/privacy", "/terms"];
  const xml = `<?xml version="1.0" encoding="UTF-8"?>\n<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n${urls
    .map((u) => `<url><loc>${base}${u}</loc></url>`) 
    .join("\n")}\n</urlset>`;
  return new NextResponse(xml, { headers: { "Content-Type": "application/xml" } });
}


