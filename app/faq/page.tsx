import type { Metadata } from "next";

export const metadata: Metadata = {
  title: "FAQ | Trash2Cash NZ",
  description: "Common questions about what we accept, payments, KiwiSaver, hygiene, and more."
};

const faqs: { q: string; a: string }[] = [
  { q: "What do you collect?", a: "Clean aluminium cans and common household metal appliances." },
  { q: "How do payments work?", a: "We tally your items, then pay out or transfer as chosen." },
  { q: "Kids & KiwiSaver?", a: "Name a child beneficiary or provide KiwiSaver provider/member ID; we transfer after verification." },
  { q: "Do I need to crush cans?", a: "Optional; please give a quick rinse." },
  { q: "Which suburbs?", a: "Current service areas across Wellington region; more coming soon." },
  { q: "Appliance condition?", a: "Must be safe to move; we handle recycling." },
  { q: "Turnaround time?", a: "Usually a few days depending on suburb and volume." },
  { q: "Cancelled pickups?", a: "Let us know ASAP—no worries, we’ll reschedule." },
  { q: "Hygiene?", a: "Please rinse cans to keep collections clean and safe." },
  { q: "Heavy items?", a: "We handle the heavy lifting—just ensure clear access." },
  { q: "Data privacy?", a: "We store minimal details securely and never sell your data." },
  { q: "Receipts?", a: "You’ll receive a reference ID after scheduling and confirmation after pickup." }
];

export default function FAQPage() {
  return (
    <div className="container py-10">
      <h1 className="text-3xl font-bold">Frequently Asked Questions</h1>
      <div className="mt-6 divide-y rounded-lg border bg-white">
        {faqs.map((f) => (
          <div key={f.q} className="p-4">
            <h3 className="font-semibold">{f.q}</h3>
            <p className="mt-1 text-sm text-slate-700">{f.a}</p>
          </div>
        ))}
      </div>
    </div>
  );
}


