<?php
/**
 * Payments Helper Functions
 * Handles payout history and balances for users (MySQL with JSON fallback)
 */

// Attempt to load database helper (safe if already included)
$dbHelper = __DIR__ . '/db.php';
if (file_exists($dbHelper)) {
    require_once $dbHelper;
}

// JSON fallback file for payments (used when DB unavailable)
if (!defined('USER_PAYMENTS_FILE')) {
    define('USER_PAYMENTS_FILE', __DIR__ . '/../data/payments.json');
}

/**
 * Check if database functions are available for payments.
 *
 * @return bool
 */
function isPaymentsDatabaseAvailable() {
    if (function_exists('isDatabaseAvailable')) {
        return isDatabaseAvailable();
    }
    return function_exists('getDB') && getDB() !== null;
}

/**
 * Normalize payment record keys for consistent output.
 *
 * @param array $payment
 * @return array
 */
function normalizePaymentRecord(array $payment) {
    return [
        'id' => $payment['id'] ?? '',
        'userId' => $payment['user_id'] ?? ($payment['userId'] ?? ''),
        'amount' => isset($payment['amount']) ? (float) $payment['amount'] : 0.0,
        'currency' => $payment['currency'] ?? 'NZD',
        'reference' => $payment['reference'] ?? '',
        'notes' => $payment['notes'] ?? '',
        'status' => $payment['status'] ?? 'completed',
        'paymentDate' => $payment['payment_date'] ?? ($payment['paymentDate'] ?? null),
        'createdAt' => $payment['created_at'] ?? ($payment['createdAt'] ?? null),
        'updatedAt' => $payment['updated_at'] ?? ($payment['updatedAt'] ?? null)
    ];
}

/**
 * Load payments from JSON fallback storage.
 *
 * @return array
 */
function loadPaymentsFromJson() {
    if (!file_exists(USER_PAYMENTS_FILE)) {
        return [];
    }

    $data = json_decode(file_get_contents(USER_PAYMENTS_FILE), true);
    if (!is_array($data)) {
        return [];
    }

    return $data;
}

/**
 * Save payments to JSON fallback storage.
 *
 * @param array $payments
 * @return bool
 */
function savePaymentsToJson(array $payments) {
    $dir = dirname(USER_PAYMENTS_FILE);
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }

    return file_put_contents(USER_PAYMENTS_FILE, json_encode($payments, JSON_PRETTY_PRINT)) !== false;
}

/**
 * Retrieve payment history for a specific user.
 *
 * @param string $userId
 * @return array
 */
function getUserPayments($userId) {
    if (empty($userId)) {
        return [];
    }

    if (isPaymentsDatabaseAvailable() && function_exists('dbQuery')) {
        $rows = dbQuery(
            "SELECT id, user_id, amount, currency, reference, notes, status, payment_date, created_at, updated_at 
             FROM user_payments 
             WHERE user_id = :user_id 
             ORDER BY payment_date DESC, created_at DESC",
            [':user_id' => $userId]
        );

        if ($rows === false) {
            return [];
        }

        return array_map('normalizePaymentRecord', $rows);
    }

    // JSON fallback
    $payments = loadPaymentsFromJson();
    $userPayments = array_filter($payments, function ($payment) use ($userId) {
        $paymentUserId = $payment['user_id'] ?? ($payment['userId'] ?? null);
        return $paymentUserId === $userId;
    });

    usort($userPayments, function ($a, $b) {
        $dateA = $a['payment_date'] ?? ($a['paymentDate'] ?? '');
        $dateB = $b['payment_date'] ?? ($b['paymentDate'] ?? '');
        if ($dateA === $dateB) {
            $createdA = $a['created_at'] ?? ($a['createdAt'] ?? '');
            $createdB = $b['created_at'] ?? ($b['createdAt'] ?? '');
            return strcmp($createdB, $createdA);
        }
        return strcmp($dateB, $dateA);
    });

    return array_map('normalizePaymentRecord', $userPayments);
}

/**
 * Calculate the current balance for a user.
 *
 * @param string $userId
 * @param array $statuses Filter payments by statuses (default: completed)
 * @return float
 */
