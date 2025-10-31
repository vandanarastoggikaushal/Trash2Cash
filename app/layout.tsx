import "@/app/styles/globals.css";
import type { Metadata } from "next";
import { COMPANY_NAME, SUPPORT_EMAIL } from "@/config/rewards";
import SITE from "@/config/site";
import { Header } from "@/components/header";
import { Footer } from "@/components/footer";
import { Toaster } from "sonner";

export const metadata: Metadata = {
  title: `${COMPANY_NAME} | Turn Your Trash Into Cash (or KiwiSaver)`,
  description: SITE.description,
  metadataBase: new URL(SITE.url),
  openGraph: {
    title: `${COMPANY_NAME}`,
    description: SITE.description,
    url: SITE.url,
    siteName: `${COMPANY_NAME}`,
    type: "website"
  },
  keywords: [
    "Wellington recycling",
    "aluminium cans",
    "appliance pickup",
    "KiwiSaver",
    "fundraiser"
  ],
  creator: SUPPORT_EMAIL
};

export default function RootLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="en-NZ">
      <body className="min-h-screen bg-white text-slate-800">
        <Header />
        <main>{children}</main>
        <Footer />
        <Toaster position="top-right" />
      </body>
    </html>
  );
}


