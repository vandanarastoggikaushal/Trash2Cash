import type { Metadata } from "next";
import { SERVICE_AREAS } from "@/config/rewards";

export const metadata: Metadata = {
  title: "How It Works | Trash2Cash NZ",
  description: "Register, prepare recyclables, schedule pickup, and get paid or deposit to KiwiSaver."
};

export default function HowItWorksPage() {
  return (
    <div className="container py-10">
      <h1 className="text-3xl font-bold">How it works</h1>
      <ol className="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        {["Register", "Prepare recyclables", "Schedule pickup", "Get paid / KiwiSaver"].map((s, i) => (
          <li key={s} className="rounded-lg border bg-white p-4">
            <div className="text-sm text-slate-500">Step {i + 1}</div>
            <div className="font-semibold">{s}</div>
          </li>
        ))}
      </ol>
      <div className="prose mt-8">
        <p>Rinse cans quickly (crushing optional). Keep appliances safe to move. Typical turnaround is a few days depending on suburb.</p>
        <p>Current service areas: {SERVICE_AREAS.join(", ")}</p>
      </div>
    </div>
  );
}


