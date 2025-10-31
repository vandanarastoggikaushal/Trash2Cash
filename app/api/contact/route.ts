import { NextResponse } from "next/server";
import { nanoid } from "nanoid";
import { sendContactEmail } from "@/lib/email";
import { appendJsonRecord } from "@/lib/data";

export const runtime = 'nodejs';
export const dynamic = 'force-dynamic';

export async function POST(req: Request) {
  try {
    const body = await req.json();
    const id = nanoid();
    const createdAt = new Date().toISOString();
    const record = { id, createdAt, name: body.name, email: body.email, message: body.message };

    await appendJsonRecord("messages.json", record);

    await sendContactEmail(record);
    return NextResponse.json({ ok: true, id });
  } catch (e) {
    return NextResponse.json({ ok: false, error: "Invalid request" }, { status: 400 });
  }
}


