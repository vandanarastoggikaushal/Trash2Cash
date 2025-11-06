<?php
/**
 * Quick Error Check
 * Run this to see what's causing the 500 error
 * 
 * IMPORTANT: This folder is protected - only use for debugging!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$rootDir = dirname(__DIR__);

echo "Checking for errors...\n\n";

// Check db-config.php syntax
echo "1. Checking db-config.php syntax...\n";
$configFile = $rootDir . '/includes/db-config.php';
if (file_exists($configFile)) {
    $output = [];
    $return = 0;
    @exec("php -l " . escapeshellarg($configFile) . " 2>&1", $output, $return);
    if ($return === 0) {
        echo "   ✅ No syntax errors\n";
    } else {
        echo "   ❌ SYNTAX ERROR FOUND:\n";
        echo "   " . implode("\n   ", $output) . "\n";
    }
} else {
    echo "   ⚠️ File not found\n";
}

// Check db.php syntax
echo "\n2. Checking db.php syntax...\n";
$dbFile = $rootDir . '/includes/db.php';
if (file_exists($dbFile)) {
    $output = [];
    $return = 0;
    @exec("php -l " . escapeshellarg($dbFile) . " 2>&1", $output, $return);
    if ($return === 0) {
        echo "   ✅ No syntax errors\n";
    } else {
        echo "   ❌ SYNTAX ERROR FOUND:\n";
        echo "   " . implode("\n   ", $output) . "\n";
    }
}

// Check auth.php syntax
echo "\n3. Checking auth.php syntax...\n";
$authFile = $rootDir . '/includes/auth.php';
if (file_exists($authFile)) {
    $output = [];
    $return = 0;
    @exec("php -l " . escapeshellarg($authFile) . " 2>&1", $output, $return);
    if ($return === 0) {
        echo "   ✅ No syntax errors\n";
    } else {
        echo "   ❌ SYNTAX ERROR FOUND:\n";
        echo "   " . implode("\n   ", $output) . "\n";
    }
}

// Try to load config
echo "\n4. Testing config file load...\n";
try {
    if (file_exists($configFile)) {
        require_once $configFile;
        echo "   ✅ Config file loaded successfully\n";
        echo "   DB_NAME: " . (defined('DB_NAME') ? DB_NAME : 'NOT DEFINED') . "\n";
        echo "   DB_USER: " . (defined('DB_USER') ? DB_USER : 'NOT DEFINED') . "\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error loading config: " . $e->getMessage() . "\n";
} catch (ParseError $e) {
    echo "   ❌ Parse error in config: " . $e->getMessage() . "\n";
    echo "   Line: " . $e->getLine() . "\n";
}

echo "\nDone!\n";

