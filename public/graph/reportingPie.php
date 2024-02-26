<?php

echo '<script src="/js/charts/chartjs/4.3.2/chart.umd.js" crossorigin="anonymous"> </script>';

include_once __DIR__ . ("/../../functions/generalFunctions.php"); // DEBUG ONLY
// $_COOKIE['token'] = 'put token here';                          // DEBUG ONLY
$headers = array();
$headers[] = 'Content-length: 0';
$headers[] = 'Content-type: application/json';
$headers[] = 'Authorization: Bearer ' . $_COOKIE['token'];
$output = callApiGet("/reporting/searchComplete", $headers);
// we SHOULD have gotten an array.... but if not convert it back to one
if (! is_array($output)) {
  $output = json_decode($output, true);
}

// Result set is ASC from API.  So debug >> critical (1 >> 5)
$outputFiltered = json_decode($output['response'], true);
$reportCount=count($outputFiltered['data']);
//debugger($outputFiltered);

$incomplete=0;
$complete=0;
foreach ($outputFiltered['data'] as $reportCounts) {
  switch ($reportCounts['status']) {
    case "complete":
      $complete++;
      break;
    default:
      $incomplete++;
      break;
  }
}

$pieValue = $complete . ',' . $incomplete;
$pieName = '"complete Reports", "incomplete Reports"';
$pieColor = '"rgb(25, 135, 84)","rgb(220, 53, 69)"';
//debugger($pieValue);
//debugger($pieName);
//debugger($pieColor);
?>
<div>
<canvas id="reportingPie" width="250" height="250"></canvas>
</div>

<script>
  (() => {
  'use strict'
  // Graphs
  const ctx = document.getElementById('reportingPie')
  // eslint-disable-next-line no-unused-vars
  const myChart = new Chart(ctx, {
    type: 'pie',
    data: {
      labels: [ <?php echo $pieName; ?>
      ],
      datasets: [{
        data: [ <?php echo $pieValue ; ?>
        ],
        lineTension: 0,
        backgroundColor: [ <?php echo $pieColor; ?>
        ],
        borderColor: '#007bff',
        borderWidth: 4,
        pointBackgroundColor: '#007bff'
      }]
    },
    options: {
      plugins: {
        legend: {
          display: true
        },
        tooltip: {
          boxPadding: 3
        }
      }
    }
  })
})()

</script>
</body>
<br>
