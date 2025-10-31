import { z } from "zod";
import { CITY } from "@/config/rewards";

export const nzPhoneRegex = /^(\+64|0)[2-9]\d{7,8}$/;
export const nzPostcodeRegex = /^\d{4}$/;

export const ApplianceSchema = z.object({
  slug: z.string(),
  qty: z.number().int().min(1),
  notes: z.string().optional()
});

export const LeadSchema = z.object({
  id: z.string(),
  createdAt: z.string(),
  person: z.object({
    fullName: z.string().min(2),
    email: z.string().email(),
    phone: z.string().regex(nzPhoneRegex),
    marketingOptIn: z.boolean()
  }),
  address: z.object({
    street: z.string().min(2),
    suburb: z.string().min(2),
    city: z.string().default(CITY),
    postcode: z.string().regex(nzPostcodeRegex),
    accessNotes: z.string().optional()
  }),
  pickup: z.object({
    type: z.enum(["cans", "appliances", "both"]),
    cansEstimate: z.number().int().min(0).optional(),
    appliances: z.array(ApplianceSchema).optional(),
    preferredDate: z.string().optional(),
    preferredWindow: z.enum(["Morning", "Afternoon", "Evening"]).optional()
  }),
  payout: z.object({
    method: z.enum(["bank", "child_account", "kiwisaver"]),
    bank: z.object({ name: z.string(), accountNumber: z.string() }).optional(),
    child: z.object({ childName: z.string(), bankAccount: z.string().optional() }).optional(),
    kiwiSaver: z.object({ provider: z.string(), memberId: z.string() }).optional()
  }),
  confirm: z.object({
    itemsAreClean: z.boolean(),
    acceptedTerms: z.boolean()
  })
});

export type LeadInput = z.infer<typeof LeadSchema>;


