<?php
/*
  Search and alter ECE values from a table
*/


// Only needed for debugging and bypassing security, etc
require_once(__DIR__ . '/../../functions/generalFunctions.php');
checkCookie($_COOKIE);  // disable check here to test 401 responses elsewhere due to expired stuff

// Load local vars for use (urls, ports, etc)
require_once __DIR__ . "/../../config/api.php";

// Grab our POSSIBLE values so users can choose what they change
$headers = array();
$headers[] = 'Authorization: Bearer ' . $_COOKIE['token'];
$post = array();  // We are using post, so give it an empty array to post with
$quitEarly = 0;
echo "<br>\n<br>\n<br>\n";

//debugger($_POST);
if (isset($_POST['categoryId'])) {
  if ($_POST['parentId'] == "Root Parent") { $_POST['parentId'] = null; }
  // Time to update the API
  $post = $_POST;
  $rawMakeParent = callApiPost("/ece/update/element", $post, $headers);
  $makeParent = json_decode($rawMakeParent['response'], true);
  $responseCode = $makeParent['statusCode'];

  // Disable refresh rePOSTing
  echo '<script type="text/javascript">' . "\n";
  echo 'if ( window.history.replaceState ) {' . "\n";
  echo '  window.history.replaceState( null, null, window.location.href );' . "\n";
  echo '}' . "\n";
  echo '</script>' . "\n";

  // debugger($makeParent);
  if ($responseCode !== 200 && $responseCode !== 403) {    // Anything other than a 200 OK is an issue
    $responseString = '';
    if ( isset($makeParent['error'])) {
      foreach ($makeParent['error'] as $errors) {
        $responseString .= $errors . "<br>";
      }
    }
    else {
      foreach ($makeParent['data'] as $errors) {
        $responseString .= $errors . "<br>";
      }
    }
    decideResponse($responseCode, $responseString );
  }
  elseif ( $responseCode == 403) {
    load403Warn("Expired access credentials");
  }
  else {
    // After a successful update, wait and then reload the page
    successMessage('Ece configuration change was successful.');
  }
}

// Always grab fresh when a POST can change our values
$rawEceList = callApiPost("/ece/list", $post, $headers);
$eceList = json_decode($rawEceList['response'], true);

  if ($quitEarly == 0) {
    echo "<center><h1>Alter ECE values</h1></center>\n";
    echo "<div class='container-lg'>\n";
    echo "<table id='dt-ece-list' class='table table-striped table-hover bg-dark table-dark' data-loading-template='loadingTemplate' style='white-space: nowrap;'>\n";
    echo "<thead>";
    echo "<tr><th>Self ID (read-only)</th><th>Parent ID</th><th>Category Name</th><th>Associated Hosts</th><th>Associated service Checks</th><th>Change Monitor</th><tr>\n";
    echo "</thead>";
    //debugger($eceList['data']);

    foreach ($eceList['data'] as $eceElement) {
      echo "<tr>\n";
//      echo "<td colspan=6><form id='changeEce" . $eceElement['categoryId'] . "' role='form' action='/ece/index.php?&page=searchEce.php' method='POST'><table><tr><td><input form='" . $eceElement['categoryId'] . "' class='form-control' type='text' name='categoryId' value='" . $eceElement['categoryId'] . "' readonly></td>\n";
      echo "<td colspan=6><form id='changeEce" . $eceElement['categoryId'] . "' role='form' action='/ece/index.php?&page=searchEce.php' method='POST'><table><tr><td><input class='form-control' type='text' name='categoryId' value='" . $eceElement['categoryId'] . "' readonly></td>\n";
      if ( empty($eceElement['parentId'])) {
        echo "<td><input class='form-control' type='text' name='parentId' value='Root Parent' readonly></td>\n";
      }
      else {
        echo "<td><input class='form-control' type='text' name='parentId' value='" . $eceElement['parentId'] . "'></td>\n";

      }
      echo "<td><input class='form-control' type='text' name='categoryName' value='" . $eceElement['categoryName'] . "'></td>\n";
      echo "<td><input class='form-control' type='text' name='associatedHost' value='" . $eceElement['associatedHost'] . "'></td>\n";
      echo "<td><input class='form-control' type='text' name='associatedCheck' value='" . $eceElement['associatedCheck'] . "'></td>\n";
      echo "<td><button form='changeEce" . $eceElement['categoryId'] . "' type='submit' class='btn btn-default btn-info btn-sm'><span class='glyphicon glyphicon-off' value='foo'></span>Change Monitor</button></td></form></table></tr>\n";
    }
    echo "</table>\n";
  }
