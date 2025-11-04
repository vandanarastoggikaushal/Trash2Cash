# Mobile App Release Guide

Complete guide to publish Trash2Cash mobile app to iOS App Store and Google Play Store.

## Prerequisites

### For iOS (App Store)
1. **Apple Developer Account** - $99/year
   - Sign up at: https://developer.apple.com/programs/
   - Required for app submission

2. **Xcode** (Mac only)
   - Download from Mac App Store
   - Required for iOS builds and submission

3. **App Store Connect Account**
   - Access via: https://appstoreconnect.apple.com
   - Link to your Apple Developer account

### For Android (Google Play)
1. **Google Play Console Account** - $25 one-time fee
   - Sign up at: https://play.google.com/console
   - One-time registration fee

2. **Android Studio** (Optional but recommended)
   - For testing and debugging
   - Download from: https://developer.android.com/studio

## Step 1: Install Expo EAS (Expo Application Services)

EAS is the modern way to build and submit Expo apps.

```bash
cd mobile-app
npm install -g eas-cli
eas login
```

## Step 2: Configure App Details

### Update app.json

Ensure your `app.json` has all required information:

```json
{
  "expo": {
    "name": "Trash2Cash NZ",
    "slug": "trash2cash-mobile",
    "version": "1.0.0",
    "orientation": "portrait",
    "icon": "./assets/icon.png",
    "splash": {
      "image": "./assets/splash.png",
      "resizeMode": "contain",
      "backgroundColor": "#15803d"
    },
    "ios": {
      "supportsTablet": true,
      "bundleIdentifier": "nz.trash2cash.mobile",
      "buildNumber": "1"
    },
    "android": {
      "package": "nz.trash2cash.mobile",
      "versionCode": 1,
      "adaptiveIcon": {
        "foregroundImage": "./assets/adaptive-icon.png",
        "backgroundColor": "#15803d"
      }
    }
  }
}
```

### Create App Icons

You need to create these image files in `mobile-app/assets/`:

1. **icon.png** - 1024x1024 pixels (square)
2. **splash.png** - 1242x2436 pixels (iPhone size)
3. **adaptive-icon.png** - 1024x1024 pixels (Android)

Tools to create icons:
- Online: https://www.appicon.co/
- Design tool: Figma, Canva, or Photoshop

## Step 3: Configure EAS Build

### Initialize EAS

```bash
cd mobile-app
eas build:configure
```

This creates `eas.json` configuration file.

### Update eas.json (if needed)

```json
{
  "build": {
    "production": {
      "env": {
        "API_BASE_URL": "https://trash2cash.co.nz"
      }
    },
    "development": {
      "developmentClient": true,
      "distribution": "internal"
    }
  },
  "submit": {
    "production": {}
  }
}
```

## Step 4: Build for iOS

### Build iOS App

```bash
eas build --platform ios
```

This will:
1. Ask if you want to create a new Apple Developer account or use existing
2. Upload your code to Expo servers
3. Build the app in the cloud
4. Provide a download link when complete (takes 15-30 minutes)

### Download and Test

1. Download the `.ipa` file from the build link
2. Install on your iPhone using TestFlight (recommended) or directly via Xcode

### Submit to App Store

**Option 1: Using EAS Submit (Recommended)**
```bash
eas submit --platform ios
```

**Option 2: Manual Submission via Xcode**
1. Download the `.ipa` file
2. Open Xcode → Window → Organizer
3. Drag the `.ipa` file into Organizer
4. Click "Distribute App"
5. Follow the prompts to submit to App Store Connect

### App Store Connect Setup

1. **Create App Listing**
   - Go to https://appstoreconnect.apple.com
   - Click "My Apps" → "+" → "New App"
   - Fill in:
     - Name: Trash2Cash NZ
     - Primary Language: English
     - Bundle ID: nz.trash2cash.mobile
     - SKU: trash2cash-mobile-001

2. **Complete App Information**
   - App description
   - Keywords
   - Category: Utilities / Lifestyle
   - Privacy Policy URL (required)
   - Screenshots (required):
     - iPhone 6.7" (1290 x 2796)
     - iPhone 6.5" (1242 x 2688)
     - iPhone 5.5" (1242 x 2208)

3. **Submit for Review**
   - Upload screenshots and metadata
   - Submit for review
   - Review time: 1-3 days typically

## Step 5: Build for Android

### Build Android App

```bash
eas build --platform android
```

