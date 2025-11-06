<?php
/**
 * Config File Diagnostic
 * Checks what's actually in db-config.php
 * 
 * IMPORTANT: Delete this file after testing!
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Config File Diagnostic</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; padding: 20px; background: #f5f5f5; }
        .section { background: white; padding: 20px; margin: 20px 0; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .success { color: #155724; background: #d4edda; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .error { color: #721c24; background: #f8d7da; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .info { color: #0c5460; background: #d1ecf1; padding: 10px; border-radius: 5px; margin: 10px 0; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
        pre { background: #f4f4f4; padding: 15px; border-radius: 5px; overflow-x: auto; }
        h1 { color: #333; }
        h2 { color: #666; border-bottom: 2px solid #ddd; padding-bottom: 10px; }
    </style>
</head>
<body>
    <h1>🔍 Config File Diagnostic</h1>
    
    <?php
    $configFile = __DIR__ . '/includes/db-config.php';
    
    echo '<div class="section">';
    echo '<h2>1. File Existence</h2>';
    if (file_exists($configFile)) {
        echo '<div class="success">✅ File exists: <code>' . htmlspecialchars($configFile) . '</code></div>';
        echo '<div class="info">File size: ' . filesize($configFile) . ' bytes</div>';
        echo '<div class="info">Last modified: ' . date('Y-m-d H:i:s', filemtime($configFile)) . '</div>';
    } else {
        echo '<div class="error">❌ File not found: <code>' . htmlspecialchars($configFile) . '</code></div>';
        exit;
    }
    echo '</div>';
    
    echo '<div class="section">';
    echo '<h2>2. File Contents (Password Hidden)</h2>';
    $content = file_get_contents($configFile);
    // Hide password value
    $contentSafe = preg_replace('/define\([\'"]DB_PASS[\'"],\s*[\'"][^\'"]*[\'"]\);/', 'define(\'DB_PASS\', \'***HIDDEN***\');', $content);
    echo '<pre>' . htmlspecialchars($contentSafe) . '</pre>';
    echo '</div>';
    
    echo '<div class="section">';
    echo '<h2>3. Syntax Check</h2>';
    $output = [];
    $return = 0;
    exec("php -l " . escapeshellarg($configFile) . " 2>&1", $output, $return);
    if ($return === 0) {
        echo '<div class="success">✅ No syntax errors</div>';
    } else {
        echo '<div class="error">❌ Syntax error found:</div>';
        echo '<pre>' . htmlspecialchars(implode("\n", $output)) . '</pre>';
    }
    echo '</div>';
    
    echo '<div class="section">';
    echo '<h2>4. Constants After Loading</h2>';
    
    // Try to load the config file
    try {
        require_once $configFile;
        
        $constants = [
            'DB_HOST' => defined('DB_HOST') ? DB_HOST : 'NOT DEFINED',
            'DB_PORT' => defined('DB_PORT') ? DB_PORT : 'NOT DEFINED',
            'DB_NAME' => defined('DB_NAME') ? DB_NAME : 'NOT DEFINED',
            'DB_USER' => defined('DB_USER') ? DB_USER : 'NOT DEFINED',
            'DB_PASS' => defined('DB_PASS') ? (DB_PASS ? '***SET***' : 'EMPTY') : 'NOT DEFINED',
        ];
        
        echo '<table style="width: 100%; border-collapse: collapse;">';
        echo '<tr style="background: #f4f4f4;"><th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Constant</th><th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Value</th></tr>';
        foreach ($constants as $name => $value) {
            $status = ($value === 'NOT DEFINED' || $value === 'EMPTY') ? 'error' : 'success';
            echo '<tr>';
            echo '<td style="padding: 10px; border: 1px solid #ddd;"><code>' . $name . '</code></td>';
            echo '<td style="padding: 10px; border: 1px solid #ddd;">';
            if ($status === 'error') {
                echo '<span style="color: #721c24;">❌ ' . htmlspecialchars($value) . '</span>';
            } else {
                echo '<span style="color: #155724;">✅ ' . htmlspecialchars($value) . '</span>';
            }
            echo '</td>';
            echo '</tr>';
        }
        echo '</table>';
        
        // Check if all required constants are set
        $allSet = defined('DB_HOST') && defined('DB_NAME') && defined('DB_USER') && defined('DB_PASS') && !empty(DB_NAME) && !empty(DB_USER);
        if ($allSet) {
            echo '<div class="success" style="margin-top: 15px;">✅ All required constants are defined and have values!</div>';
        } else {
            echo '<div class="error" style="margin-top: 15px;">❌ Some constants are missing or empty!</div>';
        }
        
    } catch (Exception $e) {
        echo '<div class="error">❌ Error loading config file: ' . htmlspecialchars($e->getMessage()) . '</div>';
    } catch (ParseError $e) {
        echo '<div class="error">❌ Parse error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        echo '<div class="info">Line: ' . $e->getLine() . '</div>';
    }
    echo '</div>';
    
    echo '<div class="section">';
    echo '<h2>5. File Permissions</h2>';
    $perms = fileperms($configFile);
    echo '<div class="info">Permissions: <code>' . substr(sprintf('%o', $perms), -4) . '</code></div>';
    echo '<div class="info">Readable: ' . (is_readable($configFile) ? '✅ Yes' : '❌ No') . '</div>';
    echo '</div>';
    
    echo '<div class="section">';
    echo '<h2>6. Database Connection Test</h2>';
    
    // Load db.php to test connection
    $dbFile = __DIR__ . '/includes/db.php';
    if (file_exists($dbFile)) {
        try {
            require_once $dbFile;
            
            if (function_exists('testDBConnection')) {
                echo '<div class="info">Testing database connection...</div>';
                
                $db = getDB();
                if ($db === null) {
                    echo '<div class="error">❌ getDB() returned null - connection failed</div>';
                    echo '<div class="info">Check error logs for details</div>';
                } else {
                    echo '<div class="success">✅ getDB() returned a connection object</div>';
                    
                    // Try a simple query
                    try {
                        $stmt = $db->query("SELECT 1 as test");
                        $result = $stmt->fetch();
                        if ($result) {
                            echo '<div class="success">✅ Database query successful!</div>';
                        }
                    } catch (PDOException $e) {
                        echo '<div class="error">❌ Query failed: ' . htmlspecialchars($e->getMessage()) . '</div>';
                    }
                }
                
                if (testDBConnection()) {
                    echo '<div class="success">✅ testDBConnection() returned true - connection successful!</div>';
                } else {
                    echo '<div class="error">❌ testDBConnection() returned false - connection failed</div>';
                }
            } else {
                echo '<div class="error">❌ testDBConnection() function not found</div>';
            }
        } catch (Exception $e) {
            echo '<div class="error">❌ Error loading db.php: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    } else {
        echo '<div class="error">❌ db.php file not found</div>';
    }
    echo '</div>';
    
    echo '<div class="section">';
    echo '<h2>7. PDO Extension Check</h2>';
    echo '<div class="info">PDO extension loaded: ' . (extension_loaded('pdo') ? '✅ Yes' : '❌ No') . '</div>';
    echo '<div class="info">PDO_MySQL extension loaded: ' . (extension_loaded('pdo_mysql') ? '✅ Yes' : '❌ No') . '</div>';
    if (extension_loaded('pdo')) {
        echo '<div class="info">Available PDO drivers: ' . implode(', ', PDO::getAvailableDrivers()) . '</div>';
    }
    echo '</div>';
    
    echo '<div class="section">';
    echo '<h2>8. Connection String Test</h2>';
    if (defined('DB_HOST') && defined('DB_PORT') && defined('DB_NAME')) {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        echo '<div class="info">DSN: <code>' . htmlspecialchars($dsn) . '</code></div>';
        echo '<div class="info">User: <code>' . (defined('DB_USER') ? htmlspecialchars(DB_USER) : 'NOT DEFINED') . '</code></div>';
        echo '<div class="info">Password: <code>' . (defined('DB_PASS') && DB_PASS ? '***SET***' : 'NOT SET') . '</code></div>';
        
        // Try to create a PDO connection directly
        if (extension_loaded('pdo') && extension_loaded('pdo_mysql')) {
            try {
                $testPdo = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_TIMEOUT => 5
                ]);
                echo '<div class="success">✅ Direct PDO connection successful!</div>';
            } catch (PDOException $e) {
                echo '<div class="error">❌ Direct PDO connection failed:</div>';
                echo '<div class="error">' . htmlspecialchars($e->getMessage()) . '</div>';
                echo '<div class="info">Error Code: ' . $e->getCode() . '</div>';
            }
        }
    }
    echo '</div>';
    ?>
    
    <div class="section">
        <h2>🔒 Security Note</h2>
        <div class="info">
            <strong>⚠️ Important:</strong> Delete this file (<code>check-config.php</code>) after testing for security reasons!
        </div>
    </div>
</body>
</html>

