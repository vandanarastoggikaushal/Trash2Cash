import { API_ENDPOINTS } from '../config/api';

// Helper function to make API calls
async function apiCall(endpoint, data, method = 'POST') {
  try {
    // Convert to form-encoded string manually (URLSearchParams might not work in React Native)
    const payload = JSON.stringify(data);
    const formBody = `payload=${encodeURIComponent(payload)}`;

    console.log('[API] Calling:', endpoint);
    console.log('[API] Data:', JSON.stringify(data, null, 2));
    console.log('[API] Form body length:', formBody.length);

    const response = await fetch(endpoint, {
      method,
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: formBody,
    });

    console.log('[API] Response status:', response.status);

    if (!response.ok) {
      const errorText = await response.text();
      console.error('[API] Error response:', errorText);
      throw new Error(`HTTP error! status: ${response.status}, message: ${errorText}`);
    }

    const result = await response.json();
    console.log('[API] Success:', result);
    return result;
  } catch (error) {
    console.error('[API] Error:', error);
    console.error('[API] Error details:', {
      message: error.message,
      stack: error.stack,
      endpoint: endpoint,
    });
    
    // Provide more helpful error messages
    if (error.message.includes('Failed to fetch') || error.message.includes('Network request failed')) {
      throw new Error('Network error: Unable to connect to server. Please check your internet connection and ensure the API server is running.');
    }
    
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
  // Convert appliances object to array format expected by PHP
  const appliances = [];
  if (formData.appliances && typeof formData.appliances === 'object') {
    Object.entries(formData.appliances).forEach(([slug, qty]) => {
      const quantity = parseInt(qty) || 0;
      if (quantity > 0) {
        appliances.push({
          slug: slug,
          qty: quantity,
        });
      }
    });
  }

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
      appliances: appliances.length > 0 ? appliances : undefined,
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

