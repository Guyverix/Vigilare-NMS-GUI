
<?php
  /*
    This is going to render via CanvasJS that
    match a given checkName name.
  */
  echo '<script src="/js/jquery/jquery-1.7.1.min.js"></script>';
  // This is just for testing.  Get the damn license when you can afford it

  echo '<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>';

  echo '<br><br><br>'; // only needed if we have a horozontal bar

  // Only needed for debugging and bypassing security, etc
  require_once __DIR__ . '/../../../functions/generalFunctions.php';
  // checkCookie($_COOKIE);  // disable check here to test 401 responses elsewhere due to expired stuff

  // Load local vars for use (urls, ports, etc)
  require_once __DIR__ . "/../../../config/api.php";

  // Hosts and Devices have A LOT of variables in play.  We need functions specific to this group
  require_once __DIR__ . "/../functions/hostFunctions.php";

  // Grab our POSSIBLE values so users can choose what they change
  $headers = array();
  $headers[] = 'Authorization: Bearer ' . $_COOKIE['token'];
  $post = array();  // We are using post, so give it an empty array to post with
  $quitEarly = 0;

  $task = 'findGraphs'; // This is the only api call it will be able to make

  if ( isset($_POST['hostname'])) {
    $hostname = $_POST['hostname'];
  }
  if ( isset($_POST['id'])) {
    $id = $_POST['id'];
  }

          echo '<input type="hidden" name="task" value="findGraphs">';
          echo '<input type="hidden" name="hostname" value="' . $hostname . '">';
          echo '<input type="hidden" name="checkType" value="' . $graphiteKey . '">';
          echo '<input type="hidden" name="checkName" value="' . $graphiteValue . '">';


  if ( isset($_POST['checkType'])) {
    $checkType = $_POST['checkType'];
  }
  if ( isset($_POST['checkName'])) {
    $checkName = $_POST['checkName'];
  }
  if (isset($_POST['startRange']) && isset($_POST['startNumber'])) {
    $startNumber = $_POST['startNumber'];
    $startRange = $_POST['startRange'];
    $startTime = '-' . $_POST['startNumber'] . $_POST['startRange'];
  }
  else {
    $startNumber = '1';
    $startRange  = 'd';
    $startTime = '-1d';
  }

  if (isset($_POST['endRange']) && isset($_POST['endNumber']) && $_POST['endNumber'] !== 'now') {
    $endNumber = $_POST['endNumber'];
    $endRange = $_POST['endRange'];
    $endTime = '-' . $_POST['endTime'] . $_POST['endRange'];
  }
  else {
    $endRange = 'h';
    $endNumber = 'now';
    $endTime = '-1m';
  }

  //debugger($post);
  //debugger($_POST);
  $post = ['task' => $task ];
  $post += ['hostname' => $hostname ];
  $post += ['checkName' => $checkName ];
  $post += ['checkType' => $checkType ];
  $post += ['from' => $startTime ];
  $post += ['to' => $endTime ];

  $rawRenderGraphs = callApiPost("/graphite/test", $post, $headers);
  $renderGraphsResult = json_decode($rawRenderGraphs['response'],true);
  //debugger($rawRenderGraphs);
  //debugger($renderGraphsResult);
  //exit();

  echo '<div class="container">';
  echo '<div class=" text-center mt-5 ">';
  echo '<h1>Available ChartJS Graphs for <a href="/host/index.php?&page=deviceDetails.php&id=' . $id . '">' . $hostname . '</a></h1><br>';
  echo '</div>';
  echo '<form id="changeTimes" method="POST" action="">';
  echo '<table class="table bg-dark table-dark">';
  echo '<thead><th>From Range</th><th>Until Range</th></thead>';
  echo '<tbody><tr><td>';
  echo '<input type="text" name="startNumber" value="' . $startNumber . '" size="3"> ';
  echo '<select name="startRange" id="startRange">';
    if ( $startRange == 'd' ) { echo '<option value="d" selected>days</option>'; } else { echo '<option value="d" selected>days</option>'; }
    if ( $startRange == 'h' ) { echo '<option value="h" selected>hours</option>'; } else { echo '<option value="h">hours</option>'; }
    if ( $startRange == 'w' ) { echo '<option value="w" selected>weeks</option>'; } else { echo '<option value="w">weeks</option>'; }
    if ( $startRange == 'm' ) { echo '<option value="m" selected>months</option>'; } else { echo '<option value="m">months</option>'; } 
  echo '</select>';
  echo '</td><td>';
  echo '<input type="text" name="endNumber" value="' . $endNumber . '" size="3">';
  echo '<select name="endRange" id="endRange">';
    if ( $endRange == 'd' ) { echo '<option value="d" selected>days</option>'; } else { echo '<option value="d">days</option>'; }
    if ( $endRange == 'h' ) { echo '<option value="h" selected>hours</option>'; } else { echo '<option value="h">hours</option>'; }
    if ( $endRange == 'w' ) { echo '<option value="w" selected>weeks</option>'; } else { echo '<option value="w">weeks</option>'; }
    if ( $endRange == 'm' ) { echo '<option value="m" selected>months</option>'; } else { echo '<option value="m">months</option>'; }
  echo '</select>';
  echo '<input type="hidden" name="checkName" value="' . $checkName . '">';
  echo '<input type="hidden" name="hostname" value="' . $hostname . '">';
  echo '<input type="hidden" name="checkType" value="' . $checkType . '">';
  echo '<input type="hidden" name="id" value="' . $id . '">';
  echo '</td></tr></tbody>';
  echo '</table>';
  echo '<center><button type="submit" form="changeTimes" class="btn btn-primary">Change Timeframe</button><p></p> <input type="button" type="submit" class="btn btn-primary" value="back one page" onclick="history.back()"/> </center>';
  echo '</form>';
  echo '<div class="container">';
  echo '<table class="table table-striped table-hover bg-dark table-dark" data-loading-template="loadingTemplate" style="white-space: nowrap;"><center><b>ChartJS Graphs for service check ' . $checkName . '</b></center>';
  // debugger($renderGraphsResult);

  if ($renderGraphsResult['statusCode'] !== 200) {
    echo '<tr><td>' . loadUnknown($renderGraphsResult['data']) . '</td></tr>';
  }
  elseif ( ! is_array($renderGraphsResult['data'][0]) && strpos($renderGraphsResult['data'][0], 'failed to generate graph') !== false) {
    echo '<tr><td>' . loadUnknown($renderGraphsResult['data'][0]) . '</td></tr>';
  }
  else {
    // This is where we are going to render CanvasJS
    $graphNumber=0;
    foreach ( $renderGraphsResult['data'][0] as $checkNameList) {
      // echo "checkNameList " . $checkNameList . "<br>";
      if ( ! is_array($checkNameList)) {
        // No array, no graphs...
      }
      else {
        foreach ($checkNameList as $k => $v) {
      $graphNumber++;
          // echo "<br>" . $k . "<br>";
          // echo "<br>" . $v . "<br>";
          if ( ! is_array($v)) {
            // callUrlGet for adhoc URL's
            $dataReturn = callUrlGet($v . '&format=json');
            $graphiteJsonConvert=json_decode($dataReturn['response'], true);

            // Set defaults for values here:
            $dataPointsG=array();
            $dataPointApexYFinal='[';
            $dataPointApexXFinal='[';

            // convert your array into something that javascript can consume (json)
            foreach ($graphiteJsonConvert as $graphiteJsonFiltered) {
              $graphiteTarget = $graphiteJsonFiltered['target'];
              foreach ($graphiteJsonFiltered['datapoints'] as $k => $v) {
                // line needs x/y values and JAVA precision on dates!
                $tim=$v[1].'000';
                $dataPointG = array("x" => $tim, "y" =>$v[0]);
                $dataPointApexY = ",$v[0]";
                //    $dataPointApexX = "," . date("m-d-Y H:i:s", $v[1]) ."";
                $dataPointApexX = "," . $v[1] ."000";
                array_push($dataPointsG, $dataPointG);

                // Spline needs different format
                $dataPointApexYFinal .= $dataPointApexY;
                $dataPointApexXFinal .= $dataPointApexX;
              }
            }

            // Close out the strings to make proper json
            $dataPointApexYFinal .= ']';
            $dataPointApexXFinal .= ']';
            // Clean out empty points
            $dataPointApexYFinal = preg_replace('/\[,/','[', $dataPointApexYFinal);
            $dataPointApexXFinal = preg_replace('/\[,/','[', $dataPointApexXFinal);

            ?>
            <tr><td align="center">
            <div id="graphiteContainer_<?php echo $graphNumber; ?>" style="height: 250px; width:1000px;"></div>
            <script type="text/javascript">
            $(function () {
              var chart = new CanvasJS.Chart("graphiteContainer_<?php echo $graphNumber; ?>", {
                theme: "theme2",
                zoomEnabled: true,
                animationEnabled: true,
                title: {
                  text: "<?php echo $graphiteTarget; ?>"
                },
                axisY: {
                  reversed: false
                },
                data: [
                {
                  type: "splineArea",
                  markerSize:5 ,
                  toolTipContent: "rta {y}ms",
                  xValueType: "dateTime",
                  color: "rgba(54,158,173,.7)",
                  dataPoints: <?php echo json_encode($dataPointsG, JSON_NUMERIC_CHECK); ?>
                }
                ]
              });
              chart.render();
            });
            </script>
            </td></tr>
            <?php
            echo "<br>";
          }
          else {
            // This is used when we have returns with multiple datasources per graph.  This is the image directly
            echo '<tr><td><center><img class="NO-CACHE" src="' .  $v[0] . '" width="900" height="200"></img></center></td></tr>';
          }
        }
      }
    }
  }
  echo '</tbody>';
  echo '</table>';

?>