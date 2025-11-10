# Trash2Cash NZ - PHP Static Site Deployment Guide

## Overview
This is a PHP-based static website for Trash2Cash NZ, designed to run on Hostinger web hosting or any PHP-enabled server.

## Project Structure
```
/
├── index.php              # Homepage
├── how-it-works.php       # How it works page (served at /how-it-works)
├── rewards.php            # Rewards page (served at /rewards)
├── schedule-pickup.php    # Schedule pickup form (served at /schedule-pickup)
├── contact.php            # Contact form (served at /contact)
├── faq.php               # FAQ page (served at /faq)
├── partners.php          # Partners page (served at /partners)
├── terms.php             # Terms page (served at /terms)
├── privacy.php           # Privacy policy (served at /privacy)
├── includes/             # PHP includes
│   ├── header.php        # Site header
│   ├── footer.php        # Site footer
│   └── config.php        # Configuration constants
├── api/                  # API endpoints
│   ├── lead.php          # Lead submission endpoint (/api/lead)
│   └── contact.php       # Contact form endpoint (/api/contact)
├── assets/               # Static assets
│   ├── styles.css        # Custom CSS
│   ├── main.js           # Main JavaScript
│   ├── contact.js        # Contact form JS
│   └── schedule-pickup.js # Pickup form JS
├── data/                 # Data storage (JSON files)
│   ├── leads.json        # Submitted leads
│   └── messages.json     # Contact messages
└── .htaccess             # Apache configuration
```

## Deployment to Hostinger

### Step 1: Upload Files
1. Connect to your Hostinger hosting via FTP/SFTP or File Manager
2. Navigate to `public_html` (or your domain's root directory)
3. Upload all project files maintaining the directory structure

### Step 2: Set Permissions
Ensure the `data/` directory is writable:
```bash
chmod 755 data/
chmod 644 data/*.json (if files exist)
```

Or via File Manager:
- Right-click `data/` folder → Permissions → 755

### Step 3: Configure Data Directory (Optional)
If your hosting restricts write access to the default `data/` directory, create a `.env` file or set an environment variable:
```php
// In includes/config.php, you can override the data directory:
$DATA_DIR = '/home/username/apps/trash2cash/data';
```

### Step 4: Test
1. Visit your domain: `https://yourdomain.com`
2. Test the contact form
3. Test the schedule pickup form
4. Check that data is being saved to `data/leads.json` and `data/messages.json`

## Features

### Static Pages
- All pages use PHP includes for header/footer
- No database required
- All content is server-side rendered

### Forms
- Contact form (`/contact`)
- Schedule pickup form (`/schedule-pickup`)
- Both submit to PHP API endpoints
- Data stored as JSON files in `data/` directory

### API Endpoints
- `/api/lead` - Handles pickup lead submissions
- `/api/contact` - Handles contact form submissions

Both endpoints:
- Accept POST requests
- Validate input
- Store data in JSON files
- Return JSON responses

## Configuration

Edit `includes/config.php` to customize:
- Company name, email, phone
- Service areas
- Appliance credits
- Site URL and metadata

## Customization

### Styling
- Tailwind CSS is loaded via CDN (in `includes/header.php`)
- Custom styles in `assets/styles.css`
- Brand colors defined in Tailwind config in header

### Adding Pages
1. Create new `pagename.php` file
2. Set `$pageTitle` and `$pageDescription` variables
3. Include `header.php` and `footer.php`

Example:
```php
<?php
$pageTitle = 'My Page';
$pageDescription = 'Page description';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/header.php';
?>
<!-- Your content here -->
<?php require_once __DIR__ . '/includes/footer.php'; ?>
```

## Maintenance

### Viewing Submissions
Check the JSON files:
- `data/leads.json` - All pickup requests
- `data/messages.json` - All contact form submissions

### Email Integration
Currently, emails are logged to PHP error log. To enable actual email sending:

1. Edit `api/lead.php` and `api/contact.php`
2. Add email sending code (using PHP `mail()` or a library like PHPMailer)

Example:
```php
mail(
  'hello@trash2cash.nz',
  'New Lead Submission',
  'Lead details: ' . json_encode($data),
  'From: noreply@yourdomain.com'
);
```

## Security Notes

1. **File Permissions**: Ensure `data/` directory is not publicly accessible
2. **Input Validation**: All forms validate on both client and server side
3. **Rate Limiting**: Consider adding rate limiting to API endpoints in production
4. **HTTPS**: Ensure SSL is enabled on your hosting

## Troubleshooting

### Forms not submitting
- Check PHP error logs
- Verify `data/` directory permissions
- Check API endpoint URLs in JavaScript files

### Styles not loading
- Verify Tailwind CDN is accessible
- Check browser console for errors
- Ensure `assets/styles.css` exists

### Navigation not highlighting
- Check that `basename($_SERVER['PHP_SELF'])` returns correct filename
- Verify page file names match navigation links

## Next Steps

1. Set up email notifications for form submissions
2. Add admin panel to view submissions
3. Implement rate limiting
4. Add analytics tracking
5. Optimize images and assets

