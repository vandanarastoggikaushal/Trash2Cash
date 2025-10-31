import { Lead } from "@/types/lead";

export async function sendLeadEmail(lead: Lead) {
  // Stub: log to console to simulate sending email
  // In production, integrate with a provider like Postmark/SES.
  console.log("[email] New lead submitted:", JSON.stringify(lead, null, 2));
}

export async function sendContactEmail(message: {
  id: string;
  name: string;
  email: string;
  message: string;
  createdAt: string;
}) {
  console.log("[email] New contact message:", JSON.stringify(message, null, 2));
}


