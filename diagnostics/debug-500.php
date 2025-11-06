<?php
/**
 * 500 Error Debug Script
 * 
 * This script helps identify what's causing the 500 error.
 * Visit this file in your browser to see detailed error information.
 * 
 * IMPORTANT: This folder is protected - only use for debugging!
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

$rootDir = dirname(__DIR__);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 Error Debug</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1000px; margin: 20px auto; padding: 20px; background: #f5f5f5; }
        .section { background: white; padding: 20px; margin: 20px 0; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .success { color: #155724; background: #d4edda; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .error { color: #721c24; background: #f8d7da; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .warning { color: #856404; background: #fff3cd; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .info { color: #0c5460; background: #d1ecf1; padding: 10px; border-radius: 5px; margin: 10px 0; }
        h1 { color: #333; }
        h2 { color: #666; border-bottom: 2px solid #ddd; padding-bottom: 10px; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
        pre { background: #f4f4f4; padding: 15px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🔍 500 Error Debug Information</h1>
    
    <div class="section">
        <h2>1. PHP Version & Extensions</h2>
        <?php
        echo '<p><strong>PHP Version:</strong> ' . phpversion() . '</p>';
        
        $requiredExtensions = ['pdo', 'pdo_mysql', 'json', 'mbstring'];
        echo '<p><strong>Required Extensions:</strong></p><ul>';
        foreach ($requiredExtensions as $ext) {
            $loaded = extension_loaded($ext);
            $status = $loaded ? '<span class="success">✅ Loaded</span>' : '<span class="error">❌ Missing</span>';
            echo '<li>' . $ext . ': ' . $status . '</li>';
        }
        echo '</ul>';
        ?>
    </div>
    
    <div class="section">
        <h2>2. File System Checks</h2>
        <?php
        $filesToCheck = [
            'includes/config.php',
            'includes/db.php',
            'includes/db-config.php',
            'includes/header.php',
            'includes/auth.php'
        ];
        
        echo '<ul>';
        foreach ($filesToCheck as $file) {
            $path = $rootDir . '/' . $file;
            $exists = file_exists($path);
            $readable = $exists ? is_readable($path) : false;
            $status = $exists && $readable ? '<span class="success">✅ OK</span>' : '<span class="error">❌ Missing/Not Readable</span>';
            echo '<li>' . $file . ': ' . $status;
            if ($exists) {
                echo ' (Size: ' . filesize($path) . ' bytes)';
            }
            echo '</li>';
        }
        echo '</ul>';
        ?>
    </div>
    
    <div class="section">
        <h2>3. Database Configuration</h2>
        <?php
        $configFile = $rootDir . '/includes/db-config.php';
        if (file_exists($configFile)) {
            echo '<div class="success">✅ Config file exists</div>';
            
            // Try to load it
            try {
                require_once $configFile;
                
                echo '<div class="info">';
                echo '<strong>Database Configuration:</strong><br>';
                echo 'Host: <code>' . (defined('DB_HOST') ? htmlspecialchars(DB_HOST) : 'NOT DEFINED') . '</code><br>';
                echo 'Database: <code>' . (defined('DB_NAME') ? htmlspecialchars(DB_NAME) : 'NOT DEFINED') . '</code><br>';
                echo 'User: <code>' . (defined('DB_USER') ? htmlspecialchars(DB_USER) : 'NOT DEFINED') . '</code><br>';
                echo 'Password: <code>' . (defined('DB_PASS') ? (empty(DB_PASS) ? '(empty)' : '••••••••') : 'NOT DEFINED') . '</code>';
                echo '</div>';
            } catch (Exception $e) {
                echo '<div class="error">';
                echo '<strong>❌ Error loading config file:</strong><br>';
                echo htmlspecialchars($e->getMessage());
                echo '</div>';
            }
        } else {
            echo '<div class="error">❌ Config file not found at: ' . htmlspecialchars($configFile) . '</div>';
        }
        ?>
    </div>
    
    <div class="section">
        <h2>4. Database Connection Test</h2>
        <?php
        if (file_exists($rootDir . '/includes/db.php')) {
            require_once $rootDir . '/includes/db.php';
            
            if (function_exists('testDBConnection')) {
                if (testDBConnection()) {
                    echo '<div class="success">✅ Database connection successful!</div>';
                } else {
                    echo '<div class="error">❌ Database connection failed!</div>';
                    echo '<div class="info">Check your database credentials and ensure the database exists.</div>';
                }
            } else {
                echo '<div class="error">❌ Database functions not loaded</div>';
            }
        } else {
            echo '<div class="error">❌ db.php file not found</div>';
        }
        ?>
    </div>
    
    <div class="section">
        <h2>5. Syntax Check</h2>
        <?php
        $phpFiles = [
            'includes/config.php',
            'includes/db.php',
            'includes/db-config.php',
            'includes/header.php',
            'includes/auth.php'
        ];
        
        echo '<ul>';
        foreach ($phpFiles as $file) {
            $path = $rootDir . '/' . $file;
            if (file_exists($path)) {
                // Check syntax
                $output = [];
                $return = 0;
                @exec("php -l " . escapeshellarg($path) . " 2>&1", $output, $return);
                
                if ($return === 0) {
                    echo '<li><span class="success">✅</span> ' . $file . ' - No syntax errors</li>';
                } else {
                    echo '<li><span class="error">❌</span> ' . $file . ' - <strong>Syntax Error!</strong><br>';
                    echo '<pre>' . htmlspecialchars(implode("\n", $output)) . '</pre></li>';
                }
            }
        }
        echo '</ul>';
        ?>
    </div>
    
    <div class="section">
        <h2>6. Include Test</h2>
        <?php
        echo '<p>Testing file includes...</p>';
        
        try {
            require_once $rootDir . '/includes/config.php';
            echo '<div class="success">✅ config.php loaded</div>';
        } catch (Exception $e) {
            echo '<div class="error">❌ Error loading config.php: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
        
        try {
            if (file_exists($rootDir . '/includes/db.php')) {
                require_once $rootDir . '/includes/db.php';
                echo '<div class="success">✅ db.php loaded</div>';
            }
        } catch (Exception $e) {
            echo '<div class="error">❌ Error loading db.php: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
        
        try {
            if (file_exists($rootDir . '/includes/header.php')) {
                // Don't actually include header.php as it outputs HTML
                // Just check if it can be parsed
                $content = file_get_contents($rootDir . '/includes/header.php');
                echo '<div class="success">✅ header.php can be read</div>';
            }
        } catch (Exception $e) {
            echo '<div class="error">❌ Error reading header.php: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
        ?>
    </div>
    
    <div class="section">
        <h2>7. Server Information</h2>
        <?php
        echo '<p><strong>Server Software:</strong> ' . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . '</p>';
        echo '<p><strong>Document Root:</strong> ' . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . '</p>';
        echo '<p><strong>Script Path:</strong> ' . __DIR__ . '</p>';
        echo '<p><strong>Current File:</strong> ' . __FILE__ . '</p>';
        ?>
    </div>
    
    <div class="section">
        <h2>8. Error Log Location</h2>
        <?php
        $errorLog = ini_get('error_log');
        if ($errorLog) {
            echo '<p><strong>Error Log:</strong> <code>' . htmlspecialchars($errorLog) . '</code></p>';
        } else {
            echo '<p><strong>Error Log:</strong> Using default PHP error log</p>';
        }
        echo '<div class="info">Check your error log for detailed error messages. Common locations:</div>';
        echo '<ul>';
        echo '<li><code>/home/username/logs/error_log</code></li>';
        echo '<li><code>/home/username/public_html/error_log</code></li>';
        echo '<li>Check Hostinger hPanel → Error Log</li>';
        echo '</ul>';
        ?>
    </div>
    
    <div class="section">
        <h2>🔒 Security Note</h2>
        <div class="warning">
            <strong>⚠️ Important:</strong> This diagnostics folder is protected. These files should only be accessed by authorized personnel for debugging purposes.
        </div>
    </div>
</body>
</html>

