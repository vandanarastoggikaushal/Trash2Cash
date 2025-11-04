# Quick Start: Release Checklist

## Pre-Release Checklist

### 1. Assets Required
- [ ] `assets/icon.png` (1024x1024)
- [ ] `assets/splash.png` (1242x2436)
- [ ] `assets/adaptive-icon.png` (1024x1024) - Android only

### 2. App Configuration
- [ ] Update `app.json` with correct bundle IDs
- [ ] Set version number (e.g., "1.0.0")
- [ ] Verify API URL is production: `https://trash2cash.co.nz`
- [ ] Test app on device before building

### 3. Accounts Needed
- [ ] Apple Developer Account ($99/year)
- [ ] Google Play Console Account ($25 one-time)
- [ ] Expo account (free)

## 5-Minute Setup

```bash
# 1. Install EAS CLI
npm install -g eas-cli

# 2. Login to Expo
eas login

# 3. Configure build
cd mobile-app
eas build:configure

# 4. Build for iOS
eas build --platform ios

# 5. Build for Android
eas build --platform android
```

## After Build Completes

### iOS
1. Download `.ipa` file
2. Go to App Store Connect
3. Create new app
4. Upload via Xcode or EAS submit
5. Complete store listing
6. Submit for review

### Android
1. Download `.aab` file
2. Go to Google Play Console
3. Create new app
4. Upload `.aab` file
5. Complete store listing
6. Submit for review

## Important Notes

- **Privacy Policy**: Required for both stores
- **Screenshots**: Required for both stores
- **App Icons**: Required before building
- **Review Time**: iOS (1-3 days), Android (1-7 days)

See `RELEASE-GUIDE.md` for detailed instructions.

