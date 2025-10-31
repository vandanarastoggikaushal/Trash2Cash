import type { Metadata } from "next";
import { COMPANY_NAME, CITY } from "@/config/rewards";

export const metadata: Metadata = {
  title: "Privacy Policy | Trash2Cash NZ",
  description: "Plain-English privacy policy for New Zealand context."
};

export default function PrivacyPage() {
  return (
    <div className="container prose py-10">
      <h1>Privacy Policy</h1>
      <p>We collect only what we need to schedule pickups and pay you. This includes your contact details, address, item estimates, and payout preferences.</p>
      <p>We store this data securely in New Zealand. We do not sell your data. You can request a copy or deletion by contacting us.</p>
      <p>For KiwiSaver or child payouts, we verify identity before transferring. We keep records as required by NZ law.</p>
      <p>Contact: {COMPANY_NAME}, {CITY}, New Zealand.</p>
    </div>
  );
}


