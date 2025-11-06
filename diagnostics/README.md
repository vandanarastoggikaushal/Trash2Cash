# Diagnostics Tools

This folder contains diagnostic and debugging tools for the Trash2Cash website. **These files should only be accessed by authorized personnel.**

## 🔒 Security

**IMPORTANT:** This folder is protected. Access should be restricted using one of the following methods:

1. **Password Protection (Recommended)**
   - Set up password protection in Hostinger hPanel
   - Go to File Manager → Select `diagnostics` folder → Password Protect Directory
   - Or configure `.htaccess` with Basic Authentication

2. **IP Restriction**
   - Edit `.htaccess` to allow only specific IP addresses
   - Useful if you always access from the same location

3. **Complete Block**
   - Deny all access via `.htaccess`
   - Access files only via FTP/SSH when needed

## 📁 Files

### `check-config.php`
**Purpose:** Comprehensive database configuration diagnostic tool

**What it checks:**
- Database configuration file existence and contents
- PHP syntax errors
- Database constants (DB_HOST, DB_NAME, DB_USER, DB_PASS)
- File permissions
- Database connection test
- PDO extension availability
- Direct PDO connection test

**Usage:** Visit `https://yourdomain.com/diagnostics/check-config.php` in your browser

**When to use:**
- After setting up database configuration
- When experiencing database connection issues
- To verify database credentials are correct

---

### `test-db.php`
**Purpose:** Simple database connection test

**What it checks:**
- Database configuration file exists
- Database credentials are set
- Database connection works
- Database tables exist
- Lists all tables in the database

**Usage:** Visit `https://yourdomain.com/diagnostics/test-db.php` in your browser

**When to use:**
- Quick database connection verification
- After creating database tables
- To check if required tables exist

---

### `debug-500.php`
**Purpose:** Comprehensive 500 error diagnostic tool

**What it checks:**
- PHP version and extensions
- File system checks (config, db, auth files)
- Database configuration
- Database connection
- PHP syntax errors in key files
- File include tests
- Server information
- Error log locations

**Usage:** Visit `https://yourdomain.com/diagnostics/debug-500.php` in your browser

**When to use:**
- When experiencing 500 Internal Server Errors
- After deploying new code
- To diagnose server configuration issues
- To check PHP extensions

---

### `check-errors.php`
**Purpose:** Quick command-line error check

**What it checks:**
- Syntax errors in key PHP files
- Config file loading

**Usage:** Run via command line: `php diagnostics/check-errors.php`

**When to use:**
- Quick syntax check before deployment
- Command-line debugging
- Automated testing scripts

---

## 🛠️ Setup Instructions

### 1. Protect the Folder

**Option A: Hostinger hPanel (Easiest)**
1. Log into Hostinger hPanel
2. Go to File Manager
3. Navigate to `public_html/diagnostics`
4. Right-click the folder → Password Protect Directory
5. Set username and password
6. Save

**Option B: .htaccess Configuration**
1. Edit `diagnostics/.htaccess`
2. Uncomment and configure one of the protection methods
3. For password protection, create `.htpasswd` file (use online generator)
4. Upload `.htpasswd` to a secure location outside public_html

### 2. Test Access

After setting up protection:
1. Try accessing a diagnostic file
2. You should be prompted for credentials (if password protected)
3. Or see 403 Forbidden (if IP restricted or denied)

## 📝 Notes

- **Never commit diagnostic files to public repositories** - they contain sensitive information
- **Delete diagnostic output** after fixing issues
- **Keep this folder protected** at all times
- **Use these tools only when needed** - don't leave them accessible permanently

## 🔗 Related Documentation

- `../DATABASE-SETUP.md` - Database setup instructions
- `../TROUBLESHOOTING-500-ERROR.md` - 500 error troubleshooting guide
- `../ERROR-PAGES-SETUP.md` - Custom error pages setup

## ⚠️ Security Reminders

1. ✅ Password protect this folder
2. ✅ Restrict access to authorized IPs if possible
3. ✅ Review diagnostic output for sensitive information
4. ✅ Delete or secure diagnostic output files
5. ✅ Keep diagnostic tools updated
6. ✅ Monitor access logs for unauthorized access attempts

