// NZ phone regex
const nzPhoneRegex = /^(\+64|0)[2-9]\d{7,8}$/;
const nzPostcodeRegex = /^\d{4}$/;

// Ensure showToast is available
if (!window.showToast) {
  window.showToast = function(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 z-50 rounded-lg px-4 py-2 shadow-lg ${
      type === 'success' ? 'bg-emerald-600 text-white' : 'bg-red-600 text-white'
    }`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => {
      toast.style.opacity = '0';
      toast.style.transition = 'opacity 0.3s';
      setTimeout(() => toast.remove(), 300);
    }, 3000);
  };
}

// Handle pickup type selection to show/hide sections
function updatePickupSections(selectedType) {
  const cansSection = document.getElementById('cans-section');
  const appliancesSection = document.getElementById('appliances-section');
  
  // Update label styling
  document.querySelectorAll('.pickup-type-label').forEach(label => {
    if (label.dataset.type === selectedType) {
      label.classList.add('bg-emerald-100', 'border-brand', 'shadow-md');
      label.classList.remove('bg-transparent');
    } else {
      label.classList.remove('bg-emerald-100', 'border-brand', 'shadow-md');
      label.classList.add('bg-transparent');
    }
  });
  
  // Show/hide sections based on selection
  if (selectedType === 'cans') {
    if (cansSection) {
      cansSection.style.display = 'block';
      cansSection.classList.add('animate-fade-in');
    }
    if (appliancesSection) {
      appliancesSection.style.display = 'none';
      appliancesSection.classList.remove('animate-fade-in');
    }
  } else if (selectedType === 'appliances') {
    if (cansSection) {
      cansSection.style.display = 'none';
      cansSection.classList.remove('animate-fade-in');
    }
    if (appliancesSection) {
      appliancesSection.style.display = 'block';
      appliancesSection.classList.add('animate-fade-in');
    }
  } else if (selectedType === 'both') {
    if (cansSection) {
      cansSection.style.display = 'block';
      cansSection.classList.add('animate-fade-in');
    }
    if (appliancesSection) {
      appliancesSection.style.display = 'block';
      appliancesSection.classList.add('animate-fade-in');
    }
  }
  
  // Recalculate rewards after visibility change
  setTimeout(calculateRewards, 100);
}

function handlePickupTypeChange() {
  const pickupTypeRadios = document.querySelectorAll('input[name="pickupType"]');
  
  pickupTypeRadios.forEach(radio => {
    radio.addEventListener('change', function() {
      updatePickupSections(this.value);
    });
  });
  
  // Initialize on page load
  const checkedRadio = document.querySelector('input[name="pickupType"]:checked');
  if (checkedRadio) {
    updatePickupSections(checkedRadio.value);
  } else {
    updatePickupSections('cans');
  }
}

// Address autocomplete functionality
let addressSearchTimeout = null;
let addressSuggestions = [];

function initAddressAutocomplete() {
  const addressSearchInput = document.getElementById('address-search');
  const suggestionsDiv = document.getElementById('address-suggestions');
  
  // Exit early if address search is disabled (element doesn't exist)
  if (!addressSearchInput || !suggestionsDiv) return;
  
  addressSearchInput.addEventListener('input', function(e) {
    const query = e.target.value.trim();
    
    clearTimeout(addressSearchTimeout);
    
    if (query.length < 3) {
      suggestionsDiv.classList.add('hidden');
      return;
    }
    
    addressSearchTimeout = setTimeout(async () => {
      try {
        const response = await fetch(`/api/address-search?q=${encodeURIComponent(query)}`);
        
        if (!response.ok) {
          console.error('Address search API error:', response.status, response.statusText);
          suggestionsDiv.classList.add('hidden');
          return;
        }
        
        const data = await response.json();
        
        if (data.suggestions && data.suggestions.length > 0) {
          addressSuggestions = data.suggestions;
          displaySuggestions(data.suggestions);
        } else {
          suggestionsDiv.classList.add('hidden');
          // Show helpful message if no suggestions
          if (query.length >= 3) {
            console.log('No address suggestions found for:', query);
          }
        }
      } catch (error) {
        console.error('Address search error:', error);
        suggestionsDiv.classList.add('hidden');
        // Show user-friendly error
        if (query.length >= 3) {
          console.warn('Address search is temporarily unavailable. Please enter your address manually.');
        }
      }
    }, 300); // Debounce 300ms
  });
  
  // Hide suggestions when clicking outside
  document.addEventListener('click', function(e) {
    if (!addressSearchInput.contains(e.target) && !suggestionsDiv.contains(e.target)) {
      suggestionsDiv.classList.add('hidden');
    }
  });
}

function displaySuggestions(suggestions) {
  const suggestionsDiv = document.getElementById('address-suggestions');
  if (!suggestionsDiv) return;
  
  suggestionsDiv.innerHTML = suggestions.map((suggestion, index) => `
    <div 
      class="px-4 py-2 hover:bg-emerald-50 cursor-pointer border-b last:border-b-0" 
      data-index="${index}"
      data-id="${suggestion.id || ''}"
    >
      ${suggestion.address || suggestion.full_address || ''}
    </div>
  `).join('');
  
  suggestionsDiv.classList.remove('hidden');
  
  // Add click handlers
  suggestionsDiv.querySelectorAll('div[data-index]').forEach(item => {
    item.addEventListener('click', async function() {
      const addressId = this.dataset.id;
      const addressText = this.textContent.trim();
      
      // If we have an ID, try to fetch details from API
      // If no ID or ID is empty, use the address text as the ID (for fallback parsing)
      const idToUse = addressId || addressText;
      
      if (idToUse) {
        try {
          const response = await fetch(`/api/address-details?id=${encodeURIComponent(idToUse)}`);
          
          if (response.ok) {
            const data = await response.json();
            
            // Check if we got an error
            if (data.error) {
              throw new Error(data.error);
            }
            
            // Fill in the form fields
            if (data.street) {
              document.getElementById('street').value = data.street;
            }
            if (data.suburb) {
              document.getElementById('suburb').value = data.suburb;
            }
            if (data.city) {
              document.getElementById('city').value = data.city;
            }
            if (data.postcode) {
              document.getElementById('postcode').value = data.postcode;
            }
          } else {
            // API returned error, fall back to parsing
            throw new Error('API error');
          }
        } catch (error) {
          console.log('Using fallback address parsing for:', addressText);
          // Fallback: try to parse from address text
          parseAddressFromText(addressText);
        }
      } else {
        // No ID or text, just parse what we have
        parseAddressFromText(addressText);
      }
      
      document.getElementById('address-search').value = addressText;
      suggestionsDiv.classList.add('hidden');
    });
  });
}

function parseAddressFromText(addressText) {
  // Enhanced parsing fallback - split by comma and handle various formats
  const parts = addressText.split(',').map(p => p.trim());
  
  if (parts.length >= 2) {
    // First part is usually street
    document.getElementById('street').value = parts[0] || '';
    
    // Second part is usually suburb
    if (parts.length >= 2) {
      document.getElementById('suburb').value = parts[1] || '';
    }
    
    // City is usually second-to-last or last (if no postcode)
    if (parts.length >= 3) {
      const cityField = document.getElementById('city');
      // Check if last part has postcode
      const lastPart = parts[parts.length - 1];
      const postcodeMatch = lastPart.match(/\d{4}/);
      
      if (postcodeMatch) {
        // Last part has postcode, so city is second-to-last
        document.getElementById('postcode').value = postcodeMatch[0];
        if (parts.length >= 4) {
          cityField.value = parts[parts.length - 2] || cityField.value;
        }
      } else {
        // No postcode, last part might be city
        if (parts.length >= 3) {
          cityField.value = parts[parts.length - 1] || cityField.value;
        }
      }
    }
  } else if (parts.length === 1) {
    // Single part - might be just suburb or street
    // Check if it's a known suburb
    const wellingtonSuburbs = ['Wellington City', 'Churton Park', 'Johnsonville', 'Karori', 
                               'Newlands', 'Tawa', 'Lower Hutt', 'Upper Hutt', 'Porirua'];
    const isSuburb = wellingtonSuburbs.some(suburb => 
      addressText.toLowerCase().includes(suburb.toLowerCase())
    );
    
    if (isSuburb) {
      document.getElementById('suburb').value = addressText;
    } else {
      document.getElementById('street').value = addressText;
    }
  }
}

// Initialize address autocomplete
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initAddressAutocomplete);
} else {
  initAddressAutocomplete();
}

// Initialize pickup type handler immediately
handlePickupTypeChange();

// Also ensure it runs on DOMContentLoaded as backup
document.addEventListener('DOMContentLoaded', function() {
  // Re-initialize to ensure sections are properly shown/hidden
  const checkedRadio = document.querySelector('input[name="pickupType"]:checked');
  if (checkedRadio) {
    updatePickupSections(checkedRadio.value);
  }
});

// Calculate and display reward estimates
function calculateRewards() {
  // Calculate cans reward ($1 per 100 cans)
  const cansEstimate = parseInt(document.getElementById('cansEstimate')?.value || 0);
  const cansReward = Math.floor(cansEstimate / 100);
  const cansRewardEl = document.getElementById('cans-reward-estimate');
  if (cansRewardEl) {
    cansRewardEl.textContent = `$${cansReward}`;
  }
  
  // Calculate appliances reward
  let appliancesReward = 0;
  document.querySelectorAll('.appliance-qty').forEach(input => {
    const qty = parseInt(input.value || 0);
    const credit = parseInt(input.dataset.credit || 0);
    appliancesReward += qty * credit;
  });
  const appliancesRewardEl = document.getElementById('appliances-reward-estimate');
  if (appliancesRewardEl) {
    appliancesRewardEl.textContent = `$${appliancesReward}`;
  }
}

// Update reward estimates when cans input changes
const cansEstimateInput = document.getElementById('cansEstimate');
if (cansEstimateInput) {
  cansEstimateInput.addEventListener('input', calculateRewards);
  cansEstimateInput.addEventListener('change', calculateRewards);
}

// Update reward estimates when appliance quantities change
document.addEventListener('input', function(e) {
  if (e.target.classList.contains('appliance-qty')) {
    calculateRewards();
  }
});

// Initialize calculations on page load
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', calculateRewards);
} else {
  calculateRewards();
}

// Handle payout method visibility
document.querySelectorAll('input[name="payoutMethod"]').forEach(radio => {
  radio.addEventListener('change', function() {
    const bankSection = document.getElementById('payout-bank');
    const childSection = document.getElementById('payout-child');
    const kiwiSection = document.getElementById('payout-kiwisaver');
    if (bankSection) {
      bankSection.style.display = this.value === 'bank' ? 'grid' : 'none';
    }
    if (childSection) {
      childSection.classList.toggle('hidden', this.value !== 'child_account');
    }
    if (kiwiSection) {
      kiwiSection.classList.toggle('hidden', this.value !== 'kiwisaver');
    }
  });
});

// Form submission
document.getElementById('pickup-form').addEventListener('submit', async function(e) {
  e.preventDefault();
  
  const form = e.target;
  const submitBtn = document.getElementById('submit-btn');
  const successDiv = document.getElementById('pickup-success');
  const errorDiv = document.getElementById('pickup-error');
  const formData = new FormData(form);
  
  // Validation
  const phone = formData.get('phone');
  const postcode = formData.get('postcode');
  
  if (!nzPhoneRegex.test(phone)) {
    showToast('Please enter a valid NZ phone number.', 'error');
    return;
  }
  
  if (!nzPostcodeRegex.test(postcode)) {
    showToast('Please enter a valid 4-digit postcode.', 'error');
    return;
  }
  
  if (!formData.get('itemsAreClean') || !formData.get('acceptedTerms')) {
    showToast('Please confirm items are clean and accept terms.', 'error');
    return;
  }
  
  // Build payload
  const appliances = [];
  document.querySelectorAll('.appliance-qty').forEach(input => {
    const qty = parseInt(input.value) || 0;
    if (qty > 0) {
      appliances.push({
        slug: input.dataset.slug,
        qty: qty
      });
    }
  });
  
  const payload = {
    person: {
      fullName: formData.get('fullName'),
      email: formData.get('email'),
      phone: formData.get('phone'),
      marketingOptIn: formData.get('marketingOptIn') === 'on'
    },
    address: {
      street: formData.get('street'),
      suburb: formData.get('suburb'),
      city: formData.get('city'),
      postcode: formData.get('postcode'),
      accessNotes: formData.get('accessNotes') || undefined
    },
    pickup: {
      type: formData.get('pickupType'),
      cansEstimate: parseInt(formData.get('cansEstimate')) || undefined,
      appliances: appliances.length > 0 ? appliances : undefined,
      preferredDate: formData.get('preferredDate') || undefined,
      preferredWindow: formData.get('preferredWindow') || undefined
    },
    payout: {
      method: formData.get('payoutMethod'),
      bank: formData.get('payoutMethod') === 'bank' ? {
        name: formData.get('bankName') || '',
        accountNumber: formData.get('bankAccount') || ''
      } : undefined,
      child: formData.get('payoutMethod') === 'child_account' ? {
        childName: formData.get('childName') || '',
        bankAccount: formData.get('childBankAccount') || undefined
      } : undefined,
      kiwiSaver: formData.get('payoutMethod') === 'kiwisaver' ? {
        provider: formData.get('kiwisaverProvider') || '',
        memberId: formData.get('kiwisaverMemberId') || ''
      } : undefined
    },
    confirm: {
      itemsAreClean: formData.get('itemsAreClean') === 'on',
      acceptedTerms: formData.get('acceptedTerms') === 'on'
    }
  };
  
  submitBtn.disabled = true;
  submitBtn.textContent = 'Submitting...';
  successDiv.classList.add('hidden');
  errorDiv.classList.add('hidden');
  
  try {
    const response = await fetch('/api/lead', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: new URLSearchParams({
        payload: JSON.stringify(payload)
      })
    });
    
    const data = await response.json();
    
    if (response.ok && data.ok) {
      document.getElementById('reference-id').textContent = data.id;
      successDiv.classList.remove('hidden');
      form.style.display = 'none';
    } else {
      errorDiv.classList.remove('hidden');
      showToast('There was a problem submitting your request.', 'error');
    }
  } catch (error) {
    console.error('Error:', error);
    errorDiv.classList.remove('hidden');
    showToast('There was a problem submitting your request.', 'error');
  } finally {
    submitBtn.disabled = false;
    submitBtn.textContent = 'Submit request';
  }
});

