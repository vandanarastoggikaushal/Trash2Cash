# Authentication System Setup

## Overview
The website now has a complete user authentication system with login, registration, and session management.

## Features

✅ **User Registration** - Users can create accounts with username/password  
✅ **User Login** - Secure login with session management  
✅ **User Logout** - Secure logout functionality  
✅ **Session Management** - PHP sessions with secure storage  
✅ **Password Hashing** - Passwords are hashed using PHP's `password_hash()`  
✅ **Protected Pages** - Easy to protect pages with `requireLogin()`  
✅ **Role-Based Access** - Support for user roles (user, admin, etc.)  
✅ **User Dashboard** - Example protected page showing user info  

## Files Created

### Core Authentication
- `includes/auth.php` - Authentication helper functions
- `login.php` - Login page
- `register.php` - Registration page
- `api/logout.php` - Logout endpoint
- `dashboard.php` - Example protected page

### Data Storage
- `data/users.json` - User data storage (created automatically)

## Usage

### Creating a User Account

1. Visit `/register.php`
2. Fill in username, email (optional), and password
3. Click "Create Account"
4. You'll be automatically logged in

### Logging In

1. Visit `/login`
2. Enter username and password
3. Click "Sign In"
4. You'll be redirected to the dashboard or the page you were trying to access

### Logging Out

- Click "Logout" in the navigation menu
- Or visit `/api/logout`

### Protecting Pages

To protect a page, add at the top (after including config):

```php
require_once __DIR__ . '/includes/auth.php';
requireLogin(); // Redirects to login if not logged in
```

### Requiring Specific Roles

```php
requireRole('admin'); // Only admins can access
// or
requireRole(['admin', 'moderator']); // Multiple roles
```

### Checking Login Status

```php
if (isLoggedIn()) {
    $user = getCurrentUser();
    echo "Welcome, " . $user['username'];
}
```

### Checking Roles

```php
if (hasRole('admin')) {
    // Show admin features
}
```

## Creating an Admin User

To create an admin user, you can either:

### Option 1: Create via PHP script

Create a file `create-admin.php` (temporary, delete after use):

```php
<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';

$username = 'admin';
$password = 'your-secure-password';
$email = 'admin@trash2cash.co.nz';
$address = "123 Example Street\nExample Suburb\nWellington 6011";
$profile = [
    'phone' => '0212345678',
    'marketingOptIn' => true,
    'payoutMethod' => 'bank',
    'payoutBankName' => 'Example Bank',
    'payoutBankAccount' => '12-3456-7890123-00'
];

$user = createUser($username, $password, $email, 'admin', 'Admin', 'User', $address, $profile);
if ($user) {
    echo "Admin user created successfully!";
} else {
    echo "Failed to create admin user (username might already exist)";
}
```

Run it once, then delete the file.

### Option 2: Register normally, then edit `data/users.json`

1. Register a user normally
2. Edit `data/users.json`
3. Find your user and change `"role": "user"` to `"role": "admin"`

## Security Features

- ✅ Passwords are hashed using `password_hash()` (bcrypt)
- ✅ Sessions are managed securely
- ✅ SQL injection protection (using JSON storage, not SQL)
- ✅ XSS protection (using `htmlspecialchars()`)
- ✅ CSRF protection (can be added if needed)

## Navigation

The header navigation automatically shows:
- **When logged out**: "Login" link
- **When logged in**: Username and "Logout" link

## User Data Structure

Users are stored in `data/users.json`:

```json
[
  {
    "id": "unique-id",
    "username": "username",
    "password": "$2y$10$hashed...",
    "firstName": "Alex",
    "lastName": "Example",
    "address": "123 Example Street\nExample Suburb\nWellington 6011",
    "phone": "0212345678",
    "marketingOptIn": true,
    "payoutMethod": "bank",
    "payoutBankName": "Example Bank",
    "payoutBankAccount": "12-3456-7890123-00",
    "email": "user@example.com",
    "role": "user",
    "createdAt": "2024-01-15T10:30:00+00:00",
    "lastLogin": "2024-01-15T12:00:00+00:00"
  }
]
```

## Next Steps

1. **Create your first user** - Visit `/register.php`
2. **Create an admin user** - Use one of the methods above
3. **Protect pages** - Add `requireLogin()` to pages that need authentication
4. **Customize dashboard** - Edit `dashboard.php` to show user-specific data
5. **Add features** - Build user-specific features like order history, profile editing, etc.

## Notes

- User data is stored in JSON files (no database required)
- Sessions are stored server-side
- The system is ready for production but consider adding:
  - Password reset functionality
  - Email verification
  - Remember me functionality
  - Two-factor authentication (for admins)

