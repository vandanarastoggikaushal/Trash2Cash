document.getElementById('contact-form').addEventListener('submit', async function(e) {
  e.preventDefault();
  
  const submitBtn = document.getElementById('submit-btn');
  const successDiv = document.getElementById('contact-success');
  const errorDiv = document.getElementById('contact-error');
  const form = e.target;
  
  const formData = {
    name: form.name.value,
    email: form.email.value,
    message: form.message.value
  };
  
  submitBtn.disabled = true;
  submitBtn.textContent = 'Sending...';
  successDiv.classList.add('hidden');
  errorDiv.classList.add('hidden');
  
  try {
    const response = await fetch('/api/contact', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: new URLSearchParams({
        payload: JSON.stringify(formData)
      })
    });
    
    const data = await response.json();
    
    if (response.ok && data.ok) {
      successDiv.classList.remove('hidden');
      form.reset();
    } else {
      errorDiv.classList.remove('hidden');
    }
  } catch (error) {
    console.error('Error:', error);
    errorDiv.classList.remove('hidden');
  } finally {
    submitBtn.disabled = false;
    submitBtn.textContent = 'Send Message';
  }
});

