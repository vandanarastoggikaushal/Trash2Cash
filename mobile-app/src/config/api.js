// API Configuration
// NOTE: If testing on a physical device, replace 'localhost' with your computer's IP address
// Find your IP: Windows (ipconfig) or Mac/Linux (ifconfig)
// Example: 'http://192.168.1.100:8000'
const API_BASE_URL = __DEV__ 
  ? 'http://localhost:8000' // For local development (use your computer's IP if testing on physical device)
  : 'https://trash2cash.co.nz'; // Production URL

export const API_ENDPOINTS = {
  CONTACT: `${API_BASE_URL}/api/contact.php`,
  LEAD: `${API_BASE_URL}/api/lead.php`,
  HEALTH: `${API_BASE_URL}/api/health.php`,
};

export const APP_CONFIG = {
  COMPANY_NAME: 'Trash2Cash NZ',
  SUPPORT_EMAIL: 'collect@trash2cash.co.nz',
  SUPPORT_PHONE: '+64221758458',
  CITY: 'Wellington',
  CAN_REWARD_PER_100: 1, // $1 per 100 cans
  APPLIANCE_CREDITS: {
    washing_machine: { label: 'Washing machine', credit: 6 },
    dishwasher: { label: 'Dishwasher', credit: 5 },
    microwave: { label: 'Microwave', credit: 2 },
    pc_case: { label: 'PC case (metal)', credit: 2 },
    laptop: { label: 'Laptop (metal body)', credit: 3 },
  },
  SERVICE_AREAS: [
    'Wellington City',
    'Churton Park',
    'Johnsonville',
    'Karori',
    'Newlands',
    'Tawa',
    'Lower Hutt',
    'Upper Hutt',
    'Porirua',
  ],
};

export default {
  API_BASE_URL,
  API_ENDPOINTS,
  APP_CONFIG,
};
