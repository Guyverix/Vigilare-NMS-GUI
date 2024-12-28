<?php
  $valueData = json_decode($deviceData['value'], true);
  $title = $deviceData['checkName'];

  if ( $acc == 'true' ) {

    $AccOne="<div class='container mt-2'>\n";
    $AccTwo="<div class=\"accordion\" id=\"" . $title . "_Head\">\n";
    $AccThree="<div class='accordion-item'>\n";
    $AccFour="<h2 class='accordion-header' id=\"" . $title . "\">\n";

    $AccHead=$AccOne . $AccTwo . $AccThree . $AccFour;
    $AccButton="<button class=\"accordion-button bg-light\" type=\"button\" data-bs-toggle=\"collapse\" data-bs-target=\"#div" . $title . "\" aria-expanded=\"true\" aria-controls=\"div" . $title . "\">" . preg_replace('/_/', ' ', $title) . "</button>\n";
    $AccButton="<button class=\"accordion-button bg-light collapsed\" type=\"button\" data-bs-toggle=\"collapse\" data-bs-target=\"#div" . $title . "\" aria-expanded=\"true\" aria-controls=\"div" . $title . "\">" . preg_replace('/_/', ' ', $title) . "</button>\n";

    $AccCollapse='</h2>' . "\n" . '<div id="div' . $title . '" class="accordion-collapse bg-light collapse" aria-labelledby="' . $title . '" data-bs-parent="#' . $title . '_Head">' . "\n" . '<div class="accordion-body">' . "\n";
    $AccTitleHeaders = $AccHead . $AccButton . $AccCollapse ;
    echo $AccTitleHeaders;
  }

          $valueData = json_decode($deviceData['value'], true);
          echo '<table id="ipRoute" class="table table-striped table-hover bg-dark table-dark" data-loading-template="loadingTemplate" style="white-space: nowrap;"><b>IP Address Routes Last Update: ' . $deviceData['date']. '</b>';
          echo '<thead><tr><th>Subnet</th><th>Route Index</th><th>Route Metric</th><th>Next Hop</th><th>Route Type</th><th>Route Protocol</th><th>Netmask</th></tr></thead><tbody>';
          foreach ($valueData as $iKey => $iValue) {  // iKey == subnet iValue = array of values for subnet
            echo '<tr><td>' . $iKey . '</td><td>' . $iValue['routeIndex']. '</td><td>' .$iValue['routeMetric']. '</td><td>' .$iValue['routeNextHop']. '</td><td>' .$iValue['routeType']. '</td><td>' . $iValue['routeProto'] . '</td><td>' . $iValue['routeNetmask'] . '</td></tr>';
          }
          echo '</tbody>';
          echo '</table>';

  if ($acc == 'true') {
    echo '</div></div></div></div></div>';
  }

?>
