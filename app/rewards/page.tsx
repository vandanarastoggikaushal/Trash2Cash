import type { Metadata } from "next";
import { APPLIANCE_CREDITS } from "@/config/rewards";

export const metadata: Metadata = {
  title: "Rewards | Trash2Cash NZ",
  description: "Earn $1 per 50 aluminium cans plus appliance pickup credits."
};

export default function RewardsPage() {
  return (
    <div className="container py-10">
      <h1 className="text-3xl font-bold">Rewards</h1>
      <div className="prose mt-6">
        <h2>$1 per 50 cans</h2>
        <ul>
          <li>50 cans → $1</li>
          <li>250 cans → $5</li>
          <li>1,000 cans → $20</li>
        </ul>
        <p>Average NZ household ≈ $500/year in recyclable value. Kids’ accounts & KiwiSaver deposits available with consent and ID check at payout time.</p>
      </div>
      <div className="mt-8">
        <h2 className="text-xl font-semibold">Appliance pickup credits</h2>
        <div className="mt-3 overflow-hidden rounded-lg border">
          <table className="w-full text-left text-sm">
            <thead className="bg-slate-50">
              <tr>
                <th className="p-3">Appliance</th>
                <th className="p-3">Credit ($)</th>
              </tr>
            </thead>
            <tbody>
              {APPLIANCE_CREDITS.map((a) => (
                <tr key={a.slug} className="border-t">
                  <td className="p-3">{a.label}</td>
                  <td className="p-3">{a.credit}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}


