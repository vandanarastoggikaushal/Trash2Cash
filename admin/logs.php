<?php
$USER = getenv('BASIC_AUTH_USER') ?: 'admin';
$PASS = getenv('BASIC_AUTH_PASS') ?: 'changeme';
if (!isset($_SERVER['PHP_AUTH_USER'])) {
  header('WWW-Authenticate: Basic realm="Logs"');
  header('HTTP/1.0 401 Unauthorized');
  echo 'Unauthorized';
  exit;
}
if (!hash_equals($USER, $_SERVER['PHP_AUTH_USER']) || !hash_equals($PASS, $_SERVER['PHP_AUTH_PW'] ?? '')) {
  header('WWW-Authenticate: Basic realm="Logs"');
  header('HTTP/1.0 401 Unauthorized');
  echo 'Unauthorized';
  exit;
}

$dir = __DIR__ . '/../data';
@mkdir($dir, 0755, true);
$leadsFile = $dir . '/leads.json';
$msgsFile = $dir . '/messages.json';

function readJson($file) {
  if (!file_exists($file)) return [];
  $content = file_get_contents($file);
  $json = json_decode($content, true);
  return is_array($json) ? $json : [];
}

$leads = readJson($leadsFile);
$messages = readJson($msgsFile);

function h($s) { return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
?><!doctype html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Trash2Cash Admin Logs</title>
    <style>
      body { font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Inter, Arial; margin: 20px; }
      h1 { font-size: 20px; }
      pre { background:#f8fafc; padding:12px; border-radius:8px; overflow:auto; }
      .grid { display:grid; grid-template-columns: 1fr; gap:20px; }
      @media (min-width: 900px) { .grid { grid-template-columns: 1fr 1fr; } }
    </style>
  </head>
  <body>
    <h1>Trash2Cash NZ – Admin Logs</h1>
    <div class="grid">
      <section>
        <h2>Leads (<?php echo count($leads); ?>)</h2>
        <pre><?php echo h(json_encode(array_slice($leads, -50), JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES)); ?></pre>
      </section>
      <section>
        <h2>Messages (<?php echo count($messages); ?>)</h2>
        <pre><?php echo h(json_encode(array_slice($messages, -50), JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES)); ?></pre>
      </section>
    </div>
  </body>
</html>

