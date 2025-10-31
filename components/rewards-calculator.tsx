"use client";
import { useMemo, useState } from "react";
import { APPLIANCE_CREDITS } from "@/config/rewards";
import { calculateApplianceReward, calculateCansReward, projectKiwiSaver } from "@/lib/rewards";

export function RewardsCalculator() {
  const [cansPerWeek, setCansPerWeek] = useState(10);
  const [appliances, setAppliances] = useState<Record<string, number>>({});
  const [showKiwiSaver, setShowKiwiSaver] = useState(true);

  const total = useMemo(() => {
    const cans = calculateCansReward(cansPerWeek);
    const appl = calculateApplianceReward(
      Object.entries(appliances)
        .filter(([, qty]) => qty > 0)
        .map(([slug, qty]) => ({ slug, qty }))
    );
    return { cans, appl, total: cans + appl };
  }, [cansPerWeek, appliances]);

  const kiwiSaver = useMemo(() => (showKiwiSaver ? projectKiwiSaver(total.total, 10, 0.05) : 0), [total, showKiwiSaver]);

  return (
    <section className="container my-12">
      <div className="rounded-xl border bg-white p-6 shadow-sm">
        <h2 className="text-xl font-semibold">Rewards Calculator</h2>
        <div className="mt-6 grid gap-6 lg:grid-cols-2">
          <div className="space-y-6">
            <div>
              <label htmlFor="cans" className="block text-sm font-medium">Cans per week: {cansPerWeek}</label>
              <input
                id="cans"
                type="range"
                min={0}
                max={100}
                value={cansPerWeek}
                onChange={(e) => setCansPerWeek(parseInt(e.target.value))}
                className="mt-2 w-full"
              />
            </div>
            <div>
              <label className="block text-sm font-medium">Appliances per year</label>
              <div className="mt-2 grid gap-3 sm:grid-cols-2">
                {APPLIANCE_CREDITS.map((a) => (
                  <div key={a.slug} className="flex items-center justify-between rounded-lg border p-3">
                    <span className="text-sm">{a.label}</span>
                    <input
                      type="number"
                      min={0}
                      className="w-20 rounded-md border px-2 py-1"
                      value={appliances[a.slug] ?? 0}
                      onChange={(e) => setAppliances((s) => ({ ...s, [a.slug]: parseInt(e.target.value || "0") }))}
                      aria-label={`${a.label} quantity`}
                    />
                  </div>
                ))}
              </div>
            </div>
          </div>
          <div className="rounded-lg bg-slate-50 p-6">
            <div className="space-y-2 text-sm">
              <div className="flex justify-between"><span>Cans reward</span><span className="font-semibold">${'{'}total.cans{'}'}</span></div>
              <div className="flex justify-between"><span>Appliance credits</span><span className="font-semibold">${'{'}total.appl{'}'}</span></div>
            </div>
            <div className="mt-4 flex items-center justify-between border-t pt-4 text-lg font-semibold">
              <span>Estimated yearly earnings</span>
              <span>${'{'}total.total{'}'}</span>
            </div>
            <div className="mt-6 flex items-center justify-between text-sm">
              <label className="flex items-center gap-2">
                <input type="checkbox" checked={showKiwiSaver} onChange={(e) => setShowKiwiSaver(e.target.checked)} />
                KiwiSaver growth preview (5%, 10yrs)
              </label>
              {showKiwiSaver && <span className="font-semibold">${'{'}kiwiSaver{'}'}</span>}
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}