This will:
1. Build the app in the cloud
2. Provide a download link for `.apk` or `.aab` file
3. Takes 15-30 minutes

### Download and Test

1. Download the `.apk` file
2. Install on Android device to test
3. For production, use `.aab` (Android App Bundle) format

### Submit to Google Play

**Option 1: Using EAS Submit (Recommended)**
```bash
eas submit --platform android
```

**Option 2: Manual Submission**
1. Go to https://play.google.com/console
2. Create new app
3. Fill in app details
4. Upload the `.aab` file
5. Complete store listing
6. Submit for review

### Google Play Console Setup

1. **Create App**
   - Go to Google Play Console
   - Click "Create app"
   - Fill in:
     - App name: Trash2Cash NZ
     - Default language: English
     - App or game: App
     - Free or paid: Free
     - Privacy Policy: Required

2. **Complete Store Listing**
   - Short description (80 chars max)
   - Full description (4000 chars max)
   - App icon (512x512)
   - Feature graphic (1024x500)
   - Screenshots (at least 2):
     - Phone: 16:9 or 9:16 ratio
     - Tablet: 16:9 or 9:16 ratio

3. **Content Rating**
   - Complete questionnaire
   - Usually rates "Everyone" for utility apps

4. **Pricing & Distribution**
   - Set as free
   - Select countries (or worldwide)
   - Accept content guidelines

5. **Submit for Review**
   - Review time: 1-7 days typically

## Step 6: Update Version Numbers

For each new release:

**iOS:**
- Update `version` in `app.json` (e.g., "1.0.1")
- Update `ios.buildNumber` (increment by 1)

**Android:**
- Update `version` in `app.json` (e.g., "1.0.1")
- Update `android.versionCode` (increment by 1)

## Step 7: Testing Checklist

Before submitting:

- [ ] Test on physical devices (iOS and Android)
- [ ] Test all forms (contact, pickup request)
- [ ] Test API connectivity to production
- [ ] Test email notifications
- [ ] Test navigation between screens
- [ ] Test date picker and time selection
- [ ] Test rewards calculator
- [ ] Verify all icons and splash screens display correctly
- [ ] Test on different screen sizes
- [ ] Test offline behavior (should show error messages)

## Step 8: Privacy Policy

**Required for both stores!**

Create a privacy policy page that covers:
- What data you collect
- How you use the data
- Third-party services (if any)
- User rights

Host it on your website: `https://trash2cash.co.nz/privacy.php`

## Common Issues & Solutions

### Issue: Build fails
- Check `app.json` for errors
- Ensure all required assets exist
- Check EAS build logs for details

### Issue: App rejected by Apple
- Check rejection reason in App Store Connect
- Common reasons: Missing privacy policy, unclear functionality
- Fix issues and resubmit

### Issue: App rejected by Google
- Check rejection reason in Play Console
- Common reasons: Missing privacy policy, content rating issues
- Fix issues and resubmit

### Issue: API not working in production build
- Ensure `API_BASE_URL` is set correctly
- Check CORS settings on server
- Verify SSL certificate is valid

## Quick Reference Commands

```bash
# Login to Expo
eas login

# Configure build
eas build:configure

# Build iOS
eas build --platform ios

# Build Android
eas build --platform android

# Build both
eas build --platform all

# Submit iOS
eas submit --platform ios

# Submit Android
eas submit --platform android

# Check build status
eas build:list

# View build logs
eas build:view [build-id]
```

## Cost Summary

- **Apple Developer Program**: $99/year
- **Google Play Console**: $25 one-time
- **Expo EAS Build**: Free tier available (limited builds/month)
  - Paid plans start at $29/month for more builds

## Timeline Estimate

- **Setup**: 1-2 days (accounts, icons, screenshots)
- **Build**: 1-2 hours per platform
- **Testing**: 1-2 days
- **App Store Review**: 1-3 days
- **Play Store Review**: 1-7 days

**Total: ~1-2 weeks from start to published**

## Next Steps After Release

1. Monitor app reviews and ratings
2. Set up analytics (optional - Firebase Analytics, etc.)
3. Plan for updates and bug fixes
4. Market your app
5. Monitor crash reports

## Support Resources

- **Expo Documentation**: https://docs.expo.dev/
- **EAS Build Docs**: https://docs.expo.dev/build/introduction/
- **Apple App Store Review Guidelines**: https://developer.apple.com/app-store/review/guidelines/
- **Google Play Policies**: https://play.google.com/about/developer-content-policy/

Good luck with your release! 🚀

