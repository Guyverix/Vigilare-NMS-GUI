<?php
  echo "<br><br><br>";  // drop us below the ribbon

  /*
    Landing page for host or device monitors that are currently active
    We will support removing a host from a given monitor, or adding
    from the complete list of monitors.

    This is not going to support our deviceGroups, and will only work
    against the single host.  Not a good idea to remove from a hostgroup
    when it would remove a large swath of service checks.

    Likely deviceGroup is going to become more of a container for things
    such as maintenance, or other adhoc groups as well as for a standard
    set of monitors.
  */

  // Only needed for debugging and bypassing security, etc
  require_once(__DIR__ . '/../../functions/generalFunctions.php');
  // checkCookie($_COOKIE);  // disable check here to test 401 responses elsewhere due to expired stuff

  // Load local vars for use (urls, ports, etc)
  require_once __DIR__ . "/../../config/api.php";

  // Hosts and Devices have A LOT of variables in play.  We need functions specific to this group
  require_once __DIR__ . "/functions/hostFunctions.php";

  // Grab our POSSIBLE values so users can choose what they change
  $headers = array();
  $headers[] = 'Authorization: Bearer ' . $_COOKIE['token'];
  $post = array();  // We are using post, so give it an empty array to post with
  $quitEarly = 0;

//debugger($_POST);




  // Retrieve our data that was sent to us
  if (isset($_POST['id'])) {
    $id = $_POST['id'];
  }
  else {
    echo '<br><br><br>';
    loadUnknown("Page was called without correct parameters.  Please go back and try again.");
    $quitEarly = 1;
  }

  if (isset($_POST['hostname'])) {
    $hostname = $_POST['hostname'];
  }

  if ( isset($_POST['addMonitor'])) {
    // This is from an internal post.  Call API
    $post = [ 'id' => $_POST['monitorId']];
    $post += ['hostId' => $_POST['hostId']];
    $addMonitor = callApiPost("/monitors/monitorAddHost", $post, $headers);
    $addMonitor = json_decode($addMonitor['response'],true);
   
    switch ($addMonitor['statusCode']) {
     case 403:
       load4XX();
       $quitEarly = 1;
       break;
     case 200:
       successMessage('Device has been added to requested monitor.');
       break;
     default:
       decideResponse($addMonitor['statusCode']);
       $quitEarly = 1;
       break;
    }
    $post = array();  // empty our array since we need it again
    $post = [ 'id' => $_POST['hostId']];
    // Call API for monitors so we have a fresh list after changes
    $activeMonitors1 = callApiPost("/monitors/findMonitorsByHostId", $post, $headers);
    $activeMonitors = json_decode($activeMonitors1['response'], true);
    $activeMonitors = $activeMonitors['data']['result'];
    $post = array();
  }
  elseif ( isset($_POST['removeMonitor'])) {
    // This is from an internal post.  Call API
    $post = [ 'id' => $_POST['monitorId']];
    $post += ['hostId' => $_POST['hostId']];
    $removeMonitor = callApiPost("/monitors/monitorDeleteHost", $post, $headers);
    $removeMonitor = json_decode($removeMonitor['response'],true);


    switch ($removeMonitor['statusCode']) {
     case 403:
       load4XX();
       $quitEarly = 1;
       break;
     case 200:
       successMessage($removeMonitor['data']['result']);
       break;
     default:
       decideResponse($removeMonitor['statusCode']);
       $quitEarly = 1;
       break;
    }
    $post = array();
    $post = [ 'id' => $_POST['hostId']];
    // Call API for monitors so we have a fresh list after changes
    $activeMonitors1 = callApiPost("/monitors/findMonitorsByHostId", $post, $headers);
    $activeMonitors = json_decode($activeMonitors1['response'], true);
    $activeMonitors = $activeMonitors['data']['result'];
    $post = array();
  }
  else {  // default is to get active list every refresh
    $post = [ 'id' => $_POST['id']];
    $activeMonitors = callApiPost("/monitors/findMonitorsByHostId", $post, $headers);
    $activeMonitors = json_decode($activeMonitors['response'], true);
    $activeMonitors = $activeMonitors['data']['result'];
    $post = array();
  }

  $rawFullMonitorList = callApiPost("/monitors/findMonitors", $post, $headers);
  $rawFullMonitorList = json_decode($rawFullMonitorList['response'], true);

  // Sanity check your results
  switch ($rawFullMonitorList['statusCode']) {
   case 403:
     load4XX();
     $quitEarly = 1;
     break;
   case 200:
     $fullMonitorList = $rawFullMonitorList['data']['result'];
     break;
   default:
     decideResponse($rawFullMonitorList['statusCode']);
     $quitEarly = 1;
     break;
  }

