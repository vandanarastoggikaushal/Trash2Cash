import { HandCoins, Truck, PiggyBank } from "lucide-react";

const features = [
  {
    icon: Truck,
    title: "Door-to-door pickup",
    desc: "Across Wellington & suburbs"
  },
  {
    icon: HandCoins,
    title: "$1 per 50 cans",
    desc: "Simple and transparent"
  },
  {
    icon: PiggyBank,
    title: "Kids & KiwiSaver",
    desc: "Grow value over time"
  }
];

export function FeatureCards() {
  return (
    <section className="container grid gap-6 py-12 sm:grid-cols-2 lg:grid-cols-3">
      {features.map((f) => (
        <div key={f.title} className="rounded-xl border bg-white p-6 shadow-sm">
          <f.icon className="text-brand" />
          <h3 className="mt-3 text-lg font-semibold">{f.title}</h3>
          <p className="mt-1 text-sm text-slate-600">{f.desc}</p>
        </div>
      ))}
    </section>
  );
}


