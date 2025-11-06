<?php
/**
 * Authentication Helper Functions
 * Handles user login, logout, session management, and user storage
 * Uses MySQL database (with JSON fallback if database not configured)
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load database helper if available
$dbFile = __DIR__ . '/db.php';
if (file_exists($dbFile)) {
    require_once $dbFile;
}

// User data file (fallback if database not available)
define('USERS_FILE', __DIR__ . '/../data/users.json');

/**
 * Check if database is available
 * @return bool
 */
function isDatabaseAvailable() {
    return function_exists('getDB') && getDB() !== null;
}

/**
 * Get all users from storage (database or JSON fallback)
 * @return array Array of users
 */
function getUsers() {
    if (isDatabaseAvailable()) {
        $users = dbQuery("SELECT id, username, password, email, role, created_at, last_login FROM users");
        if ($users !== false) {
            return $users;
        }
    }
    
    // Fallback to JSON
    if (!file_exists(USERS_FILE)) {
        return [];
    }
    $content = file_get_contents(USERS_FILE);
    $users = json_decode($content, true);
    return is_array($users) ? $users : [];
}

/**
 * Create a new user
 * @param string $username Username
 * @param string $password Plain text password (will be hashed)
 * @param string $email User email
 * @param string $role User role (default: 'user')
 * @return array|false User data on success, false on failure
 */
function createUser($username, $password, $email = '', $role = 'user') {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $userId = bin2hex(random_bytes(8));
    $createdAt = gmdate('Y-m-d H:i:s');
    
    if (isDatabaseAvailable()) {
        // Use database
        $sql = "INSERT INTO users (id, username, password, email, role, created_at) 
                VALUES (:id, :username, :password, :email, :role, :created_at)";
        $params = [
            ':id' => $userId,
            ':username' => $username,
            ':password' => $hashedPassword,
            ':email' => $email ?: null,
            ':role' => $role,
            ':created_at' => $createdAt
        ];
        
        if (dbExecute($sql, $params) !== false) {
            return [
                'id' => $userId,
                'username' => $username,
                'email' => $email,
                'role' => $role,
                'createdAt' => $createdAt,
                'lastLogin' => null
            ];
        }
        return false;
    }
    
    // Fallback to JSON
    $users = getUsers();
    
    // Check if username already exists
    foreach ($users as $user) {
        if ($user['username'] === $username) {
            return false; // Username already exists
        }
    }
    
    // Create new user
    $newUser = [
        'id' => $userId,
        'username' => $username,
        'password' => $hashedPassword,
        'email' => $email,
        'role' => $role,
        'createdAt' => gmdate('c'),
        'lastLogin' => null
    ];
    
    $users[] = $newUser;
    
    $dir = dirname(USERS_FILE);
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    
    if (file_put_contents(USERS_FILE, json_encode($users, JSON_PRETTY_PRINT)) !== false) {
        unset($newUser['password']);
        return $newUser;
    }
    
    return false;
}

/**
 * Verify user credentials
 * @param string $username Username
 * @param string $password Plain text password
 * @return array|false User data on success, false on failure
 */
function verifyUser($username, $password) {
    if (isDatabaseAvailable()) {
        // Use database
        $user = dbQueryOne(
            "SELECT id, username, password, email, role, created_at, last_login FROM users WHERE username = :username",
            [':username' => $username]
        );
        
        if ($user && password_verify($password, $user['password'])) {
            // Update last login
            dbExecute(
                "UPDATE users SET last_login = :last_login WHERE id = :id",
                [
                    ':last_login' => gmdate('Y-m-d H:i:s'),
                    ':id' => $user['id']
                ]
            );
            
            // Format for compatibility
            return [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'role' => $user['role'],
                'createdAt' => $user['created_at'],
                'lastLogin' => gmdate('c')
            ];
        }
        return false;
    }
    
    // Fallback to JSON
    $users = getUsers();
    
    foreach ($users as $user) {
        if ($user['username'] === $username) {
            if (password_verify($password, $user['password'])) {
                // Update last login
                $user['lastLogin'] = gmdate('c');
                $users = array_map(function($u) use ($user) {
                    if ($u['id'] === $user['id']) {
                        return $user;
                    }
                    return $u;
                }, $users);
                
                $dir = dirname(USERS_FILE);
                if (!is_dir($dir)) {
                    @mkdir($dir, 0755, true);
                }
                file_put_contents(USERS_FILE, json_encode($users, JSON_PRETTY_PRINT));
                
                unset($user['password']);
                return $user;
            }
            return false; // Wrong password
        }
    }
    
    return false; // User not found
}

/**
 * Login user
 * @param string $username Username
 * @param string $password Plain text password
 * @return array|false User data on success, false on failure
 */
function login($username, $password) {
    $user = verifyUser($username, $password);
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_role'] = $user['role'] ?? 'user';
        $_SESSION['user_email'] = $user['email'] ?? '';
        return $user;
    }
    return false;
}

/**
 * Logout user
 */
function logout() {
    $_SESSION = [];
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    session_destroy();
}

/**
 * Check if user is logged in
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get current user data
 * @return array|null User data or null if not logged in
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    if (isDatabaseAvailable()) {
        $user = dbQueryOne(
            "SELECT id, username, email, role, created_at, last_login FROM users WHERE id = :id",
            [':id' => $_SESSION['user_id']]
        );
        
        if ($user) {
            return [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'role' => $user['role'],
                'createdAt' => $user['created_at'],
                'lastLogin' => $user['last_login']
            ];
        }
        return null;
    }
    
    // Fallback to JSON
    $users = getUsers();
    foreach ($users as $user) {
        if ($user['id'] === $_SESSION['user_id']) {
            unset($user['password']);
            return $user;
        }
    }
    
    return null;
}

/**
 * Require login - redirect to login page if not logged in
 * @param string $redirectTo URL to redirect to after login (optional)
 */
function requireLogin($redirectTo = null) {
    if (!isLoggedIn()) {
        $redirect = $redirectTo ? '?redirect=' . urlencode($redirectTo) : '';
        header('Location: /login.php' . $redirect);
        exit;
    }
}

/**
 * Require role - redirect if user doesn't have required role
 * @param string|array $requiredRole Required role(s)
 * @param string $redirectTo URL to redirect to (optional)
 */
function requireRole($requiredRole, $redirectTo = '/') {
    if (!isLoggedIn()) {
        requireLogin($redirectTo);
        return;
    }
    
    $userRole = $_SESSION['user_role'] ?? 'user';
    $roles = is_array($requiredRole) ? $requiredRole : [$requiredRole];
    
    if (!in_array($userRole, $roles)) {
        header('Location: ' . $redirectTo);
        exit;
    }
}

/**
 * Check if user has role
 * @param string|array $requiredRole Required role(s)
 * @return bool
 */
function hasRole($requiredRole) {
    if (!isLoggedIn()) {
        return false;
    }
    
    $userRole = $_SESSION['user_role'] ?? 'user';
    $roles = is_array($requiredRole) ? $requiredRole : [$requiredRole];
    
    return in_array($userRole, $roles);
}
