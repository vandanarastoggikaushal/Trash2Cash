import { Hero } from "@/components/hero";
import { FeatureCards } from "@/components/feature-cards";
import { RewardsCalculator } from "@/components/rewards-calculator";

export const dynamic = "force-static";

export default function HomePage() {
  return (
    <div>
      <Hero />
      <FeatureCards />
      <section className="container">
        <h2 className="text-xl font-semibold">What we collect</h2>
        <div className="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
          {["Aluminium cans", "Washing machines", "Microwaves", "PC cases", "Laptops", "Dishwashers"].map((i) => (
            <div key={i} className="rounded-lg border bg-white p-4 text-sm">{i}</div>
          ))}
        </div>
      </section>
      <RewardsCalculator />
      <section className="container my-12">
        <div className="flex flex-col items-center justify-between gap-4 rounded-xl bg-emerald-600 px-6 py-10 text-white sm:flex-row">
          <div>
            <h3 className="text-xl font-semibold">Ready to turn trash into cash?</h3>
            <p className="text-emerald-100">Door-to-door pickups across Wellington & suburbs.</p>
          </div>
          <a href="/schedule-pickup" className="btn bg-white text-emerald-700 hover:bg-slate-100">Schedule a Pickup</a>
        </div>
      </section>
    </div>
  );
}


