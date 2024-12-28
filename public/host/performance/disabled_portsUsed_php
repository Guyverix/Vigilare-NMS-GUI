<?php

  $valueData = json_decode($deviceData['value'], true);
  $title = $deviceData['checkName'];

  if ( $acc == 'true' ) {

    $AccOne="<div class='container mt-2'>\n";
    $AccTwo="<div class=\"accordion\" id=\"" . $title . "_Head\">\n";
    $AccThree="<div class='accordion-item'>\n";
    $AccFour="<h2 class='accordion-header' id=\"" . $title . "\">\n";

    $AccHead=$AccOne . $AccTwo . $AccThree . $AccFour;
//    $AccButton="<button class=\"accordion-button\" type=\"button\" data-bs-toggle=\"collapse\" data-bs-target=\"#div" . $title . "\" aria-expanded=\"true\" aria-controls=\"div" . $title . "\">" . preg_replace('/_/', ' ', $title) . "</button>\n";
    $AccButton="<button class=\"accordion-button bg-light collapsed\" type=\"button\" data-bs-toggle=\"collapse\" data-bs-target=\"#div" . $title . "\" aria-expanded=\"true\" aria-controls=\"div" . $title . "\">" . preg_replace('/_/', ' ', $title) . "</button>\n";

    $AccCollapse='</h2>' . "\n" . '<div id="div' . $title . '" class="accordion-collapse bg-light collapse" aria-labelledby="' . $title . '" data-bs-parent="#' . $title . '_Head">' . "\n" . '<div class="accordion-body">' . "\n";
    $AccTitleHeaders = $AccHead . $AccButton . $AccCollapse ;
    echo $AccTitleHeaders;
  }
  echo '<table class="table table-striped table-hover bg-dark table-dark" data-loading-template="loadingTemplate" style="white-space: nowrap;"><b>IP Address Ports Used Last Update: ' . $deviceData['date']. '</b>';
  echo '<thead><tr><th>Bound IP Address</th><th>Bound Port</th></tr></thead><tbody>';
  foreach($valueData as $singleValueData) {
    echo '<tr><td>' . $singleValueData['address'] . '</td><td>' . $singleValueData['port'] . '</td></tr>';
  }
  echo '</tbody>';
  echo '</table>';
  if ($acc == 'true') {
    echo '</div></div></div></div></div>';
  }

?>
