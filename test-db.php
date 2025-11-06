<?php
/**
 * Database Connection Test
 * 
 * This file tests your database connection.
 * Visit it in your browser or run: php test-db.php
 * 
 * IMPORTANT: Delete this file after testing for security!
 */

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Connection Test</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; }
        h1 { color: #333; }
        h2 { color: #666; margin-top: 30px; }
    </style>
</head>
<body>
    <h1>🔍 Database Connection Test</h1>
    
    <?php
    // Check if database config exists
    $configFile = __DIR__ . '/includes/db-config.php';
    if (!file_exists($configFile)) {
        echo '<div class="error">';
        echo '<strong>❌ Database configuration not found!</strong><br>';
        echo 'Please copy <code>includes/db-config.php.example</code> to <code>includes/db-config.php</code> and fill in your credentials.';
        echo '</div>';
        exit;
    }
    
    echo '<div class="info">';
    echo '<strong>ℹ️ Configuration file found:</strong> <code>includes/db-config.php</code>';
    echo '</div>';
    
    // Test connection
    if (testDBConnection()) {
        echo '<div class="success">';
        echo '<strong>✅ Database connection successful!</strong><br>';
        echo 'Your database is configured correctly and ready to use.';
        echo '</div>';
        
        // Test query
        $db = getDB();
        if ($db) {
            try {
                $tables = dbQuery("SHOW TABLES");
                if ($tables !== false) {
                    echo '<h2>📊 Database Tables</h2>';
                    if (count($tables) > 0) {
                        echo '<div class="success">';
                        echo '<strong>Found ' . count($tables) . ' table(s):</strong><ul>';
                        foreach ($tables as $table) {
                            $tableName = array_values($table)[0];
                            echo '<li><code>' . htmlspecialchars($tableName) . '</code></li>';
                        }
                        echo '</ul></div>';
                        
                        // Check for required tables
                        $requiredTables = ['users', 'leads', 'messages'];
                        $existingTables = array_map(function($t) { return array_values($t)[0]; }, $tables);
                        $missingTables = array_diff($requiredTables, $existingTables);
                        
                        if (empty($missingTables)) {
                            echo '<div class="success">';
                            echo '<strong>✅ All required tables exist!</strong>';
                            echo '</div>';
                        } else {
                            echo '<div class="error">';
                            echo '<strong>⚠️ Missing tables:</strong> ';
                            echo implode(', ', array_map(function($t) { return '<code>' . $t . '</code>'; }, $missingTables));
                            echo '<br>Please run <code>database/schema.sql</code> in phpMyAdmin to create the tables.';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="error">';
                        echo '<strong>⚠️ No tables found.</strong><br>';
                        echo 'Please run <code>database/schema.sql</code> in phpMyAdmin to create the tables.';
                        echo '</div>';
                    }
                }
            } catch (Exception $e) {
                echo '<div class="error">';
                echo '<strong>❌ Error querying database:</strong> ' . htmlspecialchars($e->getMessage());
                echo '</div>';
            }
        }
    } else {
        echo '<div class="error">';
        echo '<strong>❌ Database connection failed!</strong><br>';
        echo 'Please check your database credentials in <code>includes/db-config.php</code>.<br><br>';
        echo '<strong>Common issues:</strong><ul>';
        echo '<li>Incorrect database name, username, or password</li>';
        echo '<li>Database host is not "localhost"</li>';
        echo '<li>Database user doesn\'t have permissions</li>';
        echo '<li>Database doesn\'t exist</li>';
        echo '</ul>';
        echo '</div>';
    }
    ?>
    
    <h2>🔒 Security Note</h2>
    <div class="info">
        <strong>⚠️ Important:</strong> Delete this file (<code>test-db.php</code>) after testing for security reasons!
    </div>
</body>
</html>

