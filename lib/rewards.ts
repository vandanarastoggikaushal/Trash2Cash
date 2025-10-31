import { APPLIANCE_CREDITS, CAN_REWARD_PER_50 } from "@/config/rewards";

export function calculateCansReward(cansPerWeek: number): number {
  const yearly = Math.floor((cansPerWeek * 52) / 50) * CAN_REWARD_PER_50;
  return yearly;
}

export function calculateApplianceReward(items: { slug: string; qty: number }[]): number {
  const creditMap = new Map(APPLIANCE_CREDITS.map((a) => [a.slug, a.credit] as const));
  return items.reduce((sum, i) => sum + (creditMap.get(i.slug) ?? 0) * i.qty, 0);
}

export function projectKiwiSaver(totalPerYear: number, years = 10, rate = 0.05): number {
  // Future value of an annuity-immediate: A * [((1+r)^n - 1) / r]
  if (rate === 0) return totalPerYear * years;
  const fv = totalPerYear * ((Math.pow(1 + rate, years) - 1) / rate);
  return Math.round(fv * 100) / 100;
}


