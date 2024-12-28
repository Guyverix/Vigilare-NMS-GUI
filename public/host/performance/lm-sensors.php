<?php
  $valueData = json_decode($deviceData['value'], true);
  $title = $deviceData['checkName'];

          echo '<table class="table table-striped table-bordered" style="white-space: nowrap;"><b>Physical Sensors</b><br>Last Update: ' . $deviceData['date']. "\n";
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

?>
