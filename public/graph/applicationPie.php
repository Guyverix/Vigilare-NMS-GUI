<?php

echo '<script src="/js/charts/chartjs/4.3.2/chart.umd.js" crossorigin="anonymous"> </script>';
// https://www.chartjs.org/docs/master/charts/doughnut.html
// https://www.chartjs.org/docs/latest/charts/doughnut.html


// Adding so we have our API call ability
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
//debugger($outputFiltered);

$eventCount = 0;
foreach ($outputFiltered['data'] as $eventSeverityList) {
  $eventCount = $eventCount + $eventSeverityList['count'];
}

$pieValue = '"0","' . $eventCount . '"';
$pieName = '"Application Events","All Events"';
$pieColor = "'rgb(220, 53, 69)','rgb(25, 135, 84)'";

//debugger($pieColor);
//debugger($pieValue);
//debugger($pieName);
?>
<canvas id="applicationPie" width="150" height="150"></canvas>
<script>
  (() => {
  'use strict'
  // Graphs
  const ctx = document.getElementById('applicationPie')
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
          display: false
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
