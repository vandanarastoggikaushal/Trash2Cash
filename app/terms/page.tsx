import type { Metadata } from "next";

export const metadata: Metadata = {
  title: "Terms & Conditions | Trash2Cash NZ",
  description: "Plain-English terms for pickups, payments, and service areas."
};

export default function TermsPage() {
  return (
    <div className="container prose py-10">
      <h1>Terms & Conditions</h1>
      <p>By scheduling a pickup you confirm items are clean and safe to handle. Appliance credits are indicative and may vary based on condition and materials.</p>
      <p>Payments are made after items are collected and verified. KiwiSaver deposits require correct provider/member details and consent.</p>
      <p>Service areas and turnaround times may change. We may decline unsafe items.</p>
    </div>
  );
}


