<?php
/**
 * Database Connection Helper
 * Uses PDO for secure database access
 */

// Database configuration
// You can set these via environment variables or edit directly
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: '');
define('DB_USER', getenv('DB_USER') ?: '');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_CHARSET', 'utf8mb4');

// If credentials are in a separate config file, load them
$dbConfigFile = __DIR__ . '/db-config.php';
if (file_exists($dbConfigFile)) {
    try {
        require_once $dbConfigFile;
    } catch (Exception $e) {
        // Config file has syntax errors - log but don't break
        error_log('Error loading db-config.php: ' . $e->getMessage());
    }
}

/**
 * Get database connection
 * @return PDO|null Database connection or null on failure
 */
function getDB() {
    static $pdo = null;
    
    if ($pdo !== null) {
        return $pdo;
    }
    
    // Check if database is configured
    if (empty(DB_NAME) || empty(DB_USER)) {
        error_log('Database not configured. Please set DB_NAME, DB_USER, and DB_PASS.');
        return null;
    }
    
    try {
        // Check if PDO extension is available
        if (!extension_loaded('pdo') || !extension_loaded('pdo_mysql')) {
            error_log('PDO or PDO_MySQL extension not available');
            return null;
        }
        
        // Build DSN with optional port
        $host = defined('DB_HOST') ? DB_HOST : 'localhost';
        $port = defined('DB_PORT') ? DB_PORT : '3306';
        $dsn = "mysql:host=" . $host . ";port=" . $port . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_TIMEOUT            => 5, // 5 second timeout
        ];
        
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        // Log detailed error but don't expose to user
        error_log('Database connection failed: ' . $e->getMessage());
        error_log('DSN: ' . $dsn . ' | User: ' . DB_USER);
        return null;
    } catch (Exception $e) {
        error_log('Unexpected error in database connection: ' . $e->getMessage());
        return null;
    }
}

/**
 * Test database connection
 * @return bool True if connection successful
 */
function testDBConnection() {
    $db = getDB();
    if ($db === null) {
        return false;
    }
    
    try {
        $db->query("SELECT 1");
        return true;
    } catch (PDOException $e) {
        error_log('Database test failed: ' . $e->getMessage());
        return false;
    }
}

/**
 * Execute a query and return results
 * @param string $sql SQL query
 * @param array $params Parameters for prepared statement
 * @return array|false Results or false on failure
 */
function dbQuery($sql, $params = []) {
    $db = getDB();
    if ($db === null) {
        return false;
    }
    
    try {
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log('Database query failed: ' . $e->getMessage());
        return false;
    }
}

/**
 * Execute a query and return single row
 * @param string $sql SQL query
 * @param array $params Parameters for prepared statement
 * @return array|false Single row or false on failure
 */
function dbQueryOne($sql, $params = []) {
    $db = getDB();
    if ($db === null) {
        return false;
    }
    
    try {
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log('Database query failed: ' . $e->getMessage());
        return false;
    }
}

/**
 * Execute a query (INSERT, UPDATE, DELETE)
 * @param string $sql SQL query
 * @param array $params Parameters for prepared statement
 * @return int|false Number of affected rows or false on failure
 */
function dbExecute($sql, $params = []) {
    $db = getDB();
    if ($db === null) {
        return false;
    }
    
    try {
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    } catch (PDOException $e) {
        error_log('Database execute failed: ' . $e->getMessage());
        return false;
    }
}

/**
 * Get last insert ID
 * @return string|false Last insert ID or false on failure
 */
function dbLastInsertId() {
    $db = getDB();
    if ($db === null) {
        return false;
    }
    
    return $db->lastInsertId();
}

/**
 * Begin transaction
 * @return bool Success
 */
function dbBeginTransaction() {
    $db = getDB();
    if ($db === null) {
        return false;
    }
    return $db->beginTransaction();
}

/**
 * Commit transaction
 * @return bool Success
 */
function dbCommit() {
    $db = getDB();
    if ($db === null) {
        return false;
    }
    return $db->commit();
}

/**
 * Rollback transaction
 * @return bool Success
 */
function dbRollback() {
    $db = getDB();
    if ($db === null) {
        return false;
    }
    return $db->rollBack();
}

