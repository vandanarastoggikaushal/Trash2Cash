"use client";
import { useState } from "react";
import type { Metadata } from "next";
import { toast } from "sonner";

export const metadata: Metadata = {
  title: "Contact | Trash2Cash NZ",
  description: "Get in touch with our team in Wellington."
};

export default function ContactPage() {
  const [form, setForm] = useState({ name: "", email: "", message: "" });
  const [loading, setLoading] = useState(false);

  async function submit(e: React.FormEvent) {
    e.preventDefault();
    setLoading(true);
    const res = await fetch("/api/contact.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: new URLSearchParams({ payload: JSON.stringify(form) })
    });
    setLoading(false);
    if (res.ok) {
      toast.success("Message sent successfully");
      setForm({ name: "", email: "", message: "" });
    } else {
      toast.error("Something went wrong");
    }
  }

  return (
    <div className="container py-10">
      <h1 className="text-3xl font-bold">Contact</h1>
      <form className="mt-6 max-w-xl space-y-4" onSubmit={submit}>
        <div>
          <label className="block text-sm font-medium" htmlFor="name">Name</label>
          <input id="name" className="mt-1 w-full rounded-md border px-3 py-2" required value={form.name} onChange={(e) => setForm({ ...form, name: e.target.value })} />
        </div>
        <div>
          <label className="block text-sm font-medium" htmlFor="email">Email</label>
          <input id="email" type="email" className="mt-1 w-full rounded-md border px-3 py-2" required value={form.email} onChange={(e) => setForm({ ...form, email: e.target.value })} />
        </div>
        <div>
          <label className="block text-sm font-medium" htmlFor="message">Message</label>
          <textarea id="message" className="mt-1 h-32 w-full rounded-md border px-3 py-2" required value={form.message} onChange={(e) => setForm({ ...form, message: e.target.value })} />
        </div>
        <button className="btn" type="submit" disabled={loading}>{loading ? "Sending..." : "Send Message"}</button>
      </form>
    </div>
  );
}


