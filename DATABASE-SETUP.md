# MySQL Database Setup Guide

## Overview
This guide will help you set up MySQL database for Trash2Cash website and migrate from JSON file storage.

## Prerequisites
- ✅ Database created in Hostinger
- ✅ Database username and password
- ✅ Access to phpMyAdmin or command line

## Step 1: Get Database Credentials

1. Log into Hostinger hPanel
2. Go to **Databases** → **MySQL Databases**
3. Find your database and note:
   - **Database Name**
   - **Database Username**
   - **Database Password** (if you forgot, you can reset it)

## Step 2: Configure Database Connection

1. Copy the example config file:
   ```bash
   cp includes/db-config.php.example includes/db-config.php
   ```

2. Edit `includes/db-config.php` and fill in your credentials:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'your_database_name');
   define('DB_USER', 'your_database_user');
   define('DB_PASS', 'your_database_password');
   ```

3. **IMPORTANT**: Add `includes/db-config.php` to `.gitignore` to keep credentials secure!

## Step 3: Create Database Tables

### Option A: Using phpMyAdmin (Recommended)

1. Log into phpMyAdmin from Hostinger hPanel
2. Select your database from the left sidebar
3. Click on the **SQL** tab
4. Copy and paste the contents of `database/schema.sql`
5. Click **Go** to execute
6. Verify tables were created:
   - `users`
   - `leads`
   - `messages`
   - `sessions`

### Option B: Using Command Line

```bash
mysql -u your_username -p your_database_name < database/schema.sql
```

## Step 4: Test Database Connection

Create a test file `test-db.php`:

```php
<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';

if (testDBConnection()) {
    echo "✅ Database connection successful!";
} else {
    echo "❌ Database connection failed. Check your credentials.";
}
```

Visit it in your browser or run: `php test-db.php`

## Step 5: Migrate Existing Data

If you have existing JSON data, migrate it:

```bash
php database/migrate-json-to-mysql.php
```

This will:
- ✅ Migrate users from `data/users.json`
- ✅ Migrate leads from `data/leads.json`
- ✅ Migrate messages from `data/messages.json`
- ✅ Skip duplicates (safe to run multiple times)

**Important**: Backup your JSON files before migration!

## Step 6: Verify Migration

1. Check phpMyAdmin to see if data was migrated
2. Test login with existing user accounts
3. Verify leads and messages are accessible

## Step 7: Update Code (Already Done!)

The code has been updated to use MySQL. Once you complete steps 1-6, everything should work!

## Troubleshooting

### "Cannot connect to database"
- ✅ Check database credentials in `includes/db-config.php`
- ✅ Verify database exists in Hostinger
- ✅ Check if database user has proper permissions
- ✅ Ensure database host is correct (usually `localhost`)

### "Table doesn't exist"
- ✅ Run `database/schema.sql` in phpMyAdmin
- ✅ Verify you selected the correct database

### "Access denied"
- ✅ Check username and password
- ✅ Verify database user has access to the database
- ✅ In Hostinger, ensure user is assigned to the database

### Migration Errors
- ✅ Check database connection first
- ✅ Verify tables exist
- ✅ Check file permissions on JSON files
- ✅ Review error messages in migration output

## Security Best Practices

1. ✅ **Never commit `db-config.php`** - Add to `.gitignore`
2. ✅ Use strong database passwords
3. ✅ Limit database user permissions (only what's needed)
4. ✅ Keep database credentials secure
5. ✅ Regular database backups

## Database Structure

### Tables Created:

- **users** - User accounts and authentication
- **leads** - Pickup request submissions
- **messages** - Contact form submissions
- **sessions** - Session management (optional)

## Backup Strategy

### Manual Backup via phpMyAdmin:
1. Select your database
2. Click **Export** tab
3. Choose **Quick** or **Custom** method
4. Click **Go** to download SQL file

### Automated Backup (Recommended):
Set up automated backups in Hostinger hPanel or use a cron job.

## Next Steps

After setup:
1. ✅ Test all functionality (login, registration, forms)
2. ✅ Verify data integrity
3. ✅ Set up regular backups
4. ✅ Monitor database performance
5. ✅ Keep JSON files as backup for a while

## Support

If you encounter issues:
1. Check error logs
2. Verify database credentials
3. Test connection with `test-db.php`
4. Review migration script output

---

**Note**: The code automatically falls back gracefully if database is not configured, so your site will continue working during migration.