function getUserBalance($userId, array $statuses = ['completed']) {
    if (empty($userId)) {
        return 0.0;
    }

    if (isPaymentsDatabaseAvailable() && function_exists('dbQueryOne')) {
        $placeholders = [];
        $params = [':user_id' => $userId];
        foreach ($statuses as $idx => $status) {
            $placeholder = ':status' . $idx;
            $placeholders[] = $placeholder;
            $params[$placeholder] = $status;
        }
        if (empty($placeholders)) {
            $placeholders[] = ':status0';
            $params[':status0'] = 'completed';
        }

        $sql = sprintf(
            "SELECT COALESCE(SUM(amount), 0) AS total 
             FROM user_payments 
             WHERE user_id = :user_id AND status IN (%s)",
            implode(', ', $placeholders)
        );

        $row = dbQueryOne($sql, $params);
        if ($row && isset($row['total'])) {
            return (float) $row['total'];
        }

        return 0.0;
    }

    // JSON fallback
    $payments = getUserPayments($userId);
    $total = 0.0;
    foreach ($payments as $payment) {
        if (in_array($payment['status'] ?? 'completed', $statuses, true)) {
            $total += (float) $payment['amount'];
        }
    }

    return $total;
}

/**
 * Record a payment for a user.
 *
 * @param string $userId
 * @param float $amount
 * @param string|null $reference
 * @param string|null $notes
 * @param string|null $paymentDate
 * @param string $status
 * @param string $currency
 * @return bool
 */
function recordUserPayment($userId, $amount, $reference = null, $notes = null, $paymentDate = null, $status = 'completed', $currency = 'NZD') {
    if (empty($userId) || !is_numeric($amount)) {
        return false;
    }

    $paymentId = bin2hex(random_bytes(8));
    $paymentDate = $paymentDate ?: gmdate('Y-m-d');
    $createdAt = gmdate('Y-m-d H:i:s');

    if (isPaymentsDatabaseAvailable() && function_exists('dbExecute')) {
        $sql = "INSERT INTO user_payments (id, user_id, amount, currency, reference, notes, status, payment_date, created_at)
                VALUES (:id, :user_id, :amount, :currency, :reference, :notes, :status, :payment_date, :created_at)";

        $params = [
            ':id' => $paymentId,
            ':user_id' => $userId,
            ':amount' => $amount,
            ':currency' => $currency,
            ':reference' => $reference,
            ':notes' => $notes,
            ':status' => $status,
            ':payment_date' => $paymentDate,
            ':created_at' => $createdAt
        ];

        return dbExecute($sql, $params) !== false;
    }

    // JSON fallback
    $payments = loadPaymentsFromJson();
    $payments[] = [
        'id' => $paymentId,
        'user_id' => $userId,
        'amount' => (float) $amount,
        'currency' => $currency,
        'reference' => $reference,
        'notes' => $notes,
        'status' => $status,
        'payment_date' => $paymentDate,
        'created_at' => $createdAt,
        'updated_at' => null
    ];

    return savePaymentsToJson($payments);
}

/**
 * Retrieve balances for all users (useful for reporting).
 *
 * @param array $statuses
 * @return array
 */
function getAllUserBalances(array $statuses = ['completed']) {
    if (isPaymentsDatabaseAvailable() && function_exists('dbQuery')) {
        $placeholders = [];
        $params = [];
        foreach ($statuses as $idx => $status) {
            $placeholder = ':status' . $idx;
            $placeholders[] = $placeholder;
            $params[$placeholder] = $status;
        }
        if (empty($placeholders)) {
            $placeholders[] = ':status0';
            $params[':status0'] = 'completed';
        }

        $sql = sprintf(
            "SELECT user_id, COALESCE(SUM(amount), 0) AS total
             FROM user_payments
             WHERE status IN (%s)
             GROUP BY user_id",
            implode(', ', $placeholders)
        );

        $rows = dbQuery($sql, $params);
        if ($rows === false) {
            return [];
        }

        $balances = [];
        foreach ($rows as $row) {
            $balances[$row['user_id']] = (float) $row['total'];
        }
        return $balances;
    }

    // JSON fallback
    $payments = loadPaymentsFromJson();
    $balances = [];
    foreach ($payments as $payment) {
        $status = $payment['status'] ?? 'completed';
        if (!in_array($status, $statuses, true)) {
            continue;
        }
        $userId = $payment['user_id'] ?? ($payment['userId'] ?? null);
        if (empty($userId)) {
            continue;
        }
        if (!isset($balances[$userId])) {
            $balances[$userId] = 0.0;
        }
        $balances[$userId] += (float) ($payment['amount'] ?? 0);
    }

    return $balances;
}


