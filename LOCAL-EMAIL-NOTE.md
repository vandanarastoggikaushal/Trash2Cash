# Local Development Email Note

## Important: Email Testing on Local Server

When testing the mobile app locally (using `php -S 192.168.1.11:8000`), **emails will NOT actually be sent**.

### Why?

PHP's built-in development server doesn't have a mail server configured. The `mail()` function will return `true` (success), but the emails won't actually be delivered.

### What Happens Locally?

- ✅ Form submissions work correctly
- ✅ Data is saved to JSON files (`data/messages.json`, `data/leads.json`)
- ✅ API returns success response
- ❌ Emails are NOT sent (they're just logged to console)

### What You'll See

When testing locally, check the PHP server console output. You'll see logs like:
```
[email] LOCAL DEV - Email would be sent to: collect@trash2cash.co.nz
[email] LOCAL DEV - Subject: New Contact Form Message from...
```

### Testing Email Functionality

To test actual email sending, you need to:

1. **Deploy to Hostinger** - The emails will work correctly on production
2. **Use a local mail testing tool** like MailHog or MailCatcher
3. **Configure SMTP** in PHP (requires additional setup)

### Production

When deployed to Hostinger, emails **will be sent** to `collect@trash2cash.co.nz` automatically.

The code detects whether it's running locally or on production and handles email sending accordingly.

