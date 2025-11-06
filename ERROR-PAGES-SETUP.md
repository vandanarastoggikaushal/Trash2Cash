# Custom Error Pages Setup

## Overview
Trash2Cash now has custom branded error pages that match the site's design instead of default server error pages.

## Error Pages Created

### 1. 404.php - Page Not Found
- **Triggered when:** User visits a page that doesn't exist
- **Features:**
  - Branded design matching Trash2Cash style
  - Helpful navigation links
  - Contact information

### 2. 500.php - Server Error
- **Triggered when:** PHP fatal errors occur
- **Features:**
  - User-friendly error message
  - Refresh button
  - Error reference ID for support
  - Helpful troubleshooting tips

### 3. 403.php - Access Forbidden
- **Triggered when:** User tries to access restricted content
- **Features:**
  - Login prompt
  - Navigation to homepage
  - Contact information

### 4. 503.php - Service Unavailable
- **Triggered when:** Site is under maintenance
- **Features:**
  - Maintenance message
  - Refresh option
  - Contact information

## Configuration

### .htaccess Configuration
The `.htaccess` file has been updated with:
```apache
ErrorDocument 404 /404.php
ErrorDocument 403 /403.php
ErrorDocument 500 /500.php
ErrorDocument 503 /503.php
```

### PHP Error Handler
The `includes/error-handler.php` file:
- Catches PHP fatal errors
- Catches uncaught exceptions
- Displays branded 500 error page
- Prevents infinite loops
- Logs all errors for debugging

### Auto-Loading
Error handler is automatically loaded via `includes/config.php`, so it's active on all pages.

## Testing Error Pages

### Test 404 Page
Visit a non-existent page:
```
https://yourdomain.com/this-page-does-not-exist
```

### Test 500 Page
Visit the 500 page directly:
```
https://yourdomain.com/500.php
```

### Test 403 Page
Visit the 403 page directly:
```
https://yourdomain.com/403.php
```

### Test 503 Page
Visit the 503 page directly:
```
https://yourdomain.com/503.php
```

## Features

✅ **Branded Design** - Matches Trash2Cash website style  
✅ **User-Friendly** - Clear messages and helpful links  
✅ **Navigation** - Quick links to important pages  
✅ **Contact Info** - Easy way to get help  
✅ **Error Logging** - All errors logged for debugging  
✅ **Infinite Loop Prevention** - Prevents error handler from causing issues  

## Customization

You can customize the error pages by editing:
- `404.php` - Page not found
- `500.php` - Server errors
- `403.php` - Access forbidden
- `503.php` - Service unavailable

All pages use the same header/footer as the rest of the site, so they automatically match your branding.

## Notes

- Error pages are automatically used by Apache via `.htaccess`
- PHP errors are caught by the error handler
- All errors are logged to the server error log
- Error pages won't cause infinite loops (safety checks in place)

## Files

- `404.php` - 404 error page
- `500.php` - 500 error page
- `403.php` - 403 error page
- `503.php` - 503 error page
- `includes/error-handler.php` - PHP error handler
- `.htaccess` - Apache error document configuration

