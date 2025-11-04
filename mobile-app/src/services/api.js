import { API_ENDPOINTS } from '../config/api';

// Helper function to make API calls
async function apiCall(endpoint, data, method = 'POST') {
  try {
    const formData = new URLSearchParams();
    formData.append('payload', JSON.stringify(data));

    const response = await fetch(endpoint, {
      method,
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: formData,
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const result = await response.json();
    return result;
  } catch (error) {
    console.error('API Error:', error);
    throw error;
  }
}

// Contact form submission
export const submitContact = async (formData) => {
  const data = {
    name: formData.name,
    email: formData.email,
    message: formData.message,
  };
  return apiCall(API_ENDPOINTS.CONTACT, data);
};

// Schedule pickup submission
export const submitPickupRequest = async (formData) => {
  const payload = {
    person: {
      fullName: formData.fullName,
      email: formData.email,
      phone: formData.phone,
      marketingOptIn: formData.marketingOptIn || false,
    },
    address: {
      street: formData.street,
      suburb: formData.suburb,
      city: formData.city,
      postcode: formData.postcode,
      accessNotes: formData.accessNotes || undefined,
    },
    pickup: {
      type: formData.pickupType,
      cansEstimate: formData.cansEstimate ? parseInt(formData.cansEstimate) : undefined,
      appliances: formData.appliances || undefined,
      preferredDate: formData.preferredDate || undefined,
      preferredWindow: formData.preferredWindow || undefined,
    },
    payout: {
      method: formData.payoutMethod,
      bank: formData.payoutMethod === 'bank' ? {
        name: formData.bankName || '',
        accountNumber: formData.bankAccount || '',
      } : undefined,
      child: formData.payoutMethod === 'child_account' ? {
        childName: formData.childName || '',
        bankAccount: formData.childBankAccount || undefined,
      } : undefined,
      kiwiSaver: formData.payoutMethod === 'kiwisaver' ? {
        provider: formData.kiwisaverProvider || '',
        memberId: formData.kiwisaverMemberId || '',
      } : undefined,
    },
    confirm: {
      itemsAreClean: formData.itemsAreClean || false,
      acceptedTerms: formData.acceptedTerms || false,
    },
  };
  return apiCall(API_ENDPOINTS.LEAD, payload);
};

// Health check
export const checkHealth = async () => {
  try {
    const response = await fetch(API_ENDPOINTS.HEALTH);
    return response.ok;
  } catch (error) {
    return false;
  }
};

