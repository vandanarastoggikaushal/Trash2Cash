# Troubleshooting Email Issues

## Common Issues

### 1. "Network error" or "Failed to fetch"
**Problem:** The app can't connect to the API server.

**Solutions:**
- **If testing on emulator/simulator:** Make sure PHP server is running on `localhost:8000`
- **If testing on physical device:** 
  - Replace `localhost` with your computer's IP address in `src/config/api.js`
  - Find your IP:
    - Windows: Open Command Prompt and run `ipconfig` (look for IPv4 Address)
    - Mac/Linux: Run `ifconfig` or `ip addr`
  - Update the API_BASE_URL to: `http://YOUR_IP:8000`
  - Make sure your phone and computer are on the same WiFi network
  - Make sure Windows Firewall allows PHP server connections

### 2. "Email not sent" but form submission succeeds
**Problem:** The API call succeeds but email isn't delivered.

**Check:**
1. PHP server logs (check terminal where PHP is running)
2. PHP error logs (usually in `php.ini` location or server logs)
3. Email might be in spam folder
4. On Hostinger, check email delivery logs in cPanel

### 3. 400 Bad Request Error
**Problem:** The request format is incorrect.

**Solutions:**
- Check console logs in Expo Go (shake device → "Show Debugger")
- Verify all required fields are filled
- Check that JSON payload is valid

### 4. CORS Errors
**Problem:** Cross-origin request blocked.

**Solutions:**
- PHP endpoints already have `Access-Control-Allow-Origin: *` headers
- If still seeing CORS errors, check PHP server is running correctly

## Testing Steps

1. **Test API connectivity:**
   - Open Expo Go
   - Shake device → "Show Debugger"
   - Check console for `[API]` logs
   - Look for connection errors

2. **Test with PHP server:**
   - Make sure PHP server is running: `php -S localhost:8000`
   - Test endpoint directly in browser: `http://localhost:8000/api/contact`
   - Should see "Method not allowed" (expected for GET request)

3. **Test email delivery:**
   - Submit a test form
   - Check PHP server console for email logs
   - Check email inbox (and spam folder)
   - On Hostinger, check mail delivery logs

## Debug Mode

The app now includes detailed logging:
- All API calls are logged to console with `[API]` prefix
- Error messages show specific connection issues
- Check Expo Go debugger console for detailed logs

## Production Setup

When deploying to production:
1. Update `src/config/api.js`:
   ```javascript
   const API_BASE_URL = 'https://trash2cash.co.nz';
   ```
2. Make sure PHP endpoints are accessible
3. Test email delivery on production server
4. Check Hostinger email configuration

