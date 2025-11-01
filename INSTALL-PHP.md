# PHP Installation Guide for Windows

## Option 1: Manual Installation (Recommended for Development)

### Step 1: Download PHP
1. Go to https://windows.php.net/download/
2. Download the latest PHP 8.x "Thread Safe" ZIP file (e.g., `php-8.3.x-Win32-vs16-x64.zip`)
3. Extract the ZIP file to `C:\php` (or any location you prefer)

### Step 2: Add PHP to PATH (Optional but Recommended)
1. Open "Environment Variables":
   - Press `Win + R`, type `sysdm.cpl`, press Enter
   - Click "Advanced" tab → "Environment Variables"
   - Under "User variables", find "Path" and click "Edit"
   - Click "New" and add: `C:\php` (or your PHP path)
   - Click OK on all dialogs
2. Restart PowerShell/Command Prompt

### Step 3: Verify Installation
Open a new PowerShell window and run:
```powershell
php --version
```

### Step 4: Configure PHP (Optional)
1. Rename `C:\php\php.ini-development` to `C:\php\php.ini`
2. Edit `php.ini` and enable extensions you need (e.g., uncomment `extension=mbstring`, `extension=openssl`, etc.)

---

## Option 2: Using Chocolatey (Requires Admin)

### Step 1: Install Chocolatey (Run PowerShell as Administrator)
1. Right-click PowerShell → "Run as Administrator"
2. Run this command:
```powershell
Set-ExecutionPolicy Bypass -Scope Process -Force; [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072; iex ((New-Object System.Net.WebClient).DownloadString('https://community.chocolatey.org/install.ps1'))
```

### Step 2: Install PHP
After Chocolatey is installed, run:
```powershell
choco install php -y
```

### Step 3: Verify Installation
```powershell
php --version
```

---

## Option 3: XAMPP (Includes PHP + Apache + MySQL)

1. Download XAMPP from: https://www.apachefriends.org/
2. Install XAMPP (includes PHP automatically)
3. PHP will be at: `C:\xampp\php` (usually added to PATH automatically)

---

## For This Project

Once PHP is installed, you can test your PHP files locally:

1. Navigate to your project directory:
```powershell
cd C:\Working\Code\Trash2Cash
```

2. Start PHP's built-in server:
```powershell
php -S localhost:8000
```

3. Open browser: http://localhost:8000

---

## Troubleshooting

If `php --version` doesn't work after installation:
- Make sure you restarted PowerShell/Command Prompt after adding to PATH
- Try using full path: `C:\php\php.exe --version`
- Check if PHP is in PATH: `$env:PATH` (PowerShell) or `echo %PATH%` (CMD)

