<?php

echo '<script src="/js/charts/chartjs/4.3.2/chart.umd.js" crossorigin="anonymous"> </script>';

include_once __DIR__ . ("/../../functions/generalFunctions.php"); // DEBUG ONLY
// $_COOKIE['token'] = 'put token here';                          // DEBUG ONLY
$headers = array();
$headers[] = 'Content-length: 0';
$headers[] = 'Content-type: application/json';
$headers[] = 'Authorization: Bearer ' . $_COOKIE['token'];
$output = callApiGet("/events/activeEventCountList", $headers);
// we SHOULD have gotten an array.... but if not convert it back to one
if (! is_array($output)) {
  $output = json_decode($output, true);
}

// Result set is ASC from API.  So debug >> critical (1 >> 5)
$outputFiltered = json_decode($output['response'], true);
$eventCount=count($outputFiltered);

//debugger($outputFiltered);
$pieValue = "'0.01'";
$pieName = "'Public Visible Events'";
$pieColor = "'rgb(25, 135, 84)'";
/*
foreach ($outputFiltered['data'] as $eventCount) {
  switch ($eventCount['severity']) {
    case 1: $pieName .= "'Debug', ";         $pieColor .= "'rgb(108, 117, 125)', "; break;
    case 2: $pieName .= "'Informational', "; $pieColor .= "'rgb(13, 202, 240)', ";  break;
    case 3: $pieName .= "'Warning', ";       $pieColor .= "'rgb(54, 162, 235)', ";  break;
    case 4: $pieName .= "'Major', ";         $pieColor .= "'rgb(255, 193, 7)', ";   break;
    case 5: $pieName .= "'Critical', ";      $pieColor .= "'rgb(220, 53, 69)', ";   break;
  }
  $pieValue = $pieValue . ',' . $eventCount['count'];
}

//$pieName = rtrim($pieName, ',');
//debugger($pieName);
//echo "" . print_r($outputFiltered);
//exit();
$pieValue = rtrim(rtrim($pieValue, ' '), ',');
$pieName = rtrim(rtrim($pieName, ' '), ',');
$pieColor = rtrim(rtrim($pieColor, ' '), ',');
*/
?>
<div>
<canvas id="publicPie" width="250" height="250"></canvas>
</div>

<script>
  (() => {
  'use strict'
  // Graphs
  const ctx = document.getElementById('publicPie')
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
        borderWidth: 2,
        pointBackgroundColor: '#007bff'
      }]
    },
    options: {
      plugins: {
        legend: {
          display: true
        },
        tooltip: {
          boxPadding: 2
        }
      }
    }
  })
})()

</script>
</body>
<br>
