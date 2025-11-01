<?php
// Test contact form email - simulates actual form submission
header('Content-Type: text/plain');

echo "Testing contact form email functionality...\n\n";

// Simulate form data
$data = [
  'name' => 'Test User',
  'email' => 'test@example.com',
  'message' => 'This is a test message from the contact form.'
];

$name = trim($data['name']);
$email = trim($data['email']);
$message = trim($data['message']);

$to = 'collect@trash2cash.co.nz';
$subject = 'New Contact Form Message from ' . htmlspecialchars($name);

// Create email message (exact format as contact form)
$emailMessage = "You have received a new message from the Trash2Cash contact form.\n\n";
$emailMessage .= "Name: " . htmlspecialchars($name) . "\n";
$emailMessage .= "Email: " . htmlspecialchars($email) . "\n\n";
$emailMessage .= "Message:\n" . htmlspecialchars($message) . "\n\n";
$emailMessage .= "---\n";
$emailMessage .= "Submitted at: " . date('Y-m-d H:i:s') . " NZST\n";
$emailMessage .= "Reference ID: test123";

// Test with Hostinger default (let Hostinger choose From address)
echo "Testing Header Format 1 (Hostinger default - recommended):\n";
$headers1 = "MIME-Version: 1.0\r\n";
$headers1 .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers1 .= "Reply-To: " . htmlspecialchars($email, ENT_QUOTES) . "\r\n";
$headers1 .= "X-Mailer: PHP/" . phpversion() . "\r\n";

$result1 = mail($to, $subject, $emailMessage, $headers1);
echo "Result: " . ($result1 ? "SUCCESS (TRUE)" : "FAILED (FALSE)") . "\n";
echo "Note: Hostinger will use its default From address\n\n";

echo "Testing Header Format 2 (minimal - only Reply-To):\n";
$headers2 = "Reply-To: " . htmlspecialchars($email, ENT_QUOTES) . "\r\n";

$result2 = mail($to, $subject, $emailMessage, $headers2);
echo "Result: " . ($result2 ? "SUCCESS (TRUE)" : "FAILED (FALSE)") . "\n\n";

echo "Testing Header Format 3 (no headers - let PHP use defaults):\n";
$result3 = mail($to, $subject, $emailMessage);
echo "Result: " . ($result3 ? "SUCCESS (TRUE)" : "FAILED (FALSE)") . "\n\n";

echo "--- Email Details ---\n";
echo "To: $to (M365 external account)\n";
echo "Subject: $subject\n";
echo "From: Hostinger default (automatically set by mail server)\n";
echo "Reply-To: $email\n";
echo "Message length: " . strlen($emailMessage) . " bytes\n\n";

echo "NOTE: Even if mail() returns TRUE, the email might:\n";
echo "1. Go to spam folder\n";
echo "2. Be delayed by the mail server\n";
echo "3. Be blocked by recipient's spam filter\n";
echo "4. Need domain verification on Hostinger\n\n";

echo "Check:\n";
echo "- Spam folder for collect@trash2cash.co.nz\n";
echo "- Hostinger email logs\n";
echo "- Domain email settings in Hostinger panel\n";

?>

