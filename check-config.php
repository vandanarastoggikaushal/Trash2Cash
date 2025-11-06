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
    ?>
    
    <div class="section">
        <h2>🔒 Security Note</h2>
        <div class="info">
            <strong>⚠️ Important:</strong> Delete this file (<code>check-config.php</code>) after testing for security reasons!
        </div>
    </div>
</body>
</html>