/*

debugger($_POST);
//exit();
*/
//debugger($activeMonitors);
//debugger($rawFullMonitorList);

  if ( $quitEarly == 0 ) {
  ?>
  <div class=" text-center mt-5 ">
    <h1>Change Monitors for <?php echo '<a href="/host/index.php?&page=deviceDetails.php&id=' . $id . '">' . $hostname . '</a>'; ?></h1><br>
  </div>
  <div class="container-md">
    <div class="row justify-content-center">
     <div class="col">
      <table id="dt-activeMonitor" class="table table-striped table-hover bg-dark table-dark" data-loading-template="loadingTemplate" style="white-space: nowrap;">
        <center><h3><b>Active Monitors</b></h3></center>
        <thead>
          <tr>
            <th>Check Name</th>
            <th>Check Type</th>
            <th>Check Storage</th>
            <th>Options</th>
          </tr>
        </thead>
        <tbody>
        <?php
          foreach($activeMonitors as $key => $liveMonitor) {
            echo '<tr><td>' . $liveMonitor['checkName'] . '</td>';
            echo '<td>' . $liveMonitor['type'] . '</td>';
            echo '<td>' . $liveMonitor['storage'] . '</td>';
            echo '<td><form id="removeMonitor' . $liveMonitor['checkName'] . '" method="POST">';
            echo '<input type="hidden" name="monitorId" value="' . $liveMonitor['id'] . '">';
            echo '<input type="hidden" name="hostId" value="' . $id . '">';
            echo '<input type="hidden" name="id" value="' . $id . '">';
            echo '<input type="hidden" name="hostname" value="' . $hostname . '">';
            echo '<button form="removeMonitor' . $liveMonitor['checkName'] . '" name="removeMonitor" type="submit" class="btn btn-danger btn-info btn-sm">Remove Monitor</button></form></td></tr>';
          }
        ?>
        </tbody>
      </table>
    </div>  <!-- end col -->
    <div class="col">
      <table id="dt-allMonitor" class="table table-striped table-hover bg-dark table-dark" data-loading-template="loadingTemplate" style="white-space: nowrap;">
        <center><h3><b>All Monitors</b></h3></center>
        <thead>
          <tr>
            <th>Check Name</th>
            <th>Check Type</th>
            <th>Check Storage</th>
            <th>Options</th>
          </tr>
        </thead>
        <tfoot>
        <tbody>
        <?php
          foreach($fullMonitorList as $key => $liveMonitor) {
            echo '<tr><td>' . $liveMonitor['checkName'] . '</td>';
            echo '<td>' . $liveMonitor['type'] . '</td>';
            echo '<td>' . $liveMonitor['storage'] . '</td>';
            echo '<td>';
            echo '<form id="addMonitor' . $liveMonitor['id'] . '" action="" method="POST">';
            echo '<input type="hidden" name="monitorId" value="' . $liveMonitor['id'] . '">';
            echo '<input type="hidden" name="hostId" value="' . $id . '">';
            echo '<input type="hidden" name="hostname" value="' . $hostname . '">';
            echo '<input type="hidden" name="id" value="' . $id . '">';
            echo '<button form="addMonitor' . $liveMonitor['id'] . '" name="addMonitor" type="submit" class="btn btn-success btn-sm">Add Monitor</button></form></td></tr>';
          }
        ?>
        </tbody>
        </table>
      </div> <!-- end col -->
    </div>  <!-- end row -->
  </div>  <!-- end container -->

  <script> window.addEventListener("DOMContentLoaded", event => {
    const datatablesSimple = document.getElementById("dt-allMonitor");
    if (datatablesSimple) {
      new simpleDatatables.DataTable("#dt-allMonitor", {
        searchable: true,
        sortable: true,
        storable: true,
        paging: true,
        perPage: 15,
        perPageSelect:[25,50,100,200],
        labels: {
          placeholder: "All Monitors..."
        }
        });
      }
    });
  </script>

  <script> window.addEventListener("DOMContentLoaded", event => {
    const datatablesSimple = document.getElementById("dt-activeMonitor");
    if (datatablesSimple) {
      new simpleDatatables.DataTable("#dt-activeMonitor", {
        searchable: true,
        sortable: true,
        storable: true,
        paging: true,
        perPage: 15,
        perPageSelect:[25,50,100,200],
        labels: {
          placeholder: "Active Monitors..."
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
