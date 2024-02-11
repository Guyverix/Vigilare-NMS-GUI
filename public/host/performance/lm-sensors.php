<?php
  $valueData = json_decode($deviceData['value'], true);
  $title = $deviceData['checkName'];

  if ( $acc == 'true' ) {

    $AccOne="<div class='container mt-2'>\n";
    $AccTwo="<div class=\"accordion\" id=\"" . $title . "_Head\">\n";
    $AccThree="<div class='accordion-item '>\n";
    $AccFour="<h2 class='accordion-header ' id=\"" . $title . "\">\n";

    $AccHead=$AccOne . $AccTwo . $AccThree . $AccFour;
//    $AccButton="<button class=\"accordion-button\" type=\"button\" data-bs-toggle=\"collapse\" data-bs-target=\"#div" . $title . "\" aria-expanded=\"true\" aria-controls=\"div" . $title . "\">" . preg_replace('/_/', ' ', $title) . "</button>\n";
    $AccButton="<button class=\"accordion-button bg-light collapsed\" type=\"button\" data-bs-toggle=\"collapse\" data-bs-target=\"#div" . $title . "\" aria-expanded=\"true\" aria-controls=\"div" . $title . "\">" . preg_replace('/_/', ' ', $title) . "</button>\n";

    $AccCollapse='</h2>' . "\n" . '<div id="div' . $title . '" class="accordion-collapse bg-light collapse" aria-labelledby="' . $title . '" data-bs-parent="#' . $title . '_Head">' . "\n" . '<div class="accordion-body">' . "\n";
    $AccTitleHeaders = $AccHead . $AccButton . $AccCollapse ;
    echo $AccTitleHeaders;
  }

          echo '<table class="table table-striped table-hover bg-dark table-dark" data-loading-template="loadingTemplate" style="white-space: nowrap;"><b>Physical Sensors Last Update: ' . $deviceData['date']. '</b>';
          echo '<thead><tr><th>Sensor Type</th><th>Current Value</th></tr></thead><tbody>';
          foreach($valueData as $k => $v) {
            // CLEAN THE DISPLAY
            $k = preg_replace('/\.snmp\.lmsensors\./','',$k);  // Make me pretty
            $k = preg_replace('/\._/',' ', $k);                // keep going
            if ( strpos( $k, 'temp') !== false ) {
              $v = $v / 1000;
              $v = $v . " C";
              $fav = 'fa-thermometer-empty';
            }
            elseif ( strpos( $k, 'volt') !== false ) {
              $v =  $v / 1000 ;
              $v = $v . " V";
              $fav = 'fa-bolt';
            }
            elseif ( strpos( $k, 'fan') !== false ) {
              $v = $v . " RPM";
              $fav = 'fa-fire-extinguisher';

            }
            echo '<tr><td><i class="fa ' . $fav . '" aria-hidden="true"></i>  &nbsp' . $k . '</td><td>' . $v . '</td></tr>';
          }
          echo '</tbody>';
          echo '</table>';

  if ($acc == 'true') {
    echo '</div></div></div></div></div>';
  }

?>
