<?php

echo '<script src="/js/charts/chartjs/4.3.2/chart.umd.js" crossorigin="anonymous"> </script>';

include_once __DIR__ . ("/../../functions/generalFunctions.php"); // DEBUG ONLY
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

$eventCount = 0;
foreach ($outputFiltered['data'] as $eventSeverityList) {
  $eventCount = $eventCount + $eventSeverityList['count'];
}

//debugger($eventCount);
//debugger($outputFiltered);

$pieValue = '"0","' . $eventCount .'"';
$pieName = "'Public Visible Events','Active Events'";
$pieColor = "'rgb(220, 53, 69)','rgb(25, 135, 84)'";
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
