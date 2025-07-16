<?php
/*
  Render Graphite-based graphs for a device's service check
*/

require_once __DIR__ . '/../../../functions/generalFunctions.php';
require_once __DIR__ . '/../../../config/api.php';
require_once __DIR__ . '/../functions/hostFunctions.php';

$headers = ['Authorization: Bearer ' . $_COOKIE['token']];
$quitEarly = 0;

// Collect POST vars
$specialHostname     = $_POST['specialHostname'] ?? null;
$hostname     = $_POST['hostname'] ?? null;
$id           = $_POST['id'] ?? null;
$checkType    = $_POST['checkType'] ?? null;
$checkName    = $_POST['checkName'] ?? null;
$startNumber  = $_POST['startNumber'] ?? '1';
$startRange   = $_POST['startRange'] ?? 'd';
$startTime    = '-' . $startNumber . $startRange;
$endNumber    = $_POST['endNumber'] ?? 'now';
$endRange     = $_POST['endRange'] ?? 'h';
$endTime      = ($endNumber === 'now') ? 'now' : '-' . $endNumber . $endRange;

// debugger($_POST);
//exit();


if (!$hostname || !$checkType || !$checkName) {
  loadUnknown("Missing required POST values.");
  exit;
}

if ( $checkName = 'laEntry') { $checkName = 'load'; }

// Make Graphite API call
$post = [
  'task' => 'findGraphs',
  'hostname' => $specialHostname,
  'checkName' => $checkName,
  'checkType' => $checkType,
  'from' => $startTime,
  'to' => $endTime
];

// debugger($post);

$rawRenderGraphs = callApiPost("/graphite/test", $post, $headers);
//debugger($rawRenderGraphs);
$renderGraphsResult = json_decode($rawRenderGraphs['response'], true);
//debugger($renderGraphResult);

// Begin page
echo '<div class="container">';
echo '<div class="text-center mt-5">';
echo '<h1>Available Graphite Graphs for <a href="/host/index.php?&page=deviceDetails.php&id=' . $id . '">' . htmlspecialchars($hostname) . '</a></h1><br>';
echo '</div>';

// Timeframe form
echo '<form id="changeTimes" method="POST" action="">';
echo '<table class="table bg-dark table-dark">';
echo '<thead><tr><th>From Range</th><th>Until Range</th></tr></thead>';
echo '<tbody><tr><td>';
echo '<input type="text" name="startNumber" value="' . htmlspecialchars($startNumber) . '" size="3"> ';
echo '<select name="startRange">';
foreach (['d' => 'days', 'h' => 'hours', 'w' => 'weeks', 'm' => 'months'] as $val => $label) {
  $selected = ($startRange === $val) ? 'selected' : '';
  echo "<option value=\"$val\" $selected>$label</option>";
}
echo '</select>';
echo '</td><td>';
echo '<input type="text" name="endNumber" value="' . htmlspecialchars($endNumber) . '" size="3"> ';
echo '<select name="endRange">';
foreach (['d' => 'days', 'h' => 'hours', 'w' => 'weeks', 'm' => 'months'] as $val => $label) {
  $selected = ($endRange === $val) ? 'selected' : '';
  echo "<option value=\"$val\" $selected>$label</option>";
}
echo '</select>';
echo '<input type="hidden" name="checkName" value="' . htmlspecialchars($checkName) . '">';
echo '<input type="hidden" name="hostname" value="' . htmlspecialchars($hostname) . '">';
echo '<input type="hidden" name="specialHostname" value="' . htmlspecialchars($specialHostname) . '">';
echo '<input type="hidden" name="checkType" value="' . htmlspecialchars($checkType) . '">';
echo '<input type="hidden" name="id" value="' . htmlspecialchars($id) . '">';
echo '</td></tr></tbody>';
echo '</table>';
echo '<center><button type="submit" class="btn btn-primary">Change Timeframe</button> ';
echo '<input type="button" class="btn btn-secondary" value="Back One Page" onclick="history.back()"/></center>';
echo '</form>';

// Begin graph output table
echo '<div class="container">';
echo '<table class="table table-striped table-hover bg-dark table-dark">';
echo '<thead><tr><th class="text-center">Graphite Graphs for ' . htmlspecialchars($checkName) . '</th></tr></thead>';
echo '<tbody>';

if ($renderGraphsResult['statusCode'] !== 200) {
  echo '<tr><td>' . loadUnknown($renderGraphsResult['data']) . '</td></tr>';
} elseif (!is_array($renderGraphsResult['data'][0]) && str_contains($renderGraphsResult['data'][0], 'failed to generate graph')) {
  echo '<tr><td>' . loadUnknown($renderGraphsResult['data'][0]) . '</td></tr>';
} else {
  foreach ($renderGraphsResult['data'][0] as $graphGroup) {
    if (!is_array($graphGroup)) {
      // echo "URL " . $graphGroup . "<br>";
      echo '<tr><td><center><img class="NO-CACHE" src="' . htmlspecialchars($graphGroup) . '" width="900" height="200" /></center></td></tr>';
    } else {
      foreach ($graphGroup as $subGraph) {
        $img = is_array($subGraph) ? $subGraph[0] : $subGraph;
        // echo "URL " . $img . "<br>";
        echo '<tr><td><center><img class="NO-CACHE" src="' . htmlspecialchars($img) . '" width="900" height="200" /></center></td></tr>';
      }
    }
  }
}

echo '</tbody>';
echo '</table>';
echo '</div>'; // container
