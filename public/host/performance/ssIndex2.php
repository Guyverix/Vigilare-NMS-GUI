<?php
  /*
    We can accordion OR table our output.
    set acc = 'true' in the parent, and we get
    an accordion style active.
  */

  $valueData = json_decode($deviceData['value'], true);
  $title = 'CPU_Statistics';    // Use _ for spaces, we clean it later since this is used for other things as well

  if ( $acc == 'true' ) {
    echo '<div class="container mt-2">';
      echo '<div class="accordion" id="' . $title . '_Head\">';
        echo '<div class="accordion-item">';
          echo '<h2 class="accordion-header" id="' . $title . '">';
            echo '<button class="accordion-button bg-light collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#div' . $title . '" aria-expanded="true" aria-controls="div"' . $title . '\">' . preg_replace('/_/', ' ', $title) . '</button>';
            // echo '<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#div' . $title . '" aria-expanded="true" aria-controls="div"' . $title . '\">' . preg_replace('/_/', ' ', $title) . '</button>';
          echo '</h2>';
          echo '<div id="div' . $title . '" class="accordion-collapse bg-light collapse" aria-labelledby="' . $title . '" data-bs-parent="#' . $title . '_Head">';
            echo '<div class="accordion-body">';
  }

  echo '<table class="table table-striped table-hover bg-dark table-dark" data-loading-template="loadingTemplate" style="white-space: nowrap;"><b>' . preg_replace('/_/', ' ', $title) . ' Last Update: ' . $deviceData['date']. '</b>';
  echo '<thead><tr><th>CPU Metric</th><th>Current Value</th></tr></thead><tbody>';
  $fav = 'fa-laptop';
  foreach($valueData as $k => $v) {
    $k = preg_replace('/.*.\.ss/','',$k);
    $k = preg_replace('/(?<!\ )[A-Z]/', ' $0', $k);
    if ( strpos($k, 'I O') !== false ) { $k = preg_replace('/I O/', 'IO', $k); }
    if ( strpos($k, 'I R Q') !== false ) { $k = preg_replace('/I R Q/', 'IRQ', $k); }
    echo '<tr><td><i class="fa ' . $fav . '" aria-hidden="true"></i>  &nbsp' . $k . '</td><td>' . $v . '</td></tr>';
  }
  echo '</tbody>';
  echo '</table>';

  if ($acc == 'true') {
              echo '</div>'; // end body
            echo '</div>';  // end accordion-collapse
          echo '</div>';  // end accordion-item
        echo '</div>';  // end accordion accordion-flush
      echo '</div>';  // end container
  }

?>
