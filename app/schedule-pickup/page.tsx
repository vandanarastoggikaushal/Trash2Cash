"use client";
import { useState } from "react";
import type { Metadata } from "next";
import { APPLIANCE_CREDITS, CITY } from "@/config/rewards";
import { Lead } from "@/types/lead";
import { nzPhoneRegex, nzPostcodeRegex } from "@/lib/validation";
import { toast } from "sonner";

export const metadata: Metadata = {
  title: "Schedule Pickup | Trash2Cash NZ",
  description: "Request a pickup for aluminium cans and appliances in Wellington."
};

type ApplianceState = { slug: string; qty: number; notes?: string };

export default function SchedulePickupPage() {
  const [submittedId, setSubmittedId] = useState<string | null>(null);
  const [loading, setLoading] = useState(false);
  const [form, setForm] = useState<Partial<Lead>>({
    person: { fullName: "", email: "", phone: "", marketingOptIn: false },
    address: { street: "", suburb: "", city: CITY, postcode: "", accessNotes: "" },
    pickup: { type: "cans", cansEstimate: 0, appliances: [], preferredDate: "", preferredWindow: undefined },
    payout: { method: "bank" },
    confirm: { itemsAreClean: false, acceptedTerms: false }
  });

  function update<K extends keyof Lead>(section: K, value: Lead[K] | any) {
    setForm((f) => ({ ...f, [section]: value }));
  }

  async function submit(e: React.FormEvent) {
    e.preventDefault();
    if (!form.person || !form.address || !form.pickup || !form.payout || !form.confirm) return;
    if (!form.person.fullName || !form.person.email || !form.person.phone) return toast.error("Please fill in your name, email, and phone.");
    if (!nzPhoneRegex.test(form.person.phone)) return toast.error("Please enter a valid NZ phone number.");
    if (!form.address.street || !form.address.suburb || !form.address.city || !form.address.postcode) return toast.error("Please complete your address.");
    if (!nzPostcodeRegex.test(form.address.postcode)) return toast.error("Please enter a valid 4-digit postcode.");
    if (!form.confirm.itemsAreClean || !form.confirm.acceptedTerms) return toast.error("Please confirm items are clean and accept terms.");

    setLoading(true);
    const res = await fetch("/app/api/lead.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams({ payload: JSON.stringify(form) })
    });
    setLoading(false);
    if (res.ok) {
      const data = await res.json();
      setSubmittedId(data.id);
      toast.success("Pickup scheduled! Reference ID generated.");
    } else {
      toast.error("There was a problem submitting your request.");
    }
  }

  if (submittedId) {
    return (
      <div className="container py-10">
        <h1 className="text-3xl font-bold">Thanks! Your request is in.</h1>
        <p className="mt-2 text-slate-700">Reference ID: <span className="font-mono font-semibold">{submittedId}</span></p>
        <p className="mt-4 text-sm text-slate-600">What happens next: we’ll confirm a pickup window by email or SMS and handle the rest.</p>
      </div>
    );
  }

  const applianceDefs = APPLIANCE_CREDITS;
  const appliances = (form.pickup?.appliances ?? []) as ApplianceState[];

  return (
    <div className="container py-10">
      <h1 className="text-3xl font-bold">Schedule a Pickup</h1>
      <form className="mt-6 grid gap-8 lg:grid-cols-2" onSubmit={submit}>
        <div className="space-y-6">
          <fieldset className="rounded-lg border p-4">
            <legend className="px-1 text-sm font-semibold">Person</legend>
            <div className="grid gap-4 sm:grid-cols-2">
              <div>
                <label className="block text-sm font-medium" htmlFor="fullName">Full name</label>
                <input id="fullName" className="mt-1 w-full rounded-md border px-3 py-2" required value={form.person?.fullName ?? ""} onChange={(e) => update("person", { ...form.person!, fullName: e.target.value })} />
              </div>
              <div>
                <label className="block text-sm font-medium" htmlFor="email">Email</label>
                <input id="email" type="email" className="mt-1 w-full rounded-md border px-3 py-2" required value={form.person?.email ?? ""} onChange={(e) => update("person", { ...form.person!, email: e.target.value })} />
              </div>
              <div>
                <label className="block text-sm font-medium" htmlFor="phone">Phone</label>
                <input id="phone" className="mt-1 w-full rounded-md border px-3 py-2" required value={form.person?.phone ?? ""} onChange={(e) => update("person", { ...form.person!, phone: e.target.value })} placeholder="e.g. 0212345678" />
              </div>
              <label className="mt-6 inline-flex items-center gap-2 text-sm"><input type="checkbox" checked={form.person?.marketingOptIn ?? false} onChange={(e) => update("person", { ...form.person!, marketingOptIn: e.target.checked })} /> I’d like to receive updates</label>
            </div>
          </fieldset>

          <fieldset className="rounded-lg border p-4">
            <legend className="px-1 text-sm font-semibold">Address</legend>
            <div className="grid gap-4 sm:grid-cols-2">
              <div className="sm:col-span-2">
                <label className="block text-sm font-medium" htmlFor="street">Street</label>
                <input id="street" className="mt-1 w-full rounded-md border px-3 py-2" required value={form.address?.street ?? ""} onChange={(e) => update("address", { ...form.address!, street: e.target.value })} />
              </div>
              <div>
                <label className="block text-sm font-medium" htmlFor="suburb">Suburb</label>
                <input id="suburb" className="mt-1 w-full rounded-md border px-3 py-2" required value={form.address?.suburb ?? ""} onChange={(e) => update("address", { ...form.address!, suburb: e.target.value })} />
              </div>
              <div>
                <label className="block text-sm font-medium" htmlFor="city">City</label>
                <input id="city" className="mt-1 w-full rounded-md border px-3 py-2" required value={form.address?.city ?? ""} onChange={(e) => update("address", { ...form.address!, city: e.target.value })} />
              </div>
              <div>
                <label className="block text-sm font-medium" htmlFor="postcode">Postcode</label>
                <input id="postcode" className="mt-1 w-full rounded-md border px-3 py-2" required value={form.address?.postcode ?? ""} onChange={(e) => update("address", { ...form.address!, postcode: e.target.value })} />
              </div>
              <div className="sm:col-span-2">
                <label className="block text-sm font-medium" htmlFor="access">Access notes</label>
                <input id="access" className="mt-1 w-full rounded-md border px-3 py-2" value={form.address?.accessNotes ?? ""} onChange={(e) => update("address", { ...form.address!, accessNotes: e.target.value })} />
              </div>
            </div>
          </fieldset>

          <fieldset className="rounded-lg border p-4">
            <legend className="px-1 text-sm font-semibold">Pickup</legend>
            <div className="space-y-4">
              <div className="flex gap-4 text-sm">
                {(["cans", "appliances", "both"] as const).map((t) => (
                  <label key={t} className={`inline-flex items-center gap-2 rounded-md border px-3 py-2 ${form.pickup?.type === t ? "border-brand" : ""}`}>
                    <input type="radio" name="pickup-type" checked={form.pickup?.type === t} onChange={() => update("pickup", { ...form.pickup!, type: t })} /> {t}
                  </label>
                ))}
              </div>
              <div className="grid gap-4 sm:grid-cols-2">
                <div>
                  <label className="block text-sm font-medium" htmlFor="cansEstimate">Cans estimate</label>
                  <input id="cansEstimate" type="number" min={0} className="mt-1 w-full rounded-md border px-3 py-2" value={form.pickup?.cansEstimate ?? 0} onChange={(e) => update("pickup", { ...form.pickup!, cansEstimate: parseInt(e.target.value || "0") })} />
                </div>
                <div>
                  <label className="block text-sm font-medium">Appliances</label>
                  <div className="mt-2 grid gap-2">
                    {applianceDefs.map((a) => (
                      <div key={a.slug} className="flex items-center justify-between gap-3">
                        <span className="text-sm">{a.label}</span>
                        <input type="number" min={0} className="w-24 rounded-md border px-2 py-1" value={appliances.find((x) => x.slug === a.slug)?.qty ?? 0} onChange={(e) => {
                          const qty = parseInt(e.target.value || "0");
                          const next = [...appliances.filter((x) => x.slug !== a.slug), ...(qty > 0 ? [{ slug: a.slug, qty }] : [])];
                          update("pickup", { ...form.pickup!, appliances: next });
                        }} />
                      </div>
                    ))}
                  </div>
                </div>
                <div>
                  <label className="block text-sm font-medium" htmlFor="date">Preferred date</label>
                  <input id="date" type="date" className="mt-1 w-full rounded-md border px-3 py-2" value={form.pickup?.preferredDate ?? ""} onChange={(e) => update("pickup", { ...form.pickup!, preferredDate: e.target.value })} />
                </div>
                <div>
                  <label className="block text-sm font-medium" htmlFor="window">Time window</label>
                  <select id="window" className="mt-1 w-full rounded-md border px-3 py-2" value={form.pickup?.preferredWindow ?? ""} onChange={(e) => update("pickup", { ...form.pickup!, preferredWindow: e.target.value as any })}>
                    <option value="">Select</option>
                    <option>Morning</option>
                    <option>Afternoon</option>
                    <option>Evening</option>
                  </select>
                </div>
              </div>
            </div>
          </fieldset>

          <fieldset className="rounded-lg border p-4">
            <legend className="px-1 text-sm font-semibold">Payout preference</legend>
            <div className="space-y-3 text-sm">
              {(["bank", "child_account", "kiwisaver"] as const).map((m) => (
                <label key={m} className="flex items-center gap-2">
                  <input type="radio" name="payout" checked={form.payout?.method === m} onChange={() => update("payout", { method: m })} /> {m}
                </label>
              ))}
              {form.payout?.method === "bank" && (
                <div className="grid gap-3 sm:grid-cols-2">
                  <input placeholder="Bank name" className="rounded-md border px-3 py-2" value={form.payout.bank?.name ?? ""} onChange={(e) => update("payout", { ...form.payout!, bank: { ...(form.payout.bank ?? { name: "", accountNumber: "" }), name: e.target.value } })} />
                  <input placeholder="Account number" className="rounded-md border px-3 py-2" value={form.payout.bank?.accountNumber ?? ""} onChange={(e) => update("payout", { ...form.payout!, bank: { ...(form.payout.bank ?? { name: "", accountNumber: "" }), accountNumber: e.target.value } })} />
                </div>
              )}
              {form.payout?.method === "child_account" && (
                <div className="grid gap-3 sm:grid-cols-2">
                  <input placeholder="Child name" className="rounded-md border px-3 py-2" value={form.payout.child?.childName ?? ""} onChange={(e) => update("payout", { ...form.payout!, child: { ...(form.payout.child ?? { childName: "" }), childName: e.target.value } })} />
                  <input placeholder="Optional bank account" className="rounded-md border px-3 py-2" value={form.payout.child?.bankAccount ?? ""} onChange={(e) => update("payout", { ...form.payout!, child: { ...(form.payout.child ?? { childName: "" }), bankAccount: e.target.value } })} />
                </div>
              )}
              {form.payout?.method === "kiwisaver" && (
                <div className="grid gap-3 sm:grid-cols-2">
                  <input placeholder="Provider" className="rounded-md border px-3 py-2" value={form.payout.kiwiSaver?.provider ?? ""} onChange={(e) => update("payout", { ...form.payout!, kiwiSaver: { ...(form.payout.kiwiSaver ?? { provider: "", memberId: "" }), provider: e.target.value } })} />
                  <input placeholder="Member ID" className="rounded-md border px-3 py-2" value={form.payout.kiwiSaver?.memberId ?? ""} onChange={(e) => update("payout", { ...form.payout!, kiwiSaver: { ...(form.payout.kiwiSaver ?? { provider: "", memberId: "" }), memberId: e.target.value } })} />
                </div>
              )}
            </div>
          </fieldset>

          <div className="space-y-2 text-sm">
            <label className="flex items-center gap-2"><input type="checkbox" checked={form.confirm?.itemsAreClean ?? false} onChange={(e) => update("confirm", { ...form.confirm!, itemsAreClean: e.target.checked })} /> Items are clean and safe to handle.</label>
            <label className="flex items-center gap-2"><input type="checkbox" checked={form.confirm?.acceptedTerms ?? false} onChange={(e) => update("confirm", { ...form.confirm!, acceptedTerms: e.target.checked })} /> I accept the terms.</label>
          </div>
          <button className="btn" disabled={loading} type="submit">{loading ? "Submitting..." : "Submit request"}</button>
        </div>
        <aside className="space-y-4">
          <div className="rounded-lg border bg-white p-4">
            <h2 className="font-semibold">Why Trash2Cash?</h2>
            <ul className="mt-2 list-disc pl-5 text-sm text-slate-700">
              <li>$1 per 50 aluminium cans—simple and transparent</li>
              <li>Kids’ accounts & KiwiSaver options</li>
              <li>We handle the heavy lifting</li>
            </ul>
          </div>
        </aside>
      </form>
    </div>
  );
}


