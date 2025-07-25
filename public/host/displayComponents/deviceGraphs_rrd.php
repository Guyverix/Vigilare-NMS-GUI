<?php
// deviceGraphs_rrd.php - Responsible for building the list of available RRD graphs and rendering selection UI

//require_once(__DIR__ . '/../../functions/generalFunctions.php');
//require_once(__DIR__ . '/../../config/api.php');
//require_once __DIR__ . "/functions/hostFunctions.php";

//debugger($storage);

$headers = [ 'Authorization: Bearer ' . $_COOKIE['token'] ];
$quitEarly = 0;

if (!isset($_POST['id'], $_POST['hostname'], $_POST['activeMonitors'])) {
  $quitEarly = 1;
} else {
  $id = $_POST['id'];
  $hostname = $_POST['hostname'];
  $activeMonitors = json_decode($_POST['activeMonitors'], true);
}

if ($quitEarly === 0) {
  $storage = array_unique(array_column($activeMonitors, 'storage'));

  $rrdTemplateMap = [];
  if (in_array('rrd', $storage)) {
    $post = [
      'hostname' => $hostname,
      'task'     => 'findGraphs'
    ];

    $rrdGraphsResp = callApiPost("/render/findRrd", $post, $headers);
    $rrdGraphs = json_decode($rrdGraphsResp['response'], true);

    $rrdFileList = array_map(fn($v) => '"' . $v . '"', $rrdGraphs['data']);
    $rrdFileCsv = implode(',', $rrdFileList);

    $post['files'] = $rrdFileCsv;
    $rrdTemplatesResp = callApiPost("/render/findRrdTemplates", $post, $headers);
    $rrdTemplates = json_decode($rrdTemplatesResp['response'], true);

    foreach ($rrdTemplates['data'] as $rrdTemplate) {
      foreach ($rrdTemplate as $fileName => $templateName) {
        $rrdTemplateMap[$templateName][] = $fileName;
      }
    }
  }

  echo '<div class="container">' . "\n";
  echo '<div class="text-center mt-5">' . "\n";
  // echo '<h1>Available RRD Graphs for <a href="/host/index.php?page=deviceDetails.php&id=' . $id . '">' . htmlspecialchars($hostname) . '</a></h1><br>' . "\n";
  echo '</div>' . "\n";

  echo '<div class="card mb-4">' . "\n";
  echo '<div class="card-header bg-primary text-white"><h5 class="mb-0">RRD Graph Templates</h5></div>' . "\n";
  echo '<div class="card-body">' . "\n";
  echo '<table class="table table-striped table-hover">' . "\n";
  echo '<thead><tr><th>Name</th><th>Action</th></tr></thead><tbody>' . "\n";

  foreach ($rrdTemplateMap as $templateName => $fileList) {
    $prettyName = ucwords(str_replace('_', ' ', preg_replace('/snmp/i', 'SNMP', $templateName)));
    if ($templateName === 'no_template') {
      $prettyName = basename(reset($fileList), '.rrd');
    }

    echo '<tr><td>' . $prettyName . "</td><td>\n";
    echo '<form method="POST" action="/host/index.php?page=/graphs/rrd.php">' . "\n";
    echo '<input type="hidden" name="templateName" value="' . htmlspecialchars($templateName) . '">' . "\n";
    echo '<input type="hidden" name="hostname" value="' . htmlspecialchars($hostname) . '">' . "\n";
    echo '<input type="hidden" name="id" value="' . htmlspecialchars($id) . '">' . "\n";
    echo '<input type="hidden" name="files" value=' . htmlspecialchars(json_encode($fileList)) . '>' . "\n";
    echo '<button type="submit" class="btn btn-sm btn-primary" value="View">View' . '</button>' . "\n";
//    echo '<button type="submit" class="btn btn-link">' . $prettyName . '</button>' . "\n";
    echo '</form>' . "\n";
    echo '</td></tr>' . "\n";
  }

  echo '</tbody>' . "\n";
  echo '</table>' . "\n";
  echo '</div>' . "\n"; // card-body
  echo '</div>' . "\n"; // card
  echo '</div>' . "\n"; // container
} else {
  loadUnknown("Missing or invalid input. Please reload the page or return to the device list.");
}
