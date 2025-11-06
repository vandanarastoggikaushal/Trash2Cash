# Address Search Feature Setup

## Current Status
✅ **Address search is currently DISABLED** (no API key configured)

The address search field is hidden, but all code is preserved and ready to be enabled.

## To Enable Address Search

### Step 1: Get an API Key

Choose one of these options:

#### Option A: AddressFinder (Recommended - Easier Setup)
1. Register at: https://addressfinder.co.nz/
2. Get your API key from the dashboard
3. Set environment variable: `ADDRESSFINDER_API_KEY=your_key_here`

#### Option B: NZ Post Address API
1. Register at: https://www.nzpost.co.nz/business/ecommerce/shipping-apis
2. Get API credentials
3. Set environment variables: `NZ_POST_API_KEY` and `NZ_POST_API_SECRET`

### Step 2: Enable the Feature

Edit `includes/config.php` and change:

```php
define('ENABLE_ADDRESS_SEARCH', false);
```

to:

```php
define('ENABLE_ADDRESS_SEARCH', true);
```

### Step 3: Configure API Key

#### For AddressFinder:
Set the environment variable on your server:
```bash
export ADDRESSFINDER_API_KEY=your_key_here
```

Or add to your `.htaccess` or server config:
```apache
SetEnv ADDRESSFINDER_API_KEY your_key_here
```

#### For NZ Post:
Set environment variables:
```bash
export NZ_POST_API_KEY=your_key_here
export NZ_POST_API_SECRET=your_secret_here
```

### Step 4: Test

1. Go to `/schedule-pickup.php`
2. You should see the "🔍 Search Address" field
3. Type an address to test autocomplete

## Files Involved

- `includes/config.php` - Feature flag (`ENABLE_ADDRESS_SEARCH`)
- `api/address-search.php` - Search endpoint (supports both APIs)
- `api/address-details.php` - Address details endpoint
- `assets/schedule-pickup.js` - Frontend JavaScript (auto-handles disabled state)
- `schedule-pickup.php` - Form with conditional address search field

## Notes

- All code is preserved and ready to use
- The JavaScript automatically handles the disabled state (no errors if field doesn't exist)
- Users can still manually enter addresses in the form fields
- The feature will work immediately once enabled and API key is configured

