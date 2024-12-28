<?php
  /* The main reason for prebuilt is for pretty titles and more complex table structures */
  $cardSize = 'rounded-lg';
  echo '      <div class="col">' . "\n";
  echo '        <div class="card ' . $cardSize .'">';
  echo '          <div class="card-body">';

  $valueData = json_decode($deviceData['value'], true);
  $title = $deviceData['checkName'];
  echo '       <table id="ipRoute" class="table table-striped table-bordered" style="white-space: nowrap;"><b>IP Address Routes</b><br>Last Update: ' . $deviceData['date'] . "\n";
  echo '         <thead><tr><th>Subnet</th><th>Route Index</th><th>Route Metric</th><th>Next Hop</th><th>Route Type</th><th>Route Protocol</th><th>Netmask</th></tr></thead>' . "\n"; 
  echo '         <tbody>' . "\n";
  foreach ($valueData as $iKey => $iValue) {  // iKey == subnet iValue = array of values for subnet
    echo '           <tr><td>' . $iKey . '</td><td>' . $iValue['routeIndex']. '</td><td>' .$iValue['routeMetric']. '</td><td>' .$iValue['routeNextHop']. '</td><td>' .$iValue['routeType']. '</td><td>' . $iValue['routeProto'] . '</td><td>' . $iValue['routeNetmask'] . '</td></tr>' . "\n";
  }
  echo '         </tbody>' . "\n";
  echo '       </table>' . "\n";
  echo '          </div>' . "\n";
  echo '        </div>' . "\n";
  echo '      </div>' . "\n";


?>
