<?php
  /*
    We are going to search through a list for a specfic monitor which
    is going to allow us to change, delete, addHostId, addHostGroup
  */

//  require_once(__DIR__ . '/../../functions/generalFunctions.php');
  // checkCookie($_COOKIE);  // disable check here to test 401 responses elsewhere due to expired stuff

  // Load local vars for use (urls, ports, etc)
//  require_once __DIR__ . "/../../config/api.php";


  $headers = array();
  $headers[] = 'Authorization: Bearer ' . $_COOKIE['token'];

  $post = array();  // We are using post, so give it an empty array to post with
  // we SHOULD have gotten an array.... but if not convert it back to one
  $rawMonitorsList = callApiPost("/monitors/findMonitorsAll" , $post, $headers);
  if (! is_array($rawMonitorsList)) {
    $rawMonitorsList = json_decode($rawMonitorsList, true);
  }
  $monitorsList = json_decode($rawMonitorsList['response'], true);
  $monitors = $monitorsList['data']['result'];
  $quitEarly = 0;

/*
echo "<pre>";
echo print_r($monitors, true);
echo "</pre>";
exit();
*/
  // Sanity check your results
  switch ($monitorsList['statusCode']) {
   case 403:
     echo "<br><br><br>";
     load4XX();
     $quitEarly = 1;
   case 200:
     break;
   default:
     echo "<br><br><br>";
     decideResponse($monitorsList['statusCode']);
     $quitEarly = 1;
  }

  /*
    We should now have an indexed array of our existing monitors.
    We will build a table for them, and then they can be searched
    for "things" checkName, checkAction, type should all be
    searchable so we can filter down.

    this is going to use datatablesSimple as it is less of a PITA
    than the full blown datatabes in JS
  */

  if ($quitEarly == 0) {
    echo '
      <style>
        td {
         text-align: center;
        }
      </style>';



    echo '
      <br><br><br> <!--- Drop below the menu banner --->
      <center><h1>Search all monitors</h1></center>
      <div class="container-fluid">
      <table id="datatablesSimple" class="table table-striped table-hover bg-dark table-dark" data-loading-template="loadingTemplate" style="white-space: nowrap;">
      <thead>
        <tr>
          <th><center>Check Name</center></th>
          <th><center>Check Action</center></th>
          <th><center>Check Type</center></th>
          <th><center>Iteration</center></th>
          <th><center>Storage</center></th>
          <th><center>Options</center></th>
        </tr>
      </thead>
      <tfoot>
        <tbody>
          <div id="dataTable01">
    ';
    $tableBody = array();
    foreach ($monitors as $monitor) {
      echo '<tr>';
      echo '<td>' . $monitor['checkName'] .'</td>';
      echo '<td>' . $monitor['checkAction'] . '</td>';
      echo '<td>' . $monitor['type'] . '</td>';
      echo '<td>' . $monitor['iteration'] . '</td>';
      echo '<td>' . $monitor['storage'] . '</td>';
      echo '<td>';
      echo '<form id="changeMonitor" method="POST" action="/monitor/index.php?&page=changeMonitor.php">';
      echo '<input type="hidden" name="id" value="' . $monitor['id'] . '">';
      echo '<input type="hidden" name="checkName" value="' . $monitor['checkName'] . '">';
      echo '<input type="hidden" name="checkAction" value="' . $monitor['checkAction'] . '">';
      echo '<input type="hidden" name="type" value="' . $monitor['type'] . '">';
      echo '<input type="hidden" name="iteration" value="' . $monitor['iteration'] . '">';
      echo '<input type="hidden" name="storage" value="' . $monitor['storage'] . '">';
      echo '<input type="hidden" name="hostid" value="' . $monitor['hostid'] . '">';
      echo '<input type="hidden" name="hostGroup" value="' . $monitor['hostGroup'] . '">';
      echo '</form>';
      echo '<button form="changeMonitor" type="submit" class="btn btn-default btn-info btn-sm"><span class="glyphicon glyphicon-off"></span>Change Monitor</button> ';
      echo '<button form="deleteMonitor" type="submit" class="btn btn-default btn-danger btn-sm"><span class="glyphicon glyphicon-off"></span>Delete Monitor</button>';
      echo '</td>';
      echo '</tr>';
    }

    echo '
        </tbody>
      </table>
      </div>
      <!-- datatables not loaded with footer, add it now -->
      <script src="/js/simple-datatables/script10.js"></script>  <!-- specific to 10 entries per page, default (script.js) 25, specific script50 also exists -->
      <script src="/js/simple-datatables/simple-datatables.js"></script>
    ';

  }
  else {
    // Something went very wrong with the API call, but keep the layout clean...
    loadUnknown("API calls failed in an unexpected way.  Please reload");
  }
?>
