# NZ Post Address API Setup Guide

## Overview

The address autocomplete feature uses NZ Post Address API (or AddressFinder as an alternative) to automatically fill in address fields when users search for their address.

## Option 1: AddressFinder (Recommended - Easier Setup)

AddressFinder is a New Zealand address autocomplete service that's easier to set up than NZ Post.

### Steps:

1. **Register for AddressFinder**
   - Go to: https://addressfinder.co.nz/
   - Sign up for a free account
   - Get your API key from the dashboard

2. **Set API Key on Hostinger**
   - Log into Hostinger cPanel
   - Go to Environment Variables or `.env` file
   - Add: `ADDRESSFINDER_API_KEY=your_api_key_here`
   - Or add to your `.htaccess` or PHP configuration

3. **Alternative: Set in PHP File**
   - Edit `api/address-search.php`
   - Replace `getenv('ADDRESSFINDER_API_KEY')` with your actual key (for testing only)
   - **Note:** Don't commit API keys to version control!

### AddressFinder Pricing:
- Free tier: 1,000 requests/month
- Paid plans available for higher usage

## Option 2: NZ Post Address API

NZ Post offers address validation and autocomplete through their shipping APIs.

### Steps:

1. **Register with NZ Post**
   - Go to: https://www.nzpost.co.nz/business/ecommerce/shipping-apis
   - Register for a business account
   - Request commercial access to APIs
   - Complete the registration form

2. **Get API Credentials**
   - After approval, you'll receive:
     - Client ID
     - Client Secret
   - These are used for OAuth authentication

3. **Configure in PHP**
   - Update `api/address-search.php` with your credentials
   - Implement OAuth token retrieval (see NZ Post API docs)
   - The API uses OAuth 2.0 for authentication

### NZ Post API Pricing:
- Contact NZ Post for pricing information
- May require business verification

## Testing Without API

The forms will still work without API credentials - users can manually enter addresses. The autocomplete will simply not show suggestions.

## Implementation Status

✅ **Web Form** (`schedule-pickup.php`)
- Address search field added
- Autocomplete dropdown implemented
- Auto-fills street, suburb, city, postcode

✅ **Mobile App** (`SchedulePickupScreen.js`)
- Address search field added
- Autocomplete dropdown implemented
- Auto-fills address fields

✅ **Backend API**
- `api/address-search.php` - Search for addresses
- `api/address-details.php` - Get full address details

## Next Steps

1. Choose an API provider (AddressFinder recommended)
2. Register and get API key
3. Set API key in Hostinger environment variables
4. Test address autocomplete on both web and mobile
5. Monitor API usage and upgrade plan if needed

## Security Notes

- **Never commit API keys to Git**
- Use environment variables for API keys
- On Hostinger, set environment variables in cPanel
- For local testing, use `.env` file (add to `.gitignore`)

## Troubleshooting

**Issue:** No suggestions appearing
- Check API key is set correctly
- Verify API key is valid and has remaining requests
- Check browser console for errors
- Verify API endpoints are accessible

**Issue:** Address not filling correctly
- Check API response format matches expected structure
- Verify `api/address-details.php` is parsing response correctly
- Check network tab for API responses

**Issue:** API rate limits
- Monitor usage in provider dashboard
- Implement caching if needed
- Upgrade plan if approaching limits

