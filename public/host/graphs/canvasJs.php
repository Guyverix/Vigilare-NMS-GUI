<?php
// canvasJs.php - Render CanvasJS-based Graphs (Graphite JSON API)

require_once __DIR__ . '/../../../functions/generalFunctions.php';
require_once __DIR__ . "/../../../config/api.php";
require_once __DIR__ . "/../functions/hostFunctions.php";

$headers = ['Authorization: Bearer ' . $_COOKIE['token']];
$task = 'findGraphs';

$specialHostname = $_POST['specialHostname'] ?? '';
$hostname = $_POST['hostname'] ?? '';
$id       = $_POST['id'] ?? '';
$checkType = $_POST['checkType'] ?? '';
$checkName = $_POST['checkName'] ?? '';

$startNumber = $_POST['startNumber'] ?? '1';
$startRange  = $_POST['startRange']  ?? 'd';
$startTime   = "-{$startNumber}{$startRange}";

if (!empty($_POST['endNumber']) && $_POST['endNumber'] !== 'now') {
    $endNumber = $_POST['endNumber'];
    $endRange  = $_POST['endRange'] ?? 'h';
    $endTime   = "-{$endNumber}{$endRange}";
} else {
    $endNumber = 'now';
    $endRange = 'h';
    $endTime = '-1m';
}

$post = [
  'task' => $task,
  'hostname' => $specialHostname,
  'checkName' => $checkName,
  'checkType' => $checkType,
  'from' => $startTime,
  'to'   => $endTime
];

$rawRenderGraphs = callApiPost("/graphite/test", $post, $headers);
$renderGraphsResult = json_decode($rawRenderGraphs['response'], true);

$graphNumber = 0;
echo '<script src="/js/jquery/jquery-1.7.1.min.js"></script>';
echo '<script src="/js/charts/canvasjs/canvasjs.min.js"></script>';

echo '<div class="container mt-5">';
echo '<div class="text-center mb-4">';
echo '<h1>Available CanvasJS Graphs for <a href="/host/index.php?&page=deviceDetails.php&id=' . $id . '">' . htmlspecialchars($hostname) . '</a></h1>';
echo '</div>';

echo '<form id="changeTimes" method="POST" action="">';
echo '<table class="table bg-dark table-dark">';
echo '<thead><tr><th>From Range</th><th>Until Range</th></tr></thead>';
echo '<tbody><tr><td>';
echo '<input type="text" name="startNumber" value="' . $startNumber . '" size="3"> ';
echo '<select name="startRange">';
foreach (['d'=>'days','h'=>'hours','w'=>'weeks','m'=>'months'] as $val=>$label) {
  $sel = ($startRange == $val) ? 'selected' : '';
  echo "<option value=\"$val\" $sel>$label</option>";
}
echo '</select>';
echo '</td><td>';
echo '<input type="text" name="endNumber" value="' . $endNumber . '" size="3"> ';
echo '<select name="endRange">';
foreach (['d'=>'days','h'=>'hours','w'=>'weeks','m'=>'months'] as $val=>$label) {
  $sel = ($endRange == $val) ? 'selected' : '';
  echo "<option value=\"$val\" $sel>$label</option>";
}
echo '</select>';
echo '<input type="hidden" name="checkName" value="' . htmlspecialchars($checkName) . '">';
echo '<input type="hidden" name="specialHostname" value="' . htmlspecialchars($specialHostname) . '">';
echo '<input type="hidden" name="hostname" value="' . htmlspecialchars($hostname) . '">';
echo '<input type="hidden" name="checkType" value="' . htmlspecialchars($checkType) . '">';
echo '<input type="hidden" name="id" value="' . htmlspecialchars($id) . '">';
echo '</td></tr></tbody>';
echo '</table>';
echo '<div class="text-center"><button type="submit" class="btn btn-primary">Change Timeframe</button> <input type="button" class="btn btn-secondary" value="Back One Page" onclick="history.back()"></div>';
echo '</form>';

if ($renderGraphsResult['statusCode'] !== 200) {
  echo '<div class="alert alert-danger mt-4">' . loadUnknown($renderGraphsResult['data']) . '</div>';
  return;
}

$graphData = $renderGraphsResult['data'][0] ?? [];
foreach ($graphData as $checkNameKey => $checkMetrics) {
  foreach ($checkMetrics as $metricName => $urls) {
    $metricTitle = is_numeric($metricName) ? $checkNameKey : $metricName;
    $urlList = is_array($urls) ? $urls : [$urls];

    foreach ($urlList as $graphUrl) {
      $graphNumber++;
      $dataReturn = callUrlGet($graphUrl . '&format=json');
      $graphiteJson = json_decode($dataReturn['response'], true);

      if (!is_array($graphiteJson)) continue;

      $dataSeries = [];
      foreach ($graphiteJson as $series) {
        $points = [];
        foreach ($series['datapoints'] as $pt) {
          if ($pt[0] !== null) {
            $points[] = ['x' => $pt[1] * 1000, 'y' => $pt[0]];
          }
        }
        $dataSeries[] = [
          'type' => 'splineArea',
          'markerSize' => 5,
          'toolTipContent' => '{y}',
          'xValueType' => 'dateTime',
          'showInLegend' => true,
          'name' => $series['target'],
          'dataPoints' => $points
        ];
      }

      echo '<div class="my-4">';
      echo '<h5 class="text-center">' . htmlspecialchars($metricTitle) . '</h5>';
      echo '<div id="graphiteContainer_' . $graphNumber . '" style="height: 250px; width: 100%;"></div>';
      echo '<script type="text/javascript">
        $(function() {
          var chart = new CanvasJS.Chart("graphiteContainer_' . $graphNumber . '", {
            theme: "light2",
            zoomEnabled: true,
            animationEnabled: true,
            legend: {
              verticalAlign: "bottom",
              horizontalAlign: "center"
            },
            axisY: {
              reversed: false
            },
            data: ' . json_encode($dataSeries, JSON_NUMERIC_CHECK) . '
          });
          chart.render();
        });
      </script>';
      echo '</div>';
    }
  }
}

echo '</div>';
