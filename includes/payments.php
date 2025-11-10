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
    $paymentDate = $payment['payment_date'] ?? ($payment['paymentDate'] ?? null);
    if (empty($paymentDate) && !empty($payment['created_at'])) {
        $paymentDate = substr($payment['created_at'], 0, 10);
    }
    if (empty($paymentDate)) {
        $paymentDate = gmdate('Y-m-d');
    }

    $createdAt = $payment['created_at'] ?? ($payment['createdAt'] ?? null);
    if (empty($createdAt)) {
        $createdAt = $paymentDate . ' 00:00:00';
    }

    $normalized = [
        'id' => $payment['id'] ?? '',
        'userId' => $payment['user_id'] ?? ($payment['userId'] ?? ''),
        'amount' => isset($payment['amount']) ? (float) $payment['amount'] : 0.0,
        'currency' => $payment['currency'] ?? 'NZD',
        'reference' => $payment['reference'] ?? '',
        'notes' => (string) ($payment['notes'] ?? ''),
        'status' => $payment['status'] ?? 'completed',
        'paymentDate' => $paymentDate,
        'createdAt' => $createdAt,
        'updatedAt' => $payment['updated_at'] ?? ($payment['updatedAt'] ?? null)
    ];

    if (isset($payment['user_username'])) {
        $normalized['username'] = $payment['user_username'];
    } elseif (isset($payment['username'])) {
        $normalized['username'] = $payment['username'];
    }

    if (isset($payment['user_first_name'])) {
        $normalized['firstName'] = $payment['user_first_name'];
    } elseif (isset($payment['firstName'])) {
        $normalized['firstName'] = $payment['firstName'];
    } elseif (isset($payment['first_name'])) {
        $normalized['firstName'] = $payment['first_name'];
    }

    if (isset($payment['user_last_name'])) {
        $normalized['lastName'] = $payment['user_last_name'];
    } elseif (isset($payment['lastName'])) {
        $normalized['lastName'] = $payment['lastName'];
    } elseif (isset($payment['last_name'])) {
        $normalized['lastName'] = $payment['last_name'];
    }

    if (isset($payment['user_email'])) {
        $normalized['email'] = $payment['user_email'];
    } elseif (isset($payment['email'])) {
        $normalized['email'] = $payment['email'];
    }

    return $normalized;
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

    $status = strtolower((string) $status);
    $validStatuses = ['pending', 'processing', 'completed', 'failed', 'cancelled'];
    if (!in_array($status, $validStatuses, true)) {
        $status = 'completed';
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
 * Fetch a single payment by its identifier.
 *
 * @param string $paymentId
 * @return array|null
 */
function getPaymentById($paymentId) {
    if (empty($paymentId)) {
        return null;
    }

    if (isPaymentsDatabaseAvailable() && function_exists('dbQueryOne')) {
        $row = dbQueryOne(
            "SELECT id, user_id, amount, currency, reference, notes, status, payment_date, created_at, updated_at
             FROM user_payments
             WHERE id = :id",
            [':id' => $paymentId]
        );

        return $row ? normalizePaymentRecord($row) : null;
    }

    $payments = loadPaymentsFromJson();
    foreach ($payments as $payment) {
        if (($payment['id'] ?? null) === $paymentId) {
            return normalizePaymentRecord($payment);
        }
    }

    return null;
}

/**
 * Update status and metadata for a payment.
 *
 * @param string $paymentId
 * @param string $newStatus
 * @param array $updates
 * @return bool
 */
function updatePaymentStatus($paymentId, $newStatus, array $updates = []) {
    if (empty($paymentId) || empty($newStatus)) {
        return false;
    }

    $newStatus = strtolower((string) $newStatus);
    $validStatuses = ['pending', 'processing', 'completed', 'failed', 'cancelled'];
    if (!in_array($newStatus, $validStatuses, true)) {
        return false;
    }

    $now = gmdate('Y-m-d H:i:s');

    if (isPaymentsDatabaseAvailable() && function_exists('dbExecute')) {
        $setParts = ['status = :status', 'updated_at = :updated_at'];
        $params = [
            ':id' => $paymentId,
            ':status' => $newStatus,
            ':updated_at' => $now
        ];

        if (!empty($updates['payment_date'])) {
            $setParts[] = 'payment_date = :payment_date';
            $params[':payment_date'] = $updates['payment_date'];
        }

        if (array_key_exists('reference', $updates)) {
            $setParts[] = 'reference = :reference';
            $params[':reference'] = $updates['reference'];
        }

        if (array_key_exists('notes', $updates)) {
            $setParts[] = 'notes = :notes';
            $params[':notes'] = $updates['notes'];
        }

        $sql = 'UPDATE user_payments SET ' . implode(', ', $setParts) . ' WHERE id = :id';
        return dbExecute($sql, $params) !== false;
    }

    $payments = loadPaymentsFromJson();
    $updated = false;
    foreach ($payments as &$payment) {
        if (($payment['id'] ?? null) === $paymentId) {
            $payment['status'] = $newStatus;
            $payment['updated_at'] = $now;
            if (!empty($updates['payment_date'])) {
                $payment['payment_date'] = $updates['payment_date'];
            }
            if (array_key_exists('reference', $updates)) {
                $payment['reference'] = $updates['reference'];
            }
            if (array_key_exists('notes', $updates)) {
                $payment['notes'] = $updates['notes'];
            }
            $updated = true;
            break;
        }
    }
    unset($payment);

    if ($updated) {
        return savePaymentsToJson($payments);
    }

    return false;
}

/**
 * Determine if a payment already exists for a user/reference pair.
 *
 * @param string $userId
 * @param string $reference
 * @return bool
 */
function paymentExistsForReference($userId, $reference) {
    if (empty($userId) || $reference === null || $reference === '') {
        return false;
    }

    if (isPaymentsDatabaseAvailable() && function_exists('dbQueryOne')) {
        $row = dbQueryOne(
            'SELECT id FROM user_payments WHERE user_id = :user_id AND reference = :reference LIMIT 1',
            [
                ':user_id' => $userId,
                ':reference' => $reference
            ]
        );
        return $row !== false && !empty($row);
    }

    $payments = loadPaymentsFromJson();
    foreach ($payments as $payment) {
        $paymentUserId = $payment['user_id'] ?? ($payment['userId'] ?? null);
        if ($paymentUserId === $userId && ($payment['reference'] ?? '') === $reference) {
            return true;
        }
    }

    return false;
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

/**
 * Fetch recent payments, optionally filtered by user or status.
 *
 * @param int $limit
 * @param string|null $userId
 * @param array $statuses
 * @return array
 */
function getRecentPayments($limit = 50, $userId = null, array $statuses = []) {
    $limit = (int) $limit;
    if ($limit <= 0) {
        $limit = 50;
    }

    if (isPaymentsDatabaseAvailable() && function_exists('dbQuery')) {
        $params = [];
        $conditions = [];

        if (!empty($userId)) {
            $conditions[] = 'p.user_id = :user_id';
            $params[':user_id'] = $userId;
        }

        if (!empty($statuses)) {
            $statusPlaceholders = [];
            foreach ($statuses as $idx => $status) {
                $placeholder = ':status' . $idx;
                $statusPlaceholders[] = $placeholder;
                $params[$placeholder] = $status;
            }
            $conditions[] = 'p.status IN (' . implode(', ', $statusPlaceholders) . ')';
        }

        $sql = "SELECT 
                    p.id,
                    p.user_id,
                    p.amount,
                    p.currency,
                    p.reference,
                    p.notes,
                    p.status,
                    p.payment_date,
                    p.created_at,
                    p.updated_at,
                    u.username AS user_username,
                    u.email AS user_email,
                    u.first_name AS user_first_name,
                    u.last_name AS user_last_name
                FROM user_payments p
                LEFT JOIN users u ON u.id = p.user_id";

        if (!empty($conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $sql .= ' ORDER BY p.payment_date DESC, p.created_at DESC';
        $sql .= ' LIMIT ' . $limit;

        $rows = dbQuery($sql, $params);
        if ($rows === false) {
            return [];
        }

        return array_map('normalizePaymentRecord', $rows);
    }

    // JSON fallback
    $payments = loadPaymentsFromJson();
    if (empty($payments)) {
        return [];
    }

    if (function_exists('getUsers')) {
        $users = getUsers();
        $userMap = [];
        foreach ($users as $user) {
            $userMap[$user['id']] = $user;
        }
    } else {
        $userMap = [];
    }

    $filtered = array_filter($payments, function ($payment) use ($userId, $statuses) {
        $matchesUser = empty($userId) || (($payment['user_id'] ?? ($payment['userId'] ?? null)) === $userId);
        $status = $payment['status'] ?? 'completed';
        $matchesStatus = empty($statuses) || in_array($status, $statuses, true);
        return $matchesUser && $matchesStatus;
    });

    usort($filtered, function ($a, $b) {
        $dateA = $a['payment_date'] ?? ($a['paymentDate'] ?? '');
        $dateB = $b['payment_date'] ?? ($b['paymentDate'] ?? '');
        if ($dateA === $dateB) {
            $createdA = $a['created_at'] ?? ($a['createdAt'] ?? '');
            $createdB = $b['created_at'] ?? ($b['createdAt'] ?? '');
            return strcmp($createdB, $createdA);
        }
        return strcmp($dateB, $dateA);
    });

    $filtered = array_slice($filtered, 0, $limit);

    $normalized = [];
    foreach ($filtered as $payment) {
        if (isset($userMap[$payment['user_id'] ?? ($payment['userId'] ?? '')])) {
            $user = $userMap[$payment['user_id'] ?? ($payment['userId'] ?? '')];
            $payment['username'] = $user['username'] ?? '';
            $payment['firstName'] = $user['firstName'] ?? ($user['first_name'] ?? '');
            $payment['lastName'] = $user['lastName'] ?? ($user['last_name'] ?? '');
            $payment['email'] = $user['email'] ?? '';
        }
        $normalized[] = normalizePaymentRecord($payment);
    }

    return $normalized;
}


/**
 * Retrieve payments filtered by status list.
 *
 * @param array $statuses
 * @param int|null $limit
 * @return array
 */
function getPaymentsByStatus(array $statuses, $limit = null) {
    if (empty($statuses)) {
        return [];
    }

    $validStatuses = ['pending', 'processing', 'completed', 'failed', 'cancelled'];
    $filteredStatuses = array_values(array_intersect(array_map('strtolower', $statuses), $validStatuses));
    if (empty($filteredStatuses)) {
        return [];
    }

    $limitClause = '';
    $limit = $limit !== null ? (int) $limit : null;
    if ($limit !== null && $limit > 0) {
        $limitClause = ' LIMIT ' . $limit;
    }

    if (isPaymentsDatabaseAvailable() && function_exists('dbQuery')) {
        $params = [];
        $statusPlaceholders = [];
        foreach ($filteredStatuses as $idx => $status) {
            $placeholder = ':status' . $idx;
            $statusPlaceholders[] = $placeholder;
            $params[$placeholder] = $status;
        }

        $sql = "SELECT 
                    p.id,
                    p.user_id,
                    p.amount,
                    p.currency,
                    p.reference,
                    p.notes,
                    p.status,
                    p.payment_date,
                    p.created_at,
                    p.updated_at,
                    u.username AS user_username,
                    u.email AS user_email,
                    u.first_name AS user_first_name,
                    u.last_name AS user_last_name
                FROM user_payments p
                LEFT JOIN users u ON u.id = p.user_id
                WHERE p.status IN (" . implode(', ', $statusPlaceholders) . ")
                ORDER BY p.payment_date ASC, p.created_at ASC" . $limitClause;

        $rows = dbQuery($sql, $params);
        if ($rows === false) {
            return [];
        }

        return array_map('normalizePaymentRecord', $rows);
    }

    $payments = loadPaymentsFromJson();
    if (empty($payments)) {
        return [];
    }

    $userMap = [];
    if (function_exists('getUsers')) {
        foreach (getUsers() as $user) {
            if (!empty($user['id'])) {
                $userMap[$user['id']] = $user;
            }
        }
    }

    $matched = [];
    foreach ($payments as $payment) {
        $status = strtolower($payment['status'] ?? 'completed');
        if (!in_array($status, $filteredStatuses, true)) {
            continue;
        }
        $userId = $payment['user_id'] ?? ($payment['userId'] ?? null);
        if ($userId && isset($userMap[$userId])) {
            $user = $userMap[$userId];
            $payment['username'] = $user['username'] ?? '';
            $payment['firstName'] = $user['firstName'] ?? ($user['first_name'] ?? '');
            $payment['lastName'] = $user['lastName'] ?? ($user['last_name'] ?? '');
            $payment['email'] = $user['email'] ?? '';
        }
        $matched[] = $payment;
    }

    usort($matched, function ($a, $b) {
        $dateA = $a['payment_date'] ?? ($a['paymentDate'] ?? '');
        $dateB = $b['payment_date'] ?? ($b['paymentDate'] ?? '');
        if ($dateA === $dateB) {
            $createdA = $a['created_at'] ?? ($a['createdAt'] ?? '');
            $createdB = $b['created_at'] ?? ($b['createdAt'] ?? '');
            return strcmp($createdA, $createdB);
        }
        return strcmp($dateA, $dateB);
    });

    if ($limit !== null && $limit > 0) {
        $matched = array_slice($matched, 0, $limit);
    }

    return array_map('normalizePaymentRecord', $matched);
}


