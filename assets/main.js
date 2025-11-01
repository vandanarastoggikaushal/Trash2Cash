// Rewards Calculator
(function() {
  const applianceCredits = {
    washing_machine: 6,
    dishwasher: 5,
    microwave: 2,
    pc_case: 2,
    laptop: 3
  };

  const applianceLabels = {
    washing_machine: 'Washing machine',
    dishwasher: 'Dishwasher',
    microwave: 'Microwave',
    pc_case: 'PC case (metal)',
    laptop: 'Laptop (metal body)'
  };

  function calculateCansReward(cansPerWeek) {
    const yearly = Math.floor((cansPerWeek * 52) / 50) * 1;
    return yearly;
  }

  function calculateApplianceReward(items) {
    return items.reduce((sum, item) => {
      return sum + (applianceCredits[item.slug] || 0) * item.qty;
    }, 0);
  }

  function projectKiwiSaver(totalPerYear, years = 10, rate = 0.05) {
    if (rate === 0) return totalPerYear * years;
    const fv = totalPerYear * ((Math.pow(1 + rate, years) - 1) / rate);
    return Math.round(fv * 100) / 100;
  }

  // Initialize rewards calculator if it exists
  const calculatorSection = document.getElementById('rewards-calculator');
  if (calculatorSection) {
    const cansInput = document.getElementById('cans');
    const cansDisplay = document.getElementById('cans-display');
    const appliancesContainer = document.getElementById('appliances-inputs');
    const cansReward = document.getElementById('cans-reward');
    const applianceReward = document.getElementById('appliance-reward');
    const totalEarnings = document.getElementById('total-earnings');
    const showKiwiSaver = document.getElementById('show-kiwisaver');
    const kiwisaverPreview = document.getElementById('kiwisaver-preview');

    // Initialize appliance inputs
    Object.keys(applianceCredits).forEach(slug => {
      const div = document.createElement('div');
      div.className = 'flex items-center justify-between rounded-lg border p-3';
      div.innerHTML = `
        <span class="text-sm">${applianceLabels[slug]}</span>
        <input type="number" min="0" class="w-20 rounded-md border px-2 py-1 appliance-input" data-slug="${slug}" value="0" />
      `;
      appliancesContainer.appendChild(div);
    });

    function updateCalculator() {
      const cansPerWeek = parseInt(cansInput.value) || 0;
      cansDisplay.textContent = cansPerWeek;

      const applianceInputs = document.querySelectorAll('.appliance-input');
      const appliances = Array.from(applianceInputs)
        .map(input => ({
          slug: input.dataset.slug,
          qty: parseInt(input.value) || 0
        }))
        .filter(item => item.qty > 0);

      const cans = calculateCansReward(cansPerWeek);
      const appl = calculateApplianceReward(appliances);
      const total = cans + appl;

      cansReward.textContent = `$${cans}`;
      applianceReward.textContent = `$${appl}`;
      totalEarnings.textContent = `$${total}`;

      if (showKiwiSaver.checked) {
        const kiwisaverValue = projectKiwiSaver(total, 10, 0.05);
        kiwisaverPreview.textContent = `$${kiwisaverValue.toLocaleString()}`;
        kiwisaverPreview.classList.remove('hidden');
      } else {
        kiwisaverPreview.classList.add('hidden');
      }
    }

    cansInput.addEventListener('input', updateCalculator);
    appliancesContainer.addEventListener('input', (e) => {
      if (e.target.classList.contains('appliance-input')) {
        updateCalculator();
      }
    });
    showKiwiSaver.addEventListener('change', updateCalculator);

    updateCalculator();
  }
})();

// Toast notifications helper (for schedule-pickup.js)
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

