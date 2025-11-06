<?php
/**
 * Test User Migration
 * 
 * This script tests if users can be migrated from JSON to database
 * 
 * IMPORTANT: This folder is protected - only use for debugging!
 */

// Suppress error handler for diagnostics
$rootDir = dirname(__DIR__);

// Load config first (but skip error handler for diagnostics)
$configFile = $rootDir . '/includes/config.php';
if (file_exists($configFile)) {
    // Temporarily disable error handler
    $originalErrorHandler = set_error_handler(null);
    require_once $configFile;
    if ($originalErrorHandler) {
        set_error_handler($originalErrorHandler);
    }
}

// Load db.php
$dbFile = $rootDir . '/includes/db.php';
if (file_exists($dbFile)) {
    require_once $dbFile;
}

// Load auth.php
$authFile = $rootDir . '/includes/auth.php';
if (file_exists($authFile)) {
    require_once $authFile;
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Migration Test</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1000px; margin: 20px auto; padding: 20px; background: #f5f5f5; }
        .section { background: white; padding: 20px; margin: 20px 0; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .success { color: #155724; background: #d4edda; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .error { color: #721c24; background: #f8d7da; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .info { color: #0c5460; background: #d1ecf1; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .warning { color: #856404; background: #fff3cd; padding: 10px; border-radius: 5px; margin: 10px 0; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
        pre { background: #f4f4f4; padding: 15px; border-radius: 5px; overflow-x: auto; }
        h1 { color: #333; }
        h2 { color: #666; border-bottom: 2px solid #ddd; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { padding: 10px; text-align: left; border: 1px solid #ddd; }
        th { background: #f4f4f4; font-weight: bold; }
    </style>
</head>
<body>
    <h1>🔍 User Migration Test</h1>
    
    <?php
    // Check database connection
    echo '<div class="section">';
    echo '<h2>1. Database Connection</h2>';
    
    // Check if constants are defined
    echo '<div class="info">Checking database configuration...</div>';
    echo '<ul>';
    echo '<li>DB_HOST: <code>' . (defined('DB_HOST') ? htmlspecialchars(DB_HOST) : 'NOT DEFINED') . '</code></li>';
    echo '<li>DB_PORT: <code>' . (defined('DB_PORT') ? htmlspecialchars(DB_PORT) : 'NOT DEFINED') . '</code></li>';
    echo '<li>DB_NAME: <code>' . (defined('DB_NAME') ? htmlspecialchars(DB_NAME) : 'NOT DEFINED') . '</code></li>';
    echo '<li>DB_USER: <code>' . (defined('DB_USER') ? htmlspecialchars(DB_USER) : 'NOT DEFINED') . '</code></li>';
    echo '<li>DB_PASS: <code>' . (defined('DB_PASS') ? (DB_PASS ? '***SET***' : 'NOT SET') : 'NOT DEFINED') . '</code></li>';
    echo '</ul>';
    
    // Check PDO extensions
    echo '<div class="info">Checking PDO extensions...</div>';
    echo '<ul>';
    echo '<li>PDO extension: ' . (extension_loaded('pdo') ? '✅ Loaded' : '❌ Not loaded') . '</li>';
    echo '<li>PDO_MySQL extension: ' . (extension_loaded('pdo_mysql') ? '✅ Loaded' : '❌ Not loaded') . '</li>';
    echo '</ul>';
    
    // Try to get database connection
    if (function_exists('getDB')) {
        $db = getDB();
        if ($db !== null) {
            echo '<div class="success">✅ getDB() returned a connection object</div>';
            
            // Test query
            try {
                $testQuery = $db->query("SELECT 1 as test");
                if ($testQuery) {
                    echo '<div class="success">✅ Database query test successful!</div>';
                }
            } catch (PDOException $e) {
                echo '<div class="error">❌ Database query failed: ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
        } else {
            echo '<div class="error">❌ getDB() returned null - connection failed</div>';
        }
    } else {
        echo '<div class="error">❌ getDB() function not found</div>';
    }
    
    // Test connection function
    if (function_exists('testDBConnection')) {
        $connectionTest = testDBConnection();
        if ($connectionTest) {
            echo '<div class="success">✅ testDBConnection() returned true - connection successful!</div>';
        } else {
            echo '<div class="error">❌ testDBConnection() returned false - connection failed</div>';
            echo '<div class="info">Check error logs for detailed error messages.</div>';
        }
    } else {
        echo '<div class="error">❌ testDBConnection() function not found</div>';
    }
    
    // If connection failed, show more details but don't exit
    $dbConnected = function_exists('testDBConnection') && testDBConnection();
    if (!$dbConnected) {
        echo '<div class="warning">⚠️ Database connection failed. Some features below may not work, but we\'ll continue with the test.</div>';
    }
    echo '</div>';
    
    // Check if users table exists
    echo '<div class="section">';
    echo '<h2>2. Users Table Check</h2>';
    
    if (!$dbConnected) {
        echo '<div class="warning">⚠️ Skipping table check - database connection failed</div>';
    } else {
        try {
            if (function_exists('dbQuery')) {
                $tables = dbQuery("SHOW TABLES LIKE 'users'");
                if ($tables && count($tables) > 0) {
                    echo '<div class="success">✅ Users table exists</div>';
                    
                    // Get table structure
                    $structure = dbQuery("DESCRIBE users");
                    if ($structure) {
                        echo '<table>';
                        echo '<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>';
                        foreach ($structure as $field) {
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($field['Field']) . '</td>';
                            echo '<td>' . htmlspecialchars($field['Type']) . '</td>';
                            echo '<td>' . htmlspecialchars($field['Null']) . '</td>';
                            echo '<td>' . htmlspecialchars($field['Key']) . '</td>';
                            echo '<td>' . htmlspecialchars($field['Default'] ?? 'NULL') . '</td>';
                            echo '</tr>';
                        }
                        echo '</table>';
                    }
                } else {
                    echo '<div class="error">❌ Users table does not exist!</div>';
                    echo '<div class="info">Please run <code>database/schema.sql</code> in phpMyAdmin to create the table.</div>';
                }
            } else {
                echo '<div class="error">❌ dbQuery() function not found</div>';
            }
        } catch (Exception $e) {
            echo '<div class="error">❌ Error checking table: ' . htmlspecialchars($e->getMessage()) . '</div>';
        } catch (PDOException $e) {
            echo '<div class="error">❌ PDO Error checking table: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
    echo '</div>';
    
    // Check JSON file
    echo '<div class="section">';
    echo '<h2>3. JSON Users File</h2>';
    $usersFile = $rootDir . '/data/users.json';
    if (file_exists($usersFile)) {
        echo '<div class="success">✅ JSON file exists: <code>' . htmlspecialchars($usersFile) . '</code></div>';
        $jsonUsers = json_decode(file_get_contents($usersFile), true);
        if (is_array($jsonUsers) && !empty($jsonUsers)) {
            echo '<div class="info">Found ' . count($jsonUsers) . ' user(s) in JSON file:</div>';
            echo '<table>';
            echo '<tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Created At</th></tr>';
            foreach ($jsonUsers as $user) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($user['id'] ?? 'N/A') . '</td>';
                echo '<td>' . htmlspecialchars($user['username'] ?? 'N/A') . '</td>';
                echo '<td>' . htmlspecialchars($user['email'] ?? 'N/A') . '</td>';
                echo '<td>' . htmlspecialchars($user['role'] ?? 'user') . '</td>';
                echo '<td>' . htmlspecialchars($user['createdAt'] ?? 'N/A') . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo '<div class="warning">⚠️ JSON file is empty or invalid</div>';
        }
    } else {
        echo '<div class="warning">⚠️ JSON file not found: <code>' . htmlspecialchars($usersFile) . '</code></div>';
    }
    echo '</div>';
    
    // Check database users
    echo '<div class="section">';
    echo '<h2>4. Database Users</h2>';
    try {
        $dbUsers = dbQuery("SELECT id, username, email, role, created_at, last_login FROM users ORDER BY created_at");
        if ($dbUsers !== false) {
            if (count($dbUsers) > 0) {
                echo '<div class="success">Found ' . count($dbUsers) . ' user(s) in database:</div>';
                echo '<table>';
                echo '<tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Created At</th><th>Last Login</th></tr>';
                foreach ($dbUsers as $user) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($user['id']) . '</td>';
                    echo '<td>' . htmlspecialchars($user['username']) . '</td>';
                    echo '<td>' . htmlspecialchars($user['email'] ?? 'N/A') . '</td>';
                    echo '<td>' . htmlspecialchars($user['role']) . '</td>';
                    echo '<td>' . htmlspecialchars($user['created_at']) . '</td>';
                    echo '<td>' . htmlspecialchars($user['last_login'] ?? 'Never') . '</td>';
                    echo '</tr>';
                }
                echo '</table>';
            } else {
                echo '<div class="info">ℹ️ No users in database yet</div>';
            }
        } else {
            echo '<div class="error">❌ Error querying database users</div>';
        }
    } catch (Exception $e) {
        echo '<div class="error">❌ Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
    echo '</div>';
    
    // Compare JSON vs Database
    echo '<div class="section">';
    echo '<h2>5. Migration Status</h2>';
    if (file_exists($usersFile) && is_array($jsonUsers) && !empty($jsonUsers)) {
        $dbUsers = dbQuery("SELECT username FROM users");
        $dbUsernames = [];
        if ($dbUsers !== false) {
            foreach ($dbUsers as $dbUser) {
                $dbUsernames[$dbUser['username']] = true;
            }
        }
        
        $missingUsers = [];
        foreach ($jsonUsers as $jsonUser) {
            if (!isset($dbUsernames[$jsonUser['username']])) {
                $missingUsers[] = $jsonUser;
            }
        }
        
        if (empty($missingUsers)) {
            echo '<div class="success">✅ All JSON users are in the database!</div>';
        } else {
            echo '<div class="warning">⚠️ Found ' . count($missingUsers) . ' user(s) in JSON that are NOT in database:</div>';
            echo '<table>';
            echo '<tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th></tr>';
            foreach ($missingUsers as $user) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($user['id'] ?? 'N/A') . '</td>';
                echo '<td>' . htmlspecialchars($user['username'] ?? 'N/A') . '</td>';
                echo '<td>' . htmlspecialchars($user['email'] ?? 'N/A') . '</td>';
                echo '<td>' . htmlspecialchars($user['role'] ?? 'user') . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
    }
    echo '</div>';
    
    // Test migration
    echo '<div class="section">';
    echo '<h2>6. Test Migration</h2>';
    echo '<form method="POST" action="">';
    echo '<input type="hidden" name="action" value="migrate">';
    echo '<button type="submit" style="padding: 10px 20px; background: #15803d; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;">Run Migration Now</button>';
    echo '</form>';
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'migrate') {
        echo '<div class="info" style="margin-top: 15px;">Running migration...</div>';
        $result = migrateUsersToDatabase();
        echo '<div class="' . ($result['errors'] > 0 ? 'error' : 'success') . '">';
        echo '<strong>Migration Result:</strong><br>';
        echo 'Migrated: ' . $result['migrated'] . ' users<br>';
        echo 'Skipped: ' . $result['skipped'] . ' users<br>';
        echo 'Errors: ' . $result['errors'] . ' users<br>';
        if (!empty($result['message'])) {
            echo 'Message: ' . htmlspecialchars($result['message']);
        }
        echo '</div>';
        echo '<div class="info">Page will refresh in 3 seconds...</div>';
        echo '<script>setTimeout(function(){ location.reload(); }, 3000);</script>';
    }
    echo '</div>';
    
    // Test login simulation
    echo '<div class="section">';
    echo '<h2>7. Test Login Simulation</h2>';
    if (file_exists($usersFile) && is_array($jsonUsers) && !empty($jsonUsers)) {
        echo '<p>Select a user from JSON to test login and migration:</p>';
        echo '<form method="POST" action="">';
        echo '<input type="hidden" name="action" value="test_login">';
        echo '<select name="username" style="padding: 8px; width: 200px; margin-right: 10px;">';
        foreach ($jsonUsers as $user) {
            echo '<option value="' . htmlspecialchars($user['username']) . '">' . htmlspecialchars($user['username']) . '</option>';
        }
        echo '</select>';
        echo '<input type="password" name="password" placeholder="Enter password" style="padding: 8px; width: 200px; margin-right: 10px;">';
        echo '<button type="submit" style="padding: 8px 20px; background: #15803d; color: white; border: none; border-radius: 5px; cursor: pointer;">Test Login</button>';
        echo '</form>';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'test_login') {
            $testUsername = $_POST['username'] ?? '';
            $testPassword = $_POST['password'] ?? '';
            
            if (!empty($testUsername) && !empty($testPassword)) {
                echo '<div class="info" style="margin-top: 15px;">Testing login for: ' . htmlspecialchars($testUsername) . '</div>';
                
                // Check if user exists in database before
                $userBefore = dbQueryOne("SELECT username FROM users WHERE username = :username", [':username' => $testUsername]);
                echo '<div class="info">User in database before login: ' . ($userBefore ? '✅ Yes' : '❌ No') . '</div>';
                
                // Simulate login
                $user = verifyUser($testUsername, $testPassword);
                
                if ($user) {
                    echo '<div class="success">✅ Login successful!</div>';
                    
                    // Check if user exists in database after
                    $userAfter = dbQueryOne("SELECT username, id, email, role FROM users WHERE username = :username", [':username' => $testUsername]);
                    if ($userAfter) {
                        echo '<div class="success">✅ User now exists in database!</div>';
                        echo '<div class="info">User ID: ' . htmlspecialchars($userAfter['id']) . '<br>';
                        echo 'Email: ' . htmlspecialchars($userAfter['email'] ?? 'N/A') . '<br>';
                        echo 'Role: ' . htmlspecialchars($userAfter['role']) . '</div>';
                    } else {
                        echo '<div class="error">❌ User still not in database after login!</div>';
                        echo '<div class="warning">Check error logs for migration errors.</div>';
                    }
                } else {
                    echo '<div class="error">❌ Login failed - incorrect password or user not found</div>';
                }
            }
        }
    }
    echo '</div>';
    ?>
    
    <div class="section">
        <h2>🔒 Security Note</h2>
        <div class="info">
            <strong>⚠️ Important:</strong> This diagnostics folder is protected. These files should only be accessed by authorized personnel for debugging purposes.
        </div>
    </div>
</body>
</html>

