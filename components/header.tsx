"use client";
import Link from "next/link";
import { usePathname } from "next/navigation";
import { Coins } from "lucide-react";

const nav = [
  { href: "/", label: "Home" },
  { href: "/how-it-works", label: "How it Works" },
  { href: "/rewards", label: "Rewards" },
  { href: "/schedule-pickup", label: "Schedule Pickup" },
  { href: "/partners", label: "Partners" },
  { href: "/faq", label: "FAQ" },
  { href: "/contact", label: "Contact" }
];

export function Header() {
  const pathname = usePathname();
  return (
    <header className="sticky top-0 z-50 w-full border-b bg-white/90 backdrop-blur">
      <div className="container flex h-16 items-center justify-between">
        <Link href="/" className="flex items-center gap-2 font-semibold">
          <span className="inline-flex h-8 w-8 items-center justify-center rounded-full bg-brand text-white"><Coins size={18} /></span>
          <span>Trash2Cash NZ</span>
        </Link>
        <nav className="hidden gap-6 md:flex">
          {nav.map((item) => (
            <Link
              key={item.href}
              href={item.href}
              className={`text-sm font-medium hover:text-brand ${pathname === item.href ? "text-brand" : "text-slate-700"}`}
            >
              {item.label}
            </Link>
          ))}
        </nav>
        <div className="md:hidden text-sm">
          <Link href="/schedule-pickup" className="btn">Schedule Pickup</Link>
        </div>
      </div>
    </header>
  );
}


