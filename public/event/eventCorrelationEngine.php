<?php
  // Get our utilities in place asap.

  include_once __DIR__ . "/functions/eventFunctions.php";
  include_once __DIR__ . "/../../functions/generalFunctions.php";

  // Load local vars for use (urls, ports, etc)
  require_once __DIR__ . "/../../config/api.php";
  echo "<br><br><br>";
  // Grab our POSSIBLE values so users can choose what they change
  $headers = array();
  $headers[] = 'Authorization: Bearer ' . $_COOKIE['token'];
  $cookieTimezone = $_COOKIE['clientTimezone'];
  $post = array();  // We are using post, so give it an empty array to post with
  $quitEarly = 0;


  // Always pull fresh state from ece API
  $rawEceList = callApiPost("/ece/find", $post, $headers);
  $eceList = json_decode($rawEceList['response'], true);
  if ( $eceList['statusCode'] !== 200) {
    $quitEarly = 1;
  }

  if ( $quitEarly == 0 ) {
//    echo "<div class='container-fluid'>\n";
    echo "<div class='container'>\n";
    // Show a list of our ECE rules and the state of them as discrete tables
//    echo "<div class='row justify-content-center'>";
    echo "<div class='row row-cols-auto'>\n";
    foreach($eceList['data'] as $eceRule) {
      echo "<div class='col'>\n";
//      echo "<div class='table-responsive col-xl-2'>";
      echo "<center><h5>ECE Rule: " . $eceRule['id'] . "</h5></center>\n";
      echo "<br>\n";
      echo "<table id='dt-ece-list' class='table table-bordered table-striped table-sm table-info table-hover bg-dark table-dark' data-loading-template='loadingTemplate'>\n";
      echo "<tr><td>Rule:</td><td>" . $eceRule['id'] . "</td></tr>\n";
      echo "<tr><td>Active:</td><td>" . $eceRule['active'] . "</td></tr>\n";
      echo "<tr><td>Application Name:</td><td>" . $eceRule['appName'] . "</td></tr>\n";
      echo "<tr><td>App Rule Order:</td><td>" . $eceRule['appRuleOrder'] . "</td></tr>\n";
      echo "<tr><td>Service Name:</td><td>" . $eceRule['serviceName'] . "</td></tr>\n";
      echo "<tr><td>Raw SQL:</td><td>" . $eceRule['raw'] . "</td></tr>\n";
      echo "<tr><td>Application Correlation:</td><td>\n";
      // This should be its own internal table due to showing the rules in a sane way
      if ( $eceRule['raw'] == 'false') {
        $eceConvert = json_decode($eceRule['appCorrelation'],true);
        echo "<table id='sub-table-ece-list' class='table table-striped table-success bg-dark table-dark' data-loading-template='loadingTemplate'>\n";
        foreach($eceConvert as $k => $v) {
          echo "<tr><td>" . $k . ":</td><td>" . $v . "</td><tr>\n";
        }
        echo "</table>\n";
      }
      else {
        // We are dealing with raw SQL queries here, just show them
        echo $eceRule['appCorrelation'];
      }
      echo "</td></tr>\n";
      echo "<tr><td>Corrleation Summary:</td><td><pre>" . $eceRule['eceSummary'] . "</pre></td></tr>\n";
      echo "<form id='eceActive" .$eceRule['id'] . "' role='form' action='' method='POST'>\n";
      if ( $eceRule['active'] == 'false') {
        echo "<input type='hidden' name='active' value='true'>\n";
        echo "<input type='hidden' name='id' value='" . $eceRule['id'] . "'>\n";
        echo "<tr><td colspan=2><button form='eceActive" . $eceRule['id'] . "' type='submit' class='btn btn-default btn-warning btn-lg'><span class='glyphicon glyphicon-off'></span>Activate Rule " . $eceRule['id'] . "</button></td></tr>\n";
        echo "</form>\n";
      }
      else {
        echo "<input type='hidden' name='active' value='false'>\n";
        echo "<input type='hidden' name='id' value='" . $eceRule['id'] . "'>\n";
        echo "<tr>\n<td colspan=2>\n<button form='eceActive" . $eceRule['id'] . "' type='submit' class='btn btn-default btn-danger btn-lg'><span class='glyphicon glyphicon-off'></span>Deactivate Rule " . $eceRule['id'] . "</button></td>\n</tr>\n";
        echo "</form>\n";
      }
      echo "</table>\n";
      echo "</div>\n";  // end column
    }
  echo "</div>\n";  // end row
  echo "</div>\n";  // end container
  //debugger($eceConvert);
  }
  else {
    // Show our generic error page
    loadUnknown("API calls failed in an unexpected way.  Please reload");
  }
?>
