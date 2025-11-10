# Trash2Cash Mobile App

React Native mobile application for Trash2Cash NZ using Expo.

## Features

- 📱 Schedule pickups for cans and appliances
- 💰 Rewards calculator
- 📧 Contact form
- 📖 How It Works guide
- ❓ FAQ section
- 🎨 Modern, native-feeling UI

## Prerequisites

- Node.js (v16 or higher)
- npm or yarn
- Expo CLI: `npm install -g expo-cli`
- Expo Go app on your phone (for testing)

## Installation

1. Navigate to the mobile-app directory:
```bash
cd mobile-app
```

2. Install dependencies:
```bash
npm install
```

3. Start the development server:
```bash
npm start
```

4. Scan the QR code with:
   - **iOS**: Camera app
   - **Android**: Expo Go app

## Configuration

Update the API base URL in `src/config/api.js`:
- For local development: `http://localhost:8000`
- For production: `https://trash2cash.co.nz`

## Building for Production

### iOS
```bash
expo build:ios
```

### Android
```bash
expo build:android
```

## Project Structure

```
mobile-app/
├── App.js                 # Main app component with navigation
├── src/
│   ├── screens/          # Screen components
│   │   ├── HomeScreen.js
│   │   ├── RewardsScreen.js
│   │   ├── SchedulePickupScreen.js
│   │   ├── ContactScreen.js
│   │   ├── HowItWorksScreen.js
│   │   └── FAQScreen.js
│   ├── services/        # API services
│   │   └── api.js
│   ├── config/          # Configuration
│   │   └── api.js
│   └── theme.js         # Theme configuration
└── package.json
```

## API Integration

The app connects to your existing PHP backend:
- Contact form: `/api/contact`
- Pickup requests: `/api/lead`

## Next Steps

1. Add app icons and splash screens
2. Configure push notifications
3. Add offline support
4. Set up app store listings
5. Configure deep linking

## Troubleshooting

- **Cannot connect to API**: Make sure your phone and computer are on the same network, or update the API URL to your production server
- **Build errors**: Clear node_modules and reinstall: `rm -rf node_modules && npm install`

