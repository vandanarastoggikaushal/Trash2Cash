import type { Metadata } from "next";

export const metadata: Metadata = {
  title: "Partners | Trash2Cash NZ",
  description: "Schools, clubs, and businesses—fundraise by aggregating pickups with a group code."
};

export default function PartnersPage() {
  return (
    <div className="container py-10">
      <h1 className="text-3xl font-bold">Partners</h1>
      <div className="prose mt-6">
        <p>
          Fundraiser mode: we provide a group code so households can tag their pickups. We aggregate cans and appliance
          credits and pay out to your group or directly to participating kids’ accounts. Perfect for schools, clubs, and
          local initiatives.
        </p>
      </div>
    </div>
  );
}


