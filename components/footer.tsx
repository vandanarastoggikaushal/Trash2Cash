import Link from "next/link";
import { CITY, COMPANY_NAME, SUPPORT_EMAIL, SUPPORT_PHONE } from "@/config/rewards";

export function Footer() {
  return (
    <footer className="mt-16 border-t bg-slate-50">
      <div className="container grid gap-6 py-10 sm:grid-cols-2 lg:grid-cols-4">
        <div>
          <h3 className="font-semibold">{COMPANY_NAME}</h3>
          <p className="mt-2 text-sm text-slate-600">Based in {CITY}, New Zealand</p>
          <p className="mt-2 text-sm">{SUPPORT_PHONE}</p>
          <p className="text-sm"><a href={`mailto:${SUPPORT_EMAIL}`} className="underline">{SUPPORT_EMAIL}</a></p>
        </div>
        <div>
          <h3 className="font-semibold">Company</h3>
          <ul className="mt-2 space-y-2 text-sm">
            <li><Link href="/how-it-works" className="hover:underline">How it Works</Link></li>
            <li><Link href="/rewards" className="hover:underline">Rewards</Link></li>
            <li><Link href="/partners" className="hover:underline">Partners</Link></li>
          </ul>
        </div>
        <div>
          <h3 className="font-semibold">Support</h3>
          <ul className="mt-2 space-y-2 text-sm">
            <li><Link href="/faq" className="hover:underline">FAQ</Link></li>
            <li><Link href="/contact" className="hover:underline">Contact</Link></li>
          </ul>
        </div>
        <div>
          <h3 className="font-semibold">Legal</h3>
          <ul className="mt-2 space-y-2 text-sm">
            <li><Link href="/terms" className="hover:underline">Terms</Link></li>
            <li><Link href="/privacy" className="hover:underline">Privacy</Link></li>
          </ul>
        </div>
      </div>
      <div className="border-t py-4 text-center text-xs text-slate-500">© {new Date().getFullYear()} {COMPANY_NAME}. All rights reserved.</div>
    </footer>
  );
}


