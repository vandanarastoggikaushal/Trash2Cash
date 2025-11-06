# Troubleshooting 500 Error After Database Configuration

## Common Causes & Solutions

### 1. ✅ Syntax Error in db-config.php (FIXED)
**Issue:** Text after closing comment `*/`  
**Fix:** Already fixed - make sure you upload the corrected file

### 2. Database Connection Fails
**Symptoms:** 500 error when database is accessed  
**Solutions:**
- Check database credentials are correct
- Verify database exists in Hostinger
- Ensure database user has permissions
- Check if database host is correct (might not be 'localhost' on Hostinger)

### 3. Database Tables Don't Exist
**Symptoms:** 500 error when trying to query tables  
**Solution:** Run `database/schema.sql` in phpMyAdmin to create tables

### 4. PDO Extension Not Available
**Symptoms:** Fatal error about PDO class not found  
**Solution:** Contact Hostinger support to enable PDO extension (usually enabled by default)

### 5. File Not Uploaded
**Symptoms:** Config file missing on server  
**Solution:** Upload `includes/db-config.php` to server via FTP/SFTP

### 6. File Permissions
**Symptoms:** Cannot read config file  
**Solution:** Set file permissions to 644:
```bash
chmod 644 includes/db-config.php
```

### 7. PHP Version Issues
**Symptoms:** Syntax errors on older PHP versions  
**Solution:** Check PHP version in Hostinger (should be 7.4+)

## Quick Diagnostic Steps

### Step 1: Upload debug-500.php
1. Upload `debug-500.php` to your server
2. Visit it in browser: `https://yourdomain.com/debug-500.php`
3. Review the diagnostic information

### Step 2: Check Error Logs
1. Log into Hostinger hPanel
2. Go to **Error Log** section
3. Look for recent PHP errors
4. Common errors:
   - `Parse error` = Syntax error
   - `Fatal error: Class 'PDO' not found` = PDO extension missing
   - `Access denied for user` = Wrong database credentials
   - `Unknown database` = Database doesn't exist

### Step 3: Test Database Connection
1. Upload `test-db.php` to server
2. Visit: `https://yourdomain.com/test-db.php`
3. Check if connection works

### Step 4: Verify Files Uploaded
Check these files exist on server:
- ✅ `includes/db-config.php`
- ✅ `includes/db.php`
- ✅ `includes/auth.php`
- ✅ `database/schema.sql`

### Step 5: Create Database Tables
1. Open phpMyAdmin in Hostinger
2. Select your database
3. Go to SQL tab
4. Copy/paste contents of `database/schema.sql`
5. Click Go

## Temporary Fix: Disable Database

If you need the site working immediately while debugging:

1. **Rename db-config.php temporarily:**
   ```bash
   mv includes/db-config.php includes/db-config.php.bak
   ```
   This will make the site fall back to JSON file storage.

2. **Or comment out database loading in header.php:**
   Temporarily disable the auth.php loading in header.php

## Most Likely Issues on Hostinger

1. **Database host might not be 'localhost'**
   - Check Hostinger database details
   - Might be something like `localhost:3306` or a different host

2. **Database tables don't exist yet**
   - Must run schema.sql first

3. **PDO extension disabled**
   - Usually enabled, but check with Hostinger support

4. **File permissions**
   - Config file needs to be readable (644)

## Next Steps

1. ✅ Upload corrected `db-config.php` (syntax error fixed)
2. ✅ Upload `debug-500.php` and check diagnostics
3. ✅ Check Hostinger error logs
4. ✅ Run `database/schema.sql` in phpMyAdmin
5. ✅ Test with `test-db.php`

## Getting More Help

If still having issues, check:
- Hostinger error logs (most important!)
- PHP error log location
- Database connection details in Hostinger hPanel
- PHP version (should be 7.4+)

