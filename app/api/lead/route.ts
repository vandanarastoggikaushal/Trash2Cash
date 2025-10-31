import { NextResponse } from "next/server";
import { nanoid } from "nanoid";
import { Lead } from "@/types/lead";
import { LeadSchema } from "@/lib/validation";
import { sendLeadEmail } from "@/lib/email";
import { appendJsonRecord } from "@/lib/data";

export async function POST(req: Request) {
  try {
    const incoming = await req.json();
    const id = nanoid();
    const createdAt = new Date().toISOString();
    const lead: Lead = { ...incoming, id, createdAt };

    const parsed = LeadSchema.safeParse(lead);
    if (!parsed.success) {
      return NextResponse.json({ ok: false, error: parsed.error.flatten() }, { status: 400 });
    }

    await appendJsonRecord("leads.json", lead);

    await sendLeadEmail(lead);
    return NextResponse.json({ ok: true, id });
  } catch (e) {
    return NextResponse.json({ ok: false, error: "Invalid request" }, { status: 400 });
  }
}


