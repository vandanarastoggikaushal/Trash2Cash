<?php
/**
 * Authentication Helper Functions
 * Handles user login, logout, session management, and user storage
 * Uses MySQL database (with JSON fallback if database not configured)
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

// Load database helper if available (suppress errors if it fails)
$dbFile = __DIR__ . '/db.php';
if (file_exists($dbFile)) {
    try {
        require_once $dbFile;
    } catch (Exception $e) {
        // Database file exists but has errors - log but don't break the site
        error_log('Error loading db.php: ' . $e->getMessage());
    }
}

// User data file (fallback if database not available)
define('USERS_FILE', __DIR__ . '/../data/users.json');

/**
 * Migrate users from JSON to database
 * This function automatically migrates users created during backup mode
 * @return array Statistics about migration (migrated, skipped, errors)
 */
function migrateUsersToDatabase() {
    if (!isDatabaseAvailable()) {
        return ['migrated' => 0, 'skipped' => 0, 'errors' => 0, 'message' => 'Database not available'];
    }
    
    // Check if JSON file exists
    if (!file_exists(USERS_FILE)) {
        return ['migrated' => 0, 'skipped' => 0, 'errors' => 0, 'message' => 'No JSON users file found'];
    }
    
    $jsonUsers = json_decode(file_get_contents(USERS_FILE), true);
    if (!is_array($jsonUsers) || empty($jsonUsers)) {
        return ['migrated' => 0, 'skipped' => 0, 'errors' => 0, 'message' => 'No users in JSON file'];
    }
    
    // Get existing users from database
    $dbUsers = dbQuery("SELECT username FROM users");
    $existingUsernames = [];
    if ($dbUsers !== false) {
        foreach ($dbUsers as $dbUser) {
            $existingUsernames[$dbUser['username']] = true;
        }
    }
    
    $migrated = 0;
    $skipped = 0;
    $errors = 0;
    
    foreach ($jsonUsers as $user) {
        // Skip if user already exists in database
        if (isset($existingUsernames[$user['username']])) {
            $skipped++;
            continue;
        }
        
        try {
            // Convert JSON date format to MySQL format
            $createdAt = $user['createdAt'] ?? gmdate('Y-m-d H:i:s');
            if (strpos($createdAt, 'T') !== false) {
                // ISO 8601 format (from JSON)
                $createdAt = date('Y-m-d H:i:s', strtotime($createdAt));
            }
            
            $lastLogin = null;
            if (!empty($user['lastLogin'])) {
                $lastLogin = $user['lastLogin'];
                if (strpos($lastLogin, 'T') !== false) {
                    $lastLogin = date('Y-m-d H:i:s', strtotime($lastLogin));
                }
            }
            
            $sql = "INSERT INTO users (id, username, password, email, role, created_at, last_login, first_name, last_name, address) 
                    VALUES (:id, :username, :password, :email, :role, :created_at, :last_login, :first_name, :last_name, :address)";
            
            $params = [
                ':id' => $user['id'] ?? bin2hex(random_bytes(8)),
                ':username' => $user['username'],
                ':password' => $user['password'], // Already hashed
                ':email' => !empty($user['email']) ? $user['email'] : null,
                ':role' => $user['role'] ?? 'user',
                ':created_at' => $createdAt,
                ':last_login' => $lastLogin,
                ':first_name' => $user['firstName'] ?? ($user['first_name'] ?? null),
                ':last_name' => $user['lastName'] ?? ($user['last_name'] ?? null),
                ':address' => $user['address'] ?? null
            ];
            
            $result = dbExecute($sql, $params);
            if ($result !== false) {
                $migrated++;
                // Add to existing list to avoid duplicates in same run
                $existingUsernames[$user['username']] = true;
                error_log("Migrated user: {$user['username']} to database");
            } else {
                $errors++;
                error_log("Failed to migrate user: {$user['username']} - dbExecute returned false");
            }
        } catch (Exception $e) {
            $errors++;
            error_log('Error migrating user ' . ($user['username'] ?? 'unknown') . ': ' . $e->getMessage());
        }
    }
    
    return [
        'migrated' => $migrated,
        'skipped' => $skipped,
        'errors' => $errors,
        'message' => "Migration complete: $migrated migrated, $skipped skipped, $errors errors"
    ];
}

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
        $users = dbQuery("SELECT id, username, password, email, role, created_at, last_login, first_name, last_name, address FROM users");
        if ($users !== false) {
            // Auto-migrate users from JSON if database is available
            // Check if migration is needed (only if JSON file exists and has users)
            if (file_exists(USERS_FILE)) {
                $jsonUsers = json_decode(file_get_contents(USERS_FILE), true);
                if (is_array($jsonUsers) && !empty($jsonUsers)) {
                    // Check if any JSON users are missing from database
                    $dbUsernames = [];
                    foreach ($users as $dbUser) {
                        $dbUsernames[$dbUser['username']] = true;
                    }
                    
                    $needsMigration = false;
                    foreach ($jsonUsers as $jsonUser) {
                        if (!isset($dbUsernames[$jsonUser['username']])) {
                            $needsMigration = true;
                            break;
                        }
                    }
                    
                    if ($needsMigration) {
                        $result = migrateUsersToDatabase();
                        if ($result['migrated'] > 0) {
                            error_log("Auto-migrated {$result['migrated']} users from JSON to database via getUsers()");
                            // Re-fetch users after migration
                            $users = dbQuery("SELECT id, username, password, email, role, created_at, last_login, first_name, last_name, address FROM users");
                            if ($users === false) {
                                $users = [];
                            }
                        }
                    }
                }
            }
            return array_map(function($user) {
                $user['firstName'] = $user['first_name'] ?? null;
                $user['lastName'] = $user['last_name'] ?? null;
                $user['address'] = $user['address'] ?? null;
                unset($user['first_name'], $user['last_name']);
                return $user;
            }, $users);
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
function createUser($username, $password, $email = '', $role = 'user', $firstName = '', $lastName = '', $address = '') {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $userId = bin2hex(random_bytes(8));
    $createdAt = gmdate('Y-m-d H:i:s');
    
    if (isDatabaseAvailable()) {
        // Use database
        $sql = "INSERT INTO users (id, username, password, email, role, created_at, first_name, last_name, address) 
                VALUES (:id, :username, :password, :email, :role, :created_at, :first_name, :last_name, :address)";
        $params = [
            ':id' => $userId,
            ':username' => $username,
            ':password' => $hashedPassword,
            ':email' => $email ?: null,
            ':role' => $role,
            ':created_at' => $createdAt,
            ':first_name' => $firstName ?: null,
            ':last_name' => $lastName ?: null,
            ':address' => $address ?: null
        ];
        
        if (dbExecute($sql, $params) !== false) {
            return [
                'id' => $userId,
                'username' => $username,
                'email' => $email,
                'role' => $role,
                'firstName' => $firstName,
                'lastName' => $lastName,
                'address' => $address,
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
        'firstName' => $firstName,
        'lastName' => $lastName,
        'address' => $address,
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
        // Use database first
        $user = dbQueryOne(
            "SELECT id, username, password, email, role, created_at, last_login, first_name, last_name, address FROM users WHERE username = :username",
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
                'firstName' => $user['first_name'] ?? '',
                'lastName' => $user['last_name'] ?? '',
                'address' => $user['address'] ?? '',
                'createdAt' => $user['created_at'],
                'lastLogin' => gmdate('c')
            ];
        }
        
        // If user not found in database, check JSON file (might be a backup mode user)
        // This allows login even if migration hasn't run yet, and migrates on-the-fly
        if (file_exists(USERS_FILE)) {
            $jsonUsers = json_decode(file_get_contents(USERS_FILE), true);
            if (is_array($jsonUsers)) {
                foreach ($jsonUsers as $jsonUser) {
                    if ($jsonUser['username'] === $username && 
                        isset($jsonUser['password']) && 
                        password_verify($password, $jsonUser['password'])) {
                        // User found in JSON - migrate them now
                        try {
                            $createdAt = $jsonUser['createdAt'] ?? gmdate('Y-m-d H:i:s');
                            if (strpos($createdAt, 'T') !== false) {
                                $createdAt = date('Y-m-d H:i:s', strtotime($createdAt));
                            }
                            
                            $sql = "INSERT INTO users (id, username, password, email, role, created_at, last_login, first_name, last_name, address) 
                                    VALUES (:id, :username, :password, :email, :role, :created_at, :last_login, :first_name, :last_name, :address)";
                            
                            $params = [
                                ':id' => $jsonUser['id'] ?? bin2hex(random_bytes(8)),
                                ':username' => $jsonUser['username'],
                                ':password' => $jsonUser['password'],
                                ':email' => !empty($jsonUser['email']) ? $jsonUser['email'] : null,
                                ':role' => $jsonUser['role'] ?? 'user',
                                ':created_at' => $createdAt,
                                ':last_login' => gmdate('Y-m-d H:i:s'),
                                ':first_name' => $jsonUser['firstName'] ?? ($jsonUser['first_name'] ?? null),
                                ':last_name' => $jsonUser['lastName'] ?? ($jsonUser['last_name'] ?? null),
                                ':address' => $jsonUser['address'] ?? null
                            ];
                            
                            // Check if user already exists (by ID or username) before inserting
                            $existingUser = dbQueryOne(
                                "SELECT id, username FROM users WHERE id = :id OR username = :username",
                                [':id' => $params[':id'], ':username' => $params[':username']]
                            );
                            
                            if ($existingUser) {
                                error_log("User '{$username}' already exists in database (ID: {$existingUser['id']}), skipping migration");
                            } else {
                                // Use direct PDO to get better error information
                                $db = getDB();
                                if ($db) {
                                    try {
                                        $stmt = $db->prepare($sql);
                                        $stmt->execute($params);
                                        $rowsAffected = $stmt->rowCount();
                                        
                                        if ($rowsAffected > 0) {
                                            error_log("Successfully migrated user '{$username}' to database during login (ID: {$params[':id']}, rows: $rowsAffected)");
                                            
                                            // Verify user was actually inserted
                                            $verifyUser = dbQueryOne(
                                                "SELECT id, username FROM users WHERE username = :username",
                                                [':username' => $username]
                                            );
                                            if ($verifyUser) {
                                                error_log("Verified: User '{$username}' is now in database (ID: {$verifyUser['id']})");
                                            } else {
                                                error_log("WARNING: User '{$username}' migration reported success but user not found in database!");
                                            }
                                        } else {
                                            error_log("WARNING: User '{$username}' migration returned 0 rows affected - user may not have been inserted");
                                            $errorInfo = $db->errorInfo();
                                            if ($errorInfo[0] !== '00000') {
                                                error_log("PDO Error: " . print_r($errorInfo, true));
                                            }
                                        }
                                    } catch (PDOException $e) {
                                        error_log("PDO Exception migrating user '{$username}': " . $e->getMessage());
                                        error_log("SQL State: " . $e->getCode());
                                        error_log("SQL: $sql");
                                        error_log("Params: " . print_r($params, true));
                                    }
                                } else {
                                    error_log("ERROR: Cannot get database connection to migrate user '{$username}'");
                                }
                            }
                            
                            // Return user data (formatted like database user)
                            return [
                                'id' => $jsonUser['id'],
                                'username' => $jsonUser['username'],
                                'email' => $jsonUser['email'] ?? null,
                                'role' => $jsonUser['role'] ?? 'user',
                                'createdAt' => $createdAt,
                                'lastLogin' => gmdate('c')
                            ];
                        } catch (Exception $e) {
                            error_log('Error migrating user during login: ' . $e->getMessage());
                            error_log('Stack trace: ' . $e->getTraceAsString());
                            // Still allow login even if migration fails
                            return [
                                'id' => $jsonUser['id'],
                                'username' => $jsonUser['username'],
                                'email' => $jsonUser['email'] ?? null,
                                'role' => $jsonUser['role'] ?? 'user',
                                'createdAt' => $createdAt ?? gmdate('c'),
                                'lastLogin' => gmdate('c')
                            ];
                        } catch (PDOException $e) {
                            error_log('PDO Error migrating user during login: ' . $e->getMessage());
                            error_log('SQL State: ' . $e->getCode());
                            // Still allow login even if migration fails
                            return [
                                'id' => $jsonUser['id'],
                                'username' => $jsonUser['username'],
                                'email' => $jsonUser['email'] ?? null,
                                'role' => $jsonUser['role'] ?? 'user',
                                'createdAt' => $createdAt ?? gmdate('c'),
                                'lastLogin' => gmdate('c')
                            ];
                        }
                    }
                }
            }
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
        $_SESSION['first_name'] = $user['firstName'] ?? '';
        $_SESSION['last_name'] = $user['lastName'] ?? '';
        $_SESSION['user_address'] = $user['address'] ?? '';
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
            "SELECT id, username, email, role, created_at, last_login, first_name, last_name, address FROM users WHERE id = :id",
            [':id' => $_SESSION['user_id']]
        );
        
        if ($user) {
            return [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'role' => $user['role'],
                'firstName' => $user['first_name'] ?? '',
                'lastName' => $user['last_name'] ?? '',
                'address' => $user['address'] ?? '',
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

/**
 * Get the current user's preferred display name.
 *
 * @param bool $uppercase Whether to convert the name to uppercase.
 * @return string
 */
function getUserDisplayName($uppercase = false) {
    if (!isLoggedIn()) {
        return '';
    }
    
    $first = trim($_SESSION['first_name'] ?? '');
    $last = trim($_SESSION['last_name'] ?? '');
    $displayName = trim($first . ' ' . $last);
    
    if ($displayName === '') {
        $displayName = $_SESSION['username'] ?? '';
    }
    
    if ($uppercase) {
        $displayName = strtoupper($displayName);
    }
    
    return $displayName;
}
