<?php
  /*
    We are going to search through a list for a specfic monitor which
    is going to allow us to change, delete, addHostId, addHostGroup
  */


  // Only needed for debugging and bypassing security, etc
  // require_once(__DIR__ . '/../../functions/generalFunctions.php');
  // checkCookie($_COOKIE);  // disable check here to test 401 responses elsewhere due to expired stuff

  // Load local vars for use (urls, ports, etc)
  // require_once __DIR__ . "/../../config/api.php";

  // Grab our POSSIBLE values so users can choose what they change
  $headers = array();
  $headers[] = 'Authorization: Bearer ' . $_COOKIE['token'];
  $post = array();  // We are using post, so give it an empty array to post with

  // This is from an INTERNAL POST ONLY when we have changed a monitor
  if ( isset($_POST['changeMyMonitor'])) {
    $post = $_POST;
    $changeMyMonitor = callApiPost("/monitors/updateMonitor", $post, $headers);
    $rawResponse = json_decode($changeMyMonitor['response'], true);
    $responseCode = $rawResponse['statusCode'];
    $post = array();
    if ($responseCode !== 200 && $responseCode !== 403) {    // Anything other than a 200 OK is an issue
      echo "<br><br><br>";
      decideResponse($responseCode, $responseString );
    }
    elseif ( $responseCode == 403) {
      load403Warn("Expired access credentials");
    }
    else {
      // After a successful update, wait and then reload the page
      echo "<br><br><br>";
      successMessage('Monitor update is successful.');
    }
  }
  // Success or failure, continue loading the page

  // debugger($_POST);

  // Set the values we know right now passed from parent page or a self post
  $id = $_POST['id'];
  $type = $_POST['type'];
  $storage = $_POST['storage'];
  $iteration = $_POST['iteration'];
  $checkName = $_POST['checkName'];
  $checkAction = $_POST['checkAction'];
  $hostid = $_POST['hostid'];
  $hostGroup = $_POST['hostGroup'];

  // we SHOULD have gotten an array.... but if not convert it back to one
  $rawIteration = callApiPost("/monitors/findMonitorIteration" , $post, $headers);
  if (! is_array($rawIteration)) {
    $rawIteration = json_decode($rawIteration, true);
  }

  $rawType = callApiPost("/monitors/findMonitorType", $post, $headers);
  if (! is_array($rawType)) {
    $rawType = json_decode($rawType, true);
  }

  $rawStorage = callApiPost("/monitors/findMonitorStorage", $post, $headers);
  if (! is_array($rawStorage)) {
    $rawStorage = json_decode($rawStorage, true);
  }


  // Second table so we know ids to add
  $rawDevice = callApiPost("/monitors/findDeviceId", $post, $headers);
  if ( ! is_array($rawDevice)) {
    $rawDevice = json_decode($rawDevice, true);
  }

  // Third table for a list of host groups
  $rawHostGroup = callApiPost("/monitors/findHostGroup" , $post, $headers);
  if ( ! is_array($rawHostGroup)) {
    $rawHostGroup = json_decode($rawHostGroup, true);
  }

  // Clean your data for display
  $deviceIdList = json_decode($rawDevice['response'], true);
  $HostGroupList = json_decode($rawHostGroup['response'], true);
  $iterationList = json_decode($rawIteration['response'], true);
  $typeList = json_decode($rawType['response'], true);
  $storageList = json_decode($rawStorage['response'],true);

  $selectIteration = array();
  $selectType = array();
  $selectStorage = array();
  $searchDeviceId = array();
  $searchHostGroup = array();

  //echo "<br><br><br>";
  foreach ( $iterationList['data']['result'] as $iter) {
    $selectIteration[] = $iter['iteration'];
  }
  asort($selectIteration);

  foreach ($typeList['data']['result'] as $typ) {
    $selectType[] = $typ['type'];
  }
  asort($selectType);

  foreach ($storageList['data']['result'] as $stor) {
    $selectStorage[] = $stor['storage'];
  }
  asort($selectStorage);

  foreach ($deviceIdList['data']['result'] as $idList) {
    $searchDeviceId[] = $idList;
  }

  foreach ($HostGroupList['data']['result'] as $hostGroupList) {
    $searchHostGroup[] = $hostGroupList;
  }

  $quitEarly = 0;

  if ($quitEarly == 0) {
?>
<br><br>
<div class="container">
  <div class=" text-center mt-5 ">
    <h1>Change Existing Monitor</h1><br>
  </div>
  <div class="row">
    <div class="col">
      <form id="create-monitor-form" role="form" action="" method="POST">
      <div class="controls">
        <div class="row">
          <div class="col-6">
            <div class="form-group">
              <label for="form_checkName">Check Name *</label>
              <?php echo '<input id="form_checkName" type="text" name="checkName" class="form-control" value="' . $checkName . '" required="required" data-error="A unique check name is required.">';  ?>
            </div> <!-- form-group -->
          </div>  <!-- col -->
        </div>  <!-- row -->
        <div class="row">
          <div class="col-3">
            <div class="form-group">
              <label for="form_storage">Storage *</label>
              <select id="form_storage" name="storage" class="form-control" required="required" data-error="Please specify your storage type.">
              <?php
               foreach ($selectStorage as $finalStorage) {
                 if ($finalStorage == $storage) {
                   echo "<option value=\"" . $finalStorage . "\" selected >" . $finalStorage . "</option>";
                 }
                 else {
                   echo "<option value=\"" . $finalStorage . "\">" . $finalStorage . "</option>";
                 }
               }
              ?>
              </select>
            </div> <!-- form-group -->
          </div> <!-- column -->
          <div class="col-3">
            <div class="form-group">
              <label for="form_iteration">Iteration *</label>
               <select id="form_iteration" name="iteration" class="form-control" required="required" data-error="Please specify your iteration cycle.">
               <option value="" selected disabled>--Select Iteration cycle--</option>
                <?php
                foreach ($selectIteration as $finalIteration) {
                if ( $finalIteration == $iteration) {
                  echo "<option value=\"" . $finalIteration . "\" selected >" . $finalIteration . "</option>";
                }
                else {
                  echo "<option value=\"" . $finalIteration . "\">" . $finalIteration . "</option>";
                }
                }
                ?>
              </select>
            </div>
          </div>
          <div class="col-3">
            <div class="form-group">
              <label for="form_type">Type of check *</label>
              <select id="form_type" name="type" class="form-control" required="required" data-error="Please specify your monitor type.">
              <?php
                foreach ($selectType as $finalType) {
                  if ( $finalType == $type ) {
                    echo "<option value=\"" . $finalType . "\" selected >" . $finalType . "</option>";
                  }
                  else {
                    echo "<option value=\"" . $finalType . "\">" . $finalType . "</option>";
                  }
                }
                ?>
              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="form-group">
              <label for="form_checkAction">Check Action *</label>
              <?php echo '<textarea id="form_checkAction" name="checkAction" class="form-control" rows="4" required="required" data-error="something in this field choked.">' . $checkAction . '</textarea>'; ?>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
           <div class="form-group">
             <label for="form_checkAction">Monitored Host Id numbers as CSV *</label>
             <?php echo '<textarea id="form_checkAction" name="hostid" class="form-control" rows="4" required="false" data-error="something in this field choked.">' . $hostid . '</textarea>'; ?>
           </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="form-group">
              <label for="form_checkAction">Monitored Host Group names as CSV *</label>
              <?php echo '<textarea id="form_checkAction" name="hostGroup" class="form-control" rows="4" required="false" data-error="something in this field choked.">' . $hostGroup . '</textarea>'; ?>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12"><br>
            <input type="hidden" name="changeMyMonitor" value="true">
            <?php echo '<input type="hidden" name="id" value="' . $id . '">'; ?>
            <input type="submit" class="btn btn-success btn-send  pt-2 btn-block" value="Change Monitor" >
          </div>
        </div> <!-- row for form -->
        </div> <!-- controls -->
        </form>
      </div> <!-- column end -->

      <!--  Ugly, but this is second column to show searchable hostIds via dataTable SIMPLE, dont get cute!  -->
      <div class="col-sm-3">
        <div class="col-sm">
          <table id="dt-deviceid" class="table table-striped table-hover bg-dark table-dark" data-loading-template="loadingTemplate">
            <thead><tr><th>id</th><th>Device</th></tr></thead>
            <?php
              foreach ( $searchDeviceId as $ids) {
                echo "<tr><td>" . $ids['id'] . "</td><td>" . $ids['hostname'] . "</td></tr>";
              }
            ?>
          </table>
        </div>
      </div>
      <div class="col-sm-3">
        <table id="dt-hostgroup" class="table table-striped table-hover bg-dark table-dark" data-loading-template="loadingTemplate">
          <thead><tr><th>Host Group Name</th></tr></thead>
          <?php
            foreach ($searchHostGroup as $hgids) {
              echo "<tr><td>" . $hgids['devicegroupName'] . "</td></tr>";
            }
          ?>
        </table>
      </div>
    </div>
   </div> <!-- container -->


<script> window.addEventListener("DOMContentLoaded", event => {
  const datatablesSimple = document.getElementById("dt-hostgroup");
  if (datatablesSimple) {
    new simpleDatatables.DataTable("#dt-hostgroup", {
      searchable: true,
      sortable: false,
      storable: false,
      paging: true,
      perPage: 10,
      perPageSelect:[10,20],
      labels: {
        placeholder: "hostgroup filter"
      }
      });
    }
  });
</script>

<script> window.addEventListener("DOMContentLoaded", event => {
  const datatablesSimple = document.getElementById("dt-deviceid");
  if (datatablesSimple) {
    new simpleDatatables.DataTable("#dt-deviceid", {
      searchable: true,
      sortable: false,
      storable: false,
      paging: true,
      perPage: 10,
      perPageSelect:[10,20],
      labels: {
        placeholder: "hostname filter"
      }
      });
    }
  });
</script>

  <script src="/js/simple-datatables/simple-datatables.js"></script>
  <script src="/js/simple-datatables/script.js"></script>

<?php
  }


  else {
    // Our API did not give us usable information.  May be transient, or API server is borked.
    loadUnknown("API calls failed in an unexpected way.  Please reload");
  }


?>
