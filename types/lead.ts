export interface Lead {
  id: string; // nanoid
  createdAt: string; // ISO
  person: {
    fullName: string;
    email: string;
    phone: string; // validate NZ format
    marketingOptIn: boolean;
  };
  address: {
    street: string;
    suburb: string;
    city: string; // default "Wellington"
    postcode: string; // regex 0-9{4}
    accessNotes?: string;
  };
  pickup: {
    type: "cans" | "appliances" | "both";
    cansEstimate?: number; // integer
    appliances?: { slug: string; qty: number; notes?: string }[];
    preferredDate?: string; // yyyy-mm-dd
    preferredWindow?: "Morning" | "Afternoon" | "Evening";
  };
  payout: {
    method: "bank" | "child_account" | "kiwisaver";
    bank?: { name: string; accountNumber: string }; // placeholder; masked in UI
    child?: { childName: string; bankAccount?: string };
    kiwiSaver?: { provider: string; memberId: string };
  };
  confirm: {
    itemsAreClean: boolean;
    acceptedTerms: boolean;
  };
}


