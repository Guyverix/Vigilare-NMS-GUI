<html>
<script src="/js/jquery/jquery-1.7.1.min.js"></script>

<!-- Appears to use in a public project, I need to purchase the single developer license 400$?  Ouch, but a damn nice lib for a noob -->
<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
<body>
<center><h2>Graphite single metric data</h2></center>

<?php
// $graphiteSource='http://graphite01.iwillfearnoevil.com/render/?&target=nms.infra01_iwillfearnoevil_com.pingPoller.check_ping.rta&format=json&from=-7day';
$graphiteSource=$_POST['url'];
$graphiteJsonRaw = file_get_contents($graphiteSource);

$graphiteJsonConvert=json_decode($graphiteJsonRaw, true);

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
    // $dataPointApexX = "," . date("m-d-Y H:i:s", $v[1]) ."";
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
$dataPointApexYFinal = preg_replace('/\[,/','[', $dataPointApexYFinal);
$dataPointApexXFinal = preg_replace('/\[,/','[', $dataPointApexXFinal);
?>

<!-- <div id="graphiteContainer" style="height: 250px; width:100%;"></div>  -->
<div id="graphiteContainer" style="height: 250px; width:600px;"></div>

<script type="text/javascript">
    $(function () {
        var chart = new CanvasJS.Chart("graphiteContainer", {
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
</body>
</html>
