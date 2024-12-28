<?php
  /*
   Some of these require a little tweaking to look correct.
   Override values here size and radius here

  */

  $cardSize = 'w-27 rounded-lg';
  $cardRadius = '1%';

  echo '      <div class="col">' . "\n";
//  echo '        <div class="card ' . $cardSize .'" style="border-radius: ' . $cardRadius . '">';
  echo '        <div class="card ' . $cardSize .'">';
  echo '          <div class="card-body">';
  /* The data for ports is stored as JSON in the array.  Convert back to its own array heere */
  $valueData = json_decode($deviceData['value'], true);

  echo '<table class="table table-striped table-bordered" style="white-space: nowrap;"><b>Ports Used</b><br>Last Update: ' . $deviceData['date']. "\n";
  echo '<thead><tr><th>Bound IP Address</th><th>Bound Port</th></tr></thead>' . "\n";
  echo '<tbody>' . "\n";
  foreach($valueData as $singleValueData) {
    echo '<tr><td>' . $singleValueData['address'] . '</td><td>' . $singleValueData['port'] . '</td></tr>' . "\n";
  }
  echo '</tbody>' . "\n";
  echo '</table>' . "\n";
  echo '          </div>' . "\n";
  echo '        </div>' . "\n";
  echo '      </div>' . "\n";
?>
