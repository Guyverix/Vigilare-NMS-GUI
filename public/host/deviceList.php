<?php
  /*
    this is the main Host and Devices page.  It is going to simply
    be a datatable with all hosts defined which is searchable

    Version 2 may have things like number of events, etc..  We shall
    see if that gets fugly or not.
  */

  require_once(__DIR__ . '/../../functions/generalFunctions.php');
  checkCookie($_COOKIE);  // disable check here to test 401 responses elsewhere due to expired stuff

  // Load local vars for use (urls, ports, etc)
  require_once __DIR__ . "/../../config/api.php";

  $headers = array();
  $headers[] = 'Authorization: Bearer ' . $_COOKIE['token'];

  $post = array();  // We are using post, so give it an empty array to post with

  // we SHOULD have gotten an array.... but if not convert it back to one
  $rawDevicesList = callApiPost("/device/view" , $post, $headers);
  if (! is_array($rawDevicesList)) {
    $rawDevicesList = json_decode($rawDevicesList['response'], true);
  }
  $devicesList = json_decode($rawDevicesList['response'], true);
  $responseCode = $devicesList['statusCode'];
  $devices = $devicesList['data'];
  $quitEarly = 0;

  // Sanity check your results
  switch ($responseCode) {
   case 403:
     echo "<br><br><br>";
     load4XX();
     $quitEarly = 1;
     break;
   case 200:
     break;
   default:
     echo "<br><br><br>";
     decideResponse($responseCode);
     $quitEarly = 1;
     break;
  }

  // debugger($devices);
  // debugger($rawDevicessList)
  // exit();

  if ($quitEarly == 0) {
  ?>
  <br><br><br> <!--- Drop below the menu banner --->
  <style> td { text-align: center; } </style>
  <center><h1>All Hosts and Devices</h1></center>
    <div class="container-lg">
      <table id="dt-deviceList" class="table table-striped table-hover bg-dark table-dark" data-loading-template="loadingTemplate" style="white-space: nowrap;">
        <thead>
          <tr>
            <th><center>Device Id</center></th>
            <th><center>Host Name</center></th>
            <th><center>IP Address</center></th>
            <th><center>Monitored</center></th>
            <th><center>Alive</center></th>
        </tr>
      </thead>
        <tbody>
  <?php
  foreach($devices as $device) {
    echo '<tr>';
    echo '<td>' . $device['id'] . '</td>';
//    echo '<td><a href="./index.php?&page=deviceDetails.php&id=' . $device['id']. '" target="_blank">' . $device['hostname'] . '</a></td>';  <!-- opens in new page -->
    echo '<td><a href="./index.php?&page=deviceDetails.php&id=' . $device['id']. '">' . $device['hostname'] . '</a></td>';
    echo '<td>' . $device['address'] . '</td>';
    echo '<td>' . $device['productionState'] . '</td>';
    if ( $device['isAlive'] == "alive") {
      echo '<td><img src="/images/generic/green_dot.png" style="width:20px;height:20px;" alt="alive"></img></td>';
    }
    elseif ( $device['isAlive'] == "dead") {
      echo '<td><img src="/images/generic/red_dot.png" style="width:20px;height:20px;" alt="dead"></img></td>';
    }
    else {
      echo '<td><img src="/images/generic/grey_dot.png" style="width:20px;height:20px;" alt="unknown"></img></td>';
    }
    echo '</tr>';
  }
  ?>
        </tbody>
      </table>
      </div>
      <script> window.addEventListener("DOMContentLoaded", event => {
        const datatablesSimple = document.getElementById("dt-deviceList");
        if (datatablesSimple) {
          new simpleDatatables.DataTable("#dt-deviceList", {
            searchable: true,
            sortable: true,
            storable: true,
            paging: true,
            perPage: 25,
            perPageSelect:[25,50,100,200],
            labels: {
              placeholder: "Search Devices..."
            }
            });
          }
        });
      </script>
      <!-- datatables not loaded with footer, add it now -->
      <script src="/js/simple-datatables/simple-datatables.js"></script>

  <?php

  }
  else {
    // Something went very wrong with the API call, but keep the layout clean...
    loadUnknown("API calls failed in an unexpected way.  Please reload");
  }
?>
