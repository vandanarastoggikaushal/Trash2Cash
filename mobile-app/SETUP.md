# Trash2Cash Mobile App Setup Guide

## Quick Start

1. **Install Expo CLI globally** (if not already installed):
```bash
npm install -g expo-cli
```

2. **Navigate to mobile-app directory**:
```bash
cd mobile-app
```

3. **Install dependencies**:
```bash
npm install
```

4. **Start the development server**:
```bash
npm start
```

5. **Install Expo Go app** on your phone:
   - iOS: Download from App Store
   - Android: Download from Google Play Store

6. **Scan the QR code** that appears:
   - iOS: Use Camera app to scan QR code
   - Android: Open Expo Go app and scan QR code

## Configuration

### API URL Setup

Update `src/config/api.js` to point to your backend:

**For local development:**
```javascript
const API_BASE_URL = 'http://YOUR_LOCAL_IP:8000';
```

**For production:**
```javascript
const API_BASE_URL = 'https://trash2cash.co.nz';
```

To find your local IP:
- Windows: `ipconfig` (look for IPv4 Address)
- Mac/Linux: `ifconfig` or `ip addr`

## Features

✅ Home screen with quick calculator
✅ Rewards calculator with cans and appliances
✅ Schedule pickup form (connects to your PHP API)
✅ Contact form
✅ How It Works guide
✅ FAQ section
✅ Native navigation with bottom tabs

## Project Structure

```
mobile-app/
├── App.js                      # Main app with navigation
├── app.json                    # Expo configuration
├── package.json                 # Dependencies
├── babel.config.js             # Babel configuration
├── src/
│   ├── screens/                # All screen components
│   │   ├── HomeScreen.js
│   │   ├── RewardsScreen.js
│   │   ├── SchedulePickupScreen.js
│   │   ├── ContactScreen.js
│   │   ├── HowItWorksScreen.js
│   │   └── FAQScreen.js
│   ├── services/
│   │   └── api.js              # API service (connects to PHP backend)
│   ├── config/
│   │   └── api.js              # API configuration
│   └── theme.js                # App theme and colors
```

## Next Steps

1. **Add app icons**:
   - Create `assets/icon.png` (1024x1024)
   - Create `assets/splash.png` (1242x2436)
   - Create `assets/adaptive-icon.png` (1024x1024)

2. **Test on device**:
   - Make sure your phone and computer are on the same WiFi network
   - Or update API URL to production server

3. **Build for production**:
   - iOS: `expo build:ios`
   - Android: `expo build:android`

## Troubleshooting

**"Cannot connect to API"**
- Make sure API URL is correct in `src/config/api.js`
- For local testing, use your computer's local IP address (not localhost)
- Ensure your PHP server is running and accessible

**"Module not found"**
- Delete `node_modules` folder
- Run `npm install` again

**"Expo Go not working"**
- Make sure you have the latest Expo Go app
- Try clearing Expo Go cache
- Restart the development server

## API Integration

The app connects to your existing PHP backend:
- Contact form → `/api/contact`
- Pickup requests → `/api/lead`

No changes needed to your PHP backend - it works as-is!

