import { describe, expect, it } from "vitest";
import { calculateCansReward, calculateApplianceReward, projectKiwiSaver } from "@/lib/rewards";

describe("rewards calculator", () => {
  it("calculates cans reward correctly", () => {
    expect(calculateCansReward(0)).toBe(0);
    expect(calculateCansReward(50)).toBe(Math.floor((50 * 52) / 50) * 1);
    expect(calculateCansReward(10)).toBe(Math.floor((10 * 52) / 50) * 1);
  });

  it("calculates appliance reward", () => {
    expect(calculateApplianceReward([{ slug: "microwave", qty: 2 }])).toBe(4);
  });

  it("projects kiwisaver value", () => {
    expect(projectKiwiSaver(100, 10, 0.05)).toBeGreaterThan(1000);
  });
});


