<?php
  /*
    Override defaults at this point if needed to make display look correct
    when using oddball sized tables
  */

  echo '      <div class="col">' . "\n";
//  echo '        <div class="card ' . $cardSize .'" style="border-radius: ' . $cardRadius . '">';
  echo '        <div class="card rounded">';
  echo '          <div class="card-body rounded">';


  $valueData = json_decode($deviceData['value'], true);
  $title = 'CPU_Statistics';    // Use _ for spaces, we clean it later since this is used for other things as well


  echo '<table class="table table-striped table-hover" style="white-space: nowrap;"><b>' . preg_replace('/_/', ' ', $title) . '</b><br>Last Update: ' . $deviceData['date']. "\n";
  echo '<thead><tr><th>CPU Metric</th><th>Current Value</th></tr></thead>' . "\n";
  echo '<tbody>' . "\n";
  $fav = 'fa-laptop';
  foreach($valueData as $k => $v) {
    $k = preg_replace('/.*.\.ss/','',$k);
    $k = preg_replace('/(?<!\ )[A-Z]/', ' $0', $k);
    if ( strpos($k, 'I O') !== false ) { $k = preg_replace('/I O/', 'IO', $k); }
    if ( strpos($k, 'I R Q') !== false ) { $k = preg_replace('/I R Q/', 'IRQ', $k); }
    echo '<tr><td><i class="fa ' . $fav . '" aria-hidden="true"></i>  &nbsp' . $k . '</td><td>' . $v . '</td></tr>' . "\n";
  }
  echo '</tbody>' . "\n";
  echo '</table>' . "\n";
  echo '          </div>' . "\n";
  echo '        </div>' . "\n";
  echo '      </div>' . "\n";

?>
