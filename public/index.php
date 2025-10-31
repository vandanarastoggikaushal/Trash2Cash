<?php
// Fallback to serve the exported index.html if Apache isn't picking it up
$index = __DIR__ . '/index.html';
if (file_exists($index)) {
  header('Content-Type: text/html; charset=utf-8');
  readfile($index);
  exit;
}
http_response_code(404);
echo 'Not Found';
?>

