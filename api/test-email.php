<?php
// Test email script for debugging
// Access this file directly in browser to test email sending

header('Content-Type: text/plain');

echo "Testing email functionality...\n\n";

$to = 'collect@trash2cash.co.nz';
$subject = 'Test Email from Trash2Cash';
$message = "This is a test email from Trash2Cash website.\n\n";
$message .= "If you receive this, email functionality is working correctly.\n";
$message .= "Sent at: " . date('Y-m-d H:i:s') . "\n";

$headers = [];
$headers[] = 'MIME-Version: 1.0';
$headers[] = 'Content-Type: text/plain; charset=UTF-8';
$headers[] = 'From: Trash2Cash NZ <noreply@trash2cash.co.nz>';
$headers[] = 'X-Mailer: PHP/' . phpversion();
$headersString = implode("\r\n", $headers);

echo "Attempting to send email to: $to\n";
echo "Subject: $subject\n\n";

$result = mail($to, $subject, $message, $headersString);

if ($result) {
  echo "SUCCESS: Email function returned TRUE\n";
  echo "Check your inbox at: $to\n";
} else {
  echo "FAILED: Email function returned FALSE\n";
  $error = error_get_last();
  if ($error) {
    echo "Error: " . $error['message'] . "\n";
  }
}

echo "\n--- PHP Mail Configuration ---\n";
echo "sendmail_path: " . ini_get('sendmail_path') . "\n";
echo "SMTP: " . ini_get('SMTP') . "\n";
echo "smtp_port: " . ini_get('smtp_port') . "\n";

?>

