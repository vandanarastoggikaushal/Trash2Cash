<?php
/**
 * User Migration Script: JSON to MySQL Database
 * 
 * This script migrates users created during backup mode (when DB was unavailable)
 * to the database once it becomes available.
 * 
 * INSTRUCTIONS:
 * 1. Make sure database is set up and configured
 * 2. Run this script: php database/migrate-users-only.php
 * 3. Or it will run automatically when users are accessed (via getUsers())
 * 
 * This script is safe to run multiple times - it will skip users that already exist.
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

echo "=== Trash2Cash User Migration (JSON to MySQL) ===\n\n";

// Test database connection
if (!testDBConnection()) {
    echo "❌ ERROR: Cannot connect to database!\n";
    echo "Please check your database configuration in includes/db-config.php\n";
    exit(1);
}

echo "✅ Database connection successful!\n\n";

// Run migration
echo "Migrating users from JSON to database...\n";
$result = migrateUsersToDatabase();

echo "\n=== Migration Results ===\n";
echo "✅ Migrated: {$result['migrated']} users\n";
echo "⏭️  Skipped: {$result['skipped']} users (already exist in database)\n";
if ($result['errors'] > 0) {
    echo "❌ Errors: {$result['errors']} users\n";
} else {
    echo "✅ No errors!\n";
}

if (!empty($result['message'])) {
    echo "\n{$result['message']}\n";
}

echo "\n=== Next Steps ===\n";
echo "1. Verify users in phpMyAdmin\n";
echo "2. Test login with migrated users\n";
echo "3. Keep JSON file as backup (don't delete yet)\n";
echo "4. Once confirmed working, you can archive the JSON file\n";
echo "\nNote: Users will be automatically migrated when accessed via getUsers()\n";
echo "      This script is safe to run multiple times.\n";

