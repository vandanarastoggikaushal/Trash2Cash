import { API_ENDPOINTS } from '../config/api';

const jsonHeaders = {
  'Content-Type': 'application/json',
};

async function handleJsonResponse(response) {
  const text = await response.text();
  let data = null;
  try {
    data = text ? JSON.parse(text) : {};
  } catch (error) {
    throw new Error('Unexpected response from server.');
  }

  if (!response.ok || !data?.success) {
    const message = data?.error || `Request failed with status ${response.status}`;
    const error = new Error(message);
    error.status = response.status;
    throw error;
  }

  return data;
}

export async function loginUser(username, password) {
  const response = await fetch(API_ENDPOINTS.AUTH_LOGIN, {
    method: 'POST',
    headers: jsonHeaders,
    body: JSON.stringify({ username, password }),
  });
  return handleJsonResponse(response);
}

export async function registerUser(form) {
  const payload = {
    username: form.username,
    email: form.email,
    password: form.password,
    passwordConfirm: form.passwordConfirm,
    firstName: form.firstName,
    lastName: form.lastName,
    phone: form.phone,
    street: form.street,
    suburb: form.suburb,
    city: form.city,
    postcode: form.postcode,
    marketingOptIn: !!form.marketingOptIn,
    setupPayoutNow: !!form.setupPayoutNow,
    payoutMethod: form.payoutMethod,
    bankName: form.bankName,
    bankAccount: form.bankAccount,
    childName: form.childName,
    childBankAccount: form.childBankAccount,
    kiwisaverProvider: form.kiwisaverProvider,
    kiwisaverMemberId: form.kiwisaverMemberId,
  };

  if (!form.setupPayoutNow) {
    payload.payoutMethod = 'bank';
    payload.bankName = '';
    payload.bankAccount = '';
    payload.childName = '';
    payload.childBankAccount = '';
    payload.kiwisaverProvider = '';
    payload.kiwisaverMemberId = '';
  }

  const response = await fetch(API_ENDPOINTS.AUTH_REGISTER, {
    method: 'POST',
    headers: jsonHeaders,
    body: JSON.stringify(payload),
  });
  return handleJsonResponse(response);
}

export async function getProfile(token) {
  const response = await fetch(API_ENDPOINTS.PROFILE, {
    method: 'GET',
    headers: {
      ...jsonHeaders,
      Authorization: `Bearer ${token}`,
    },
  });
  return handleJsonResponse(response);
}

export async function updateProfile(token, updates) {
  const response = await fetch(API_ENDPOINTS.PROFILE, {
    method: 'POST',
    headers: {
      ...jsonHeaders,
      Authorization: `Bearer ${token}`,
    },
    body: JSON.stringify(updates),
  });
  return handleJsonResponse(response);
}

export async function logoutUser(token) {
  if (!token) return;
  try {
    await fetch(API_ENDPOINTS.AUTH_LOGOUT, {
      method: 'POST',
      headers: {
        ...jsonHeaders,
        Authorization: `Bearer ${token}`,
      },
    });
  } catch (error) {
    console.warn('[API] logout request failed', error);
  }
}

