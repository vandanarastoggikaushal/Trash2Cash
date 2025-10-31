import Link from "next/link";
import { motion } from "framer-motion";

export function Hero() {
  return (
    <section className="relative overflow-hidden bg-gradient-to-b from-emerald-50 to-white">
      <div className="container grid gap-8 py-16 lg:grid-cols-2 lg:items-center">
        <div>
          <motion.h1
            className="text-4xl font-bold tracking-tight text-slate-900 sm:text-5xl"
            initial={{ opacity: 0, y: 12 }}
            animate={{ opacity: 1, y: 0 }}
          >
            Turn Your Trash Into Cash (or KiwiSaver)
          </motion.h1>
          <motion.p
            className="mt-4 text-lg text-slate-700"
            initial={{ opacity: 0, y: 12 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ delay: 0.1 }}
          >
            We collect your clean aluminium cans and old appliances from home. You earn $1 for every 50 cans—and can send it straight to your kids’ accounts or KiwiSaver.
          </motion.p>
          <div className="mt-6 flex gap-3">
            <Link className="btn" href="/schedule-pickup">Schedule a Pickup</Link>
            <Link className="btn-secondary" href="/how-it-works">How It Works</Link>
          </div>
          <p className="mt-6 text-sm text-slate-600">Most households throw away around $500/year in recyclable value—and get nothing for it. Let’s change that.</p>
        </div>
        <motion.div
          className="h-64 rounded-xl bg-emerald-100/60 lg:h-80"
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          transition={{ delay: 0.2 }}
        />
      </div>
    </section>
  );
}


