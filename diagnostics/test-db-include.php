<?php
/**
 * Test db.php include
 * This tests if db.php can be loaded without errors
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

$rootDir = dirname(__DIR__);

echo "Testing db.php include...\n\n";

// Test 1: Load config.php
echo "1. Loading config.php...\n";
try {
    require_once $rootDir . '/includes/config.php';
    echo "   ✅ config.php loaded\n";
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
    exit(1);
} catch (ParseError $e) {
    echo "   ❌ Parse Error: " . $e->getMessage() . " on line " . $e->getLine() . "\n";
    exit(1);
}

// Test 2: Load db.php
echo "\n2. Loading db.php...\n";
try {
    require_once $rootDir . '/includes/db.php';
    echo "   ✅ db.php loaded\n";
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
    echo "   Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
} catch (ParseError $e) {
    echo "   ❌ Parse Error: " . $e->getMessage() . " on line " . $e->getLine() . "\n";
    exit(1);
} catch (Error $e) {
    echo "   ❌ Fatal Error: " . $e->getMessage() . " on line " . $e->getLine() . "\n";
    echo "   File: " . $e->getFile() . "\n";
    exit(1);
}

// Test 3: Check if functions are defined
echo "\n3. Checking functions...\n";
$functions = ['getDB', 'testDBConnection', 'dbQuery', 'dbQueryOne', 'dbExecute'];
foreach ($functions as $func) {
    if (function_exists($func)) {
        echo "   ✅ $func() exists\n";
    } else {
        echo "   ❌ $func() NOT FOUND\n";
    }
}

// Test 4: Check constants
echo "\n4. Checking constants...\n";
$constants = ['DB_HOST', 'DB_PORT', 'DB_NAME', 'DB_USER', 'DB_PASS', 'DB_CHARSET'];
foreach ($constants as $const) {
    if (defined($const)) {
        $value = constant($const);
        if ($const === 'DB_PASS') {
            $value = $value ? '***SET***' : 'NOT SET';
        }
        echo "   ✅ $const = " . (is_string($value) ? htmlspecialchars($value) : $value) . "\n";
    } else {
        echo "   ❌ $const NOT DEFINED\n";
    }
}

// Test 5: Test database connection
echo "\n5. Testing database connection...\n";
if (function_exists('testDBConnection')) {
    if (testDBConnection()) {
        echo "   ✅ Database connection successful!\n";
    } else {
        echo "   ❌ Database connection failed\n";
    }
} else {
    echo "   ❌ testDBConnection() function not found\n";
}

echo "\n✅ All tests completed!\n";

