<?php
/**
 * Migration Script: JSON Files to MySQL Database
 * 
 * This script migrates existing JSON data to MySQL database
 * 
 * INSTRUCTIONS:
 * 1. Make sure database is set up (run schema.sql first)
 * 2. Configure database credentials in includes/db-config.php
 * 3. Run this script: php database/migrate-json-to-mysql.php
 * 4. Verify data was migrated correctly
 * 5. Backup your JSON files before running
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

echo "=== Trash2Cash JSON to MySQL Migration ===\n\n";

// Test database connection
if (!testDBConnection()) {
    echo "❌ ERROR: Cannot connect to database!\n";
    echo "Please check your database configuration in includes/db-config.php\n";
    exit(1);
}

echo "✅ Database connection successful!\n\n";

$migrated = 0;
$errors = 0;

// Migrate Users (using the automatic migration function)
echo "Migrating users...\n";
require_once __DIR__ . '/../includes/auth.php';
$result = migrateUsersToDatabase();

if ($result['migrated'] > 0 || $result['skipped'] > 0) {
    $migrated += $result['migrated'];
    $errors += $result['errors'];
    echo "  ✓ Migrated: {$result['migrated']} users\n";
    echo "  ⏭️  Skipped: {$result['skipped']} users (already exist)\n";
    if ($result['errors'] > 0) {
        echo "  ✗ Errors: {$result['errors']} users\n";
    }
} else {
    echo "  ℹ {$result['message']}\n";
}

echo "\n";

// Migrate Leads
echo "Migrating leads...\n";
$leadsFile = __DIR__ . '/../data/leads.json';
if (file_exists($leadsFile)) {
    $leads = json_decode(file_get_contents($leadsFile), true);
    if (is_array($leads)) {
        foreach ($leads as $lead) {
            try {
                $person = $lead['person'] ?? [];
                $address = $lead['address'] ?? [];
                $pickup = $lead['pickup'] ?? [];
                $payout = $lead['payout'] ?? [];
                $confirm = $lead['confirm'] ?? [];
                
                // Extract appliances
                $appliances = [];
                if (isset($pickup['appliances']) && is_array($pickup['appliances'])) {
                    $appliances = $pickup['appliances'];
                }
                
                $sql = "INSERT INTO leads (
                    id, person_name, person_email, person_phone, person_marketing_optin,
                    address_street, address_suburb, address_city, address_postcode, address_access_notes,
                    pickup_type, pickup_cans_estimate, pickup_preferred_date, pickup_preferred_window,
                    payout_method, payout_bank_name, payout_bank_account,
                    payout_child_name, payout_child_bank_account,
                    payout_kiwisaver_provider, payout_kiwisaver_member_id,
                    items_are_clean, accepted_terms, appliances_json, created_at, status
                ) VALUES (
                    :id, :person_name, :person_email, :person_phone, :person_marketing_optin,
                    :address_street, :address_suburb, :address_city, :address_postcode, :address_access_notes,
                    :pickup_type, :pickup_cans_estimate, :pickup_preferred_date, :pickup_preferred_window,
                    :payout_method, :payout_bank_name, :payout_bank_account,
                    :payout_child_name, :payout_child_bank_account,
                    :payout_kiwisaver_provider, :payout_kiwisaver_member_id,
                    :items_are_clean, :accepted_terms, :appliances_json, :created_at, :status
                ) ON DUPLICATE KEY UPDATE status = VALUES(status)";
                
                $params = [
                    ':id' => $lead['id'] ?? bin2hex(random_bytes(6)),
                    ':person_name' => $person['fullName'] ?? '',
                    ':person_email' => $person['email'] ?? '',
                    ':person_phone' => $person['phone'] ?? '',
                    ':person_marketing_optin' => ($person['marketingOptIn'] ?? false) ? 1 : 0,
                    ':address_street' => $address['street'] ?? '',
                    ':address_suburb' => $address['suburb'] ?? '',
                    ':address_city' => $address['city'] ?? '',
                    ':address_postcode' => $address['postcode'] ?? '',
                    ':address_access_notes' => $address['accessNotes'] ?? null,
                    ':pickup_type' => $pickup['type'] ?? 'cans',
                    ':pickup_cans_estimate' => $pickup['cansEstimate'] ?? null,
                    ':pickup_preferred_date' => $pickup['preferredDate'] ?? null,
                    ':pickup_preferred_window' => $pickup['preferredWindow'] ?? null,
                    ':payout_method' => $payout['method'] ?? 'bank',
                    ':payout_bank_name' => $payout['bank']['name'] ?? null,
                    ':payout_bank_account' => $payout['bank']['accountNumber'] ?? null,
                    ':payout_child_name' => $payout['child']['childName'] ?? null,
                    ':payout_child_bank_account' => $payout['child']['bankAccount'] ?? null,
                    ':payout_kiwisaver_provider' => $payout['kiwiSaver']['provider'] ?? null,
                    ':payout_kiwisaver_member_id' => $payout['kiwiSaver']['memberId'] ?? null,
                    ':items_are_clean' => ($confirm['itemsAreClean'] ?? false) ? 1 : 0,
                    ':accepted_terms' => ($confirm['acceptedTerms'] ?? false) ? 1 : 0,
                    ':appliances_json' => !empty($appliances) ? json_encode($appliances) : null,
                    ':created_at' => $lead['createdAt'] ?? gmdate('Y-m-d H:i:s'),
                    ':status' => 'pending'
                ];
                
                if (dbExecute($sql, $params) !== false) {
                    $migrated++;
                    echo "  ✓ Migrated lead: {$lead['id']}\n";
                } else {
                    $errors++;
                    echo "  ✗ Failed to migrate lead: {$lead['id']}\n";
                }
            } catch (Exception $e) {
                $errors++;
                echo "  ✗ Error migrating lead {$lead['id']}: " . $e->getMessage() . "\n";
            }
        }
    }
} else {
    echo "  ℹ No leads.json file found (skipping)\n";
}

echo "\n";

// Migrate Messages
echo "Migrating messages...\n";
$messagesFile = __DIR__ . '/../data/messages.json';
if (file_exists($messagesFile)) {
    $messages = json_decode(file_get_contents($messagesFile), true);
    if (is_array($messages)) {
        foreach ($messages as $message) {
            try {
                $sql = "INSERT INTO messages (id, name, email, message, created_at, read) 
                        VALUES (:id, :name, :email, :message, :created_at, 0)
                        ON DUPLICATE KEY UPDATE read = VALUES(read)";
                
                $params = [
                    ':id' => $message['id'] ?? bin2hex(random_bytes(6)),
                    ':name' => $message['name'] ?? '',
                    ':email' => $message['email'] ?? '',
                    ':message' => $message['message'] ?? '',
                    ':created_at' => $message['createdAt'] ?? gmdate('Y-m-d H:i:s')
                ];
                
                if (dbExecute($sql, $params) !== false) {
                    $migrated++;
                    echo "  ✓ Migrated message: {$message['id']}\n";
                } else {
                    $errors++;
                    echo "  ✗ Failed to migrate message: {$message['id']}\n";
                }
            } catch (Exception $e) {
                $errors++;
                echo "  ✗ Error migrating message {$message['id']}: " . $e->getMessage() . "\n";
            }
        }
    }
} else {
    echo "  ℹ No messages.json file found (skipping)\n";
}

echo "\n";
echo "=== Migration Complete ===\n";
echo "✅ Migrated: $migrated records\n";
if ($errors > 0) {
    echo "❌ Errors: $errors records\n";
} else {
    echo "✅ No errors!\n";
}
echo "\n";
echo "Next steps:\n";
echo "1. Verify data in phpMyAdmin\n";
echo "2. Test the website functionality\n";
echo "3. Keep JSON files as backup (don't delete yet)\n";
echo "4. Once confirmed working, you can archive the JSON files\n";

