<?php
// saveEventSetting.php
// Called by JavaScript to persist user settings via your setCookieSimple() method

require_once(__DIR__ . '/../../functions/generalFunctions.php');

$name  = $_GET['name'] ?? null;
$value = $_GET['value'] ?? null;
$path = $_GET['path'] ?? '/event/';

if (!$name || $value === null) {
  http_response_code(400);
  echo json_encode(["error" => "Missing name or value."]);
  exit;
}

// Lifetime: 100 days
//$path = '/event/';
$lifetime = 8640000; // 100 days in seconds

setCookieSimple($name, $value, $path, $lifetime);

header('Content-Type: application/json');
echo json_encode(["status" => "ok", "name" => $name, "value" => $value]);
