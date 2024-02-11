<?php
  /*
    Change Device information in the device table, and deviceGroup membership
  */

  echo "<br><br><br>";

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

  // Set initial page values if possible
  if (isset($_POST['id']) && ! isset($_POST['changeDevice'])) {
    $id = $_POST['id'];
    $hostname = $_POST['hostname'];
    $address = $_POST['address'];
    $firstSeen = $_POST['firstSeen'];
    $productionState = $_POST['productionState'];
    $isAlive = $_POST['isAlive'];

    // If we are actually changing membership of a group
    if (isset($_POST['changeDeviceGroup'])) {
      $post = array();
      $post =  [ 'change' => $_POST['change']];
      $post += [ 'deviceGroup' => $_POST['deviceGroup']];
      $post += [ 'hostname' => $_POST['id']];
      $changeDeviceGroupHost = callApiPost("/device/update", $post, $headers);
    }

    // If we are looking for monitors from a POST, continue here
    if (isset($_POST['deviceGroupMonitors'])) {
      $post = array();
      $post = ['deviceGroupMonitors' => $_POST['deviceGroupMonitors']];
      $findMonitors = callApiPost("/device/find", $post, $headers);
      $rawFindMonitors = json_decode($findMonitors['response'],true);
      foreach($rawFindMonitors['data'] as $singleMonitors) {
        $rawBody[] = $singleMonitors['checkName'];
      }
      $body ='';
      foreach ($rawBody as $singleBody) {
        $body .= $singleBody . "<br>";
      }
    }

    // Grab whatever is defined in the deviceGroup for display
    $post = ['deviceInDeviceGroup' => $_POST['id']];
    $rawDeviceGroupMember = callApiPost('/device/find', $post, $headers); // find deviceGroups id is a member of
    $deviceGroupMember = json_decode($rawDeviceGroupMember['response'], true);
    $deviceMember = $deviceGroupMember['data'];
    foreach($deviceMember as $singleMember) {
      $deviceMemberList[] = $singleMember['devicegroupName'];
    }
    $post = ['deviceGroup' => "deviceGroup" ];
    $rawDeviceGroupList = callApiPost('/device/find', $post, $headers);  // find all deviceGroups
    $deviceGroupList = json_decode($rawDeviceGroupList['response'],1);
    $groupList = $deviceGroupList['data'];
  }
  // If we changed the device itself, not a group change
  elseif (isset($_POST['changeDevice'])) {
    $post  = [ 'id' => $_POST['id'] ];
    $post += [ 'hostname' => $_POST['hostname'] ];
    $post += [ 'address' => $_POST['address'] ];
    $post += [ 'productionState' => $_POST['productionState'] ];

    $updateMyDevice = callApiPost("/device/update", $post, $headers);
    $rawResponse = json_decode($updateMyDevice['response'], true);
    $responseCode = $rawResponse['statusCode'];
    if ($responseCode !== 200 && $responseCode !== 403) {    // Anything other than a 200 OK is an issue
      decideResponse($responseCode, $responseString );
      $quitEarly = 1;
    }
    elseif ( $responseCode == 403) {
      load403Warn("Expired access credentials");
      $quitEarly = 1;
    }
    else {

      // After a successful update make sure we display the NEW values
      $id = $rawResponse['data'][0]['id'];
      $hostname = $rawResponse['data'][0]['hostname'];
      $address = $rawResponse['data'][0]['address'];
      $productionState = $rawResponse['data'][0]['productionState'];
      $isAlive = $_POST['isAlive'];
      $firstSeen = $_POST['firstSeen'];
      $post = array();
      $quitEarly = 0;
      successMessage('Device update is complete.');

      $post = ['deviceInDeviceGroup' => $_POST['id']];
      $rawDeviceGroupMember = callApiPost('/device/find', $post, $headers); // find deviceGroups id is a member of
      $deviceGroupMember = json_decode($rawDeviceGroupMember['response'], 1);
      $deviceMember = $deviceGroupMember['data'];
      foreach($deviceMember as $singleMember) {
        $deviceMemberList[] = $singleMember['devicegroupName'];
      }
      if (empty($deviceMemberList)) { $deviceMemberList = array(); }

      $post = ['deviceGroup' => "deviceGroup" ];
      $rawDeviceGroupList = callApiPost('/device/find', $post, $headers);  // find all deviceGroups
      $deviceGroupList = json_decode($rawDeviceGroupList['response'],1);
      $groupList = $deviceGroupList['data'];
    }
  }
  else {
    // We dont have basic information,  error out
    $quitEarly = 1 ;
    loadUnknown("Page was not loaded in a normal way.  Please go back to device page and try again.");
  }

  // If we are not a member of ANY group, just set empty array
  if (empty($deviceMemberList)) { $deviceMemberList = array(); }

//debugger($rawDeviceGroupMember);
//debugger($deviceMemberList);
//debugger($rawDeviceGroupMember);
//debugger($deviceGroupMember);
//debugger($deviceMember);
//debugger($_POST);
//debugger($rawResponse);
//debugger($rawDeviceGroupList);
//debugger($deviceGroupList);
//debugger($groupList);
//debugger($rawFindMonitors);
//debugger($findMonitors);
//debugger($rawBody);
//debugger($rawDeviceGroupMember);
//debugger($deviceGroupMember);
//debugger($deviceMember);
//debugger($deviceMemberList);
//debugger($deviceGroupList);
//debugger($groupList);
//exit();

  // Load our main page now
  if ($quitEarly == 0 ) {
  ?>


  <?php
    // Build your modal if someone requested monitor details and show it
    if (isset($_POST['deviceGroupMonitors'])) {
     showModal("Monitors associated with " . $_POST['deviceGroupMonitors'], $body);
    }
  ?>
  <script type="text/javascript">
    var myModal = new bootstrap.Modal(document.getElementById('deviceGroupModal'), {})
    myModal.toggle()
  </script>

<div class="container">
  <div class=" text-center mt-5 ">
    <h1>Change <?php echo '<a href="/host/index.php?&page=deviceDetails.php&id=' . $id . '">' . $hostname . '</a>'; ?> Details</h1><br>
  </div>
  <div class="row">
    <div class="col">
      <form id="change-details" role="form" action="" method="POST">
        <table class="table table-striped table-hover bg-dark table-dark" data-loading-template="loadingTemplate" style="white-space: nowrap;">
          <thead>
            <tr>
              <th>Device Id</th>
              <th>Hostname</th>
              <th>IP Address</th>
              <th>First Seen</th>
              <th>Can be active monitored</th>
              <th>Is Alive</th>
            </tr>
          </thead>
          <tbody>
          <tr>
            <td>
              <?php
                echo '<input type="text" name="id" class="form-control" value="' . $id . '" style="width: 50px" readonly></td>';
                echo '<td><input type="text" name="hostname" value="' . $hostname . '" style="width: 300px"></td>';
                echo '<td><input type="text" name="address" value="' . $address . '"></td>';
                echo '<td><input type="text" name="firstSeen" class="form-control" value="' . $firstSeen . '" readonly></td>';
                echo '<td><input type="text" name="productionState" value="' . $productionState . '" style="width: 40px"></td>';
                echo '<td><input type="text" name="isAlive" class="form-control" value="' . $isAlive . '" readonly>';
              ?>
            </td>
          </tr>
          </tbody>
        </table>
        <button type="submit" class="btn btn-warning" name="changeDevice" form="change-details"> Change Device </button>
      </form>
    </div>
    <div>
    <div class="col">
        <table class="table table-striped table-hover bg-dark table-dark" data-loading-template="loadingTemplate" style="white-space: nowrap;"><center><h3>Device Group Memberships</h3></center>
          <thead>
            <tr>
              <th>Device Group</th>
              <th>Service Check Groups</th>
              <th>Membership</th>
            </tr>
          </thead>
          <tbody>
          <tr>
             <?php
             foreach($groupList as $group) {
               if ( in_array($group['devicegroupName'], $deviceMemberList)) {
                 echo '<tr><td>' . $group['devicegroupName'] . "</td>";
                 echo '<td>';
                 echo '<form class="form-inline" id="find-serviceCheck' . $group['devicegroupName'] . '" role="form" action="" method="POST">';

                 echo '<input type="hidden" name="hostname" value="' . $hostname . '">';
                 echo '<input type="hidden" name="address" value="' . $address . '">';
                 echo '<input type="hidden" name="firstSeen" value="' . $firstSeen . '">';
                 echo '<input type="hidden" name="productionState" value="' . $productionState . '">';
                 echo '<input type="hidden" name="isAlive" value="' . $isAlive . '">';

                 echo '<input type="hidden" name="id" value="' . $id . '">';
                 echo '<input type="hidden" name="deviceGroupMonitors" value="' . $group['devicegroupName'] . '">';
                 echo '<button type="submit" class="btn btn-success" name="find-serviceCheck" form="find-serviceCheck' . $group['devicegroupName'] . '"> Show Group Service Checks </button> &nbsp ';
                 echo '</form>';
                 echo '</td><td>';

                 echo '<form class="form-inline" id="change-deviceGroup' . $group['devicegroupName'] . '" role="form" action="" method="POST">';
                 echo '<input type="hidden" name="id" value="' . $id . '">';
                 echo '<input type="hidden" name="hostname" value="' . $hostname . '">';
                 echo '<input type="hidden" name="address" value="' . $address . '">';
                 echo '<input type="hidden" name="firstSeen" value="' . $firstSeen . '">';
                 echo '<input type="hidden" name="productionState" value="' . $productionState . '">';
                 echo '<input type="hidden" name="isAlive" value="' . $isAlive . '">';
                 echo '<input type="hidden" name="deviceGroup" value="' . $group['devicegroupName'] . '">';
                 echo '<input type="hidden" name="change" value="remove">';
                 echo '<button type="submit" class="btn btn-warning" name="changeDeviceGroup" form="change-deviceGroup' . $group['devicegroupName'] . '"> Remove Membership </button> ';
                 echo '</form>';
                 echo '</td></tr>';
               }
               else {
                 echo '<tr><td>' . $group['devicegroupName'] . "</td>";
                 echo '<td>';
                 echo '<form class="form-inline" id="find-serviceCheck' . $group['devicegroupName'] . '" role="form" action="" method="POST">';
                 echo '<input type="hidden" name="hostname" value="' . $hostname . '">';
                 echo '<input type="hidden" name="address" value="' . $address . '">';
                 echo '<input type="hidden" name="firstSeen" value="' . $firstSeen . '">';
                 echo '<input type="hidden" name="productionState" value="' . $productionState  . '">';
                 echo '<input type="hidden" name="isAlive" value="' . $isAlive . '">';
                 echo '<input type="hidden" name="id" value="' . $id . '">';
                 echo '<input type="hidden" name="deviceGroupMonitors" value="' . $group['devicegroupName'] . '">';
                 echo '<button type="submit" class="btn btn-success" name="find-serviceCheck" form="find-serviceCheck' . $group['devicegroupName'] . '"> Show Group Service Checks </button> &nbsp ';
                 echo '</form>';
                 echo '</td><td>';

                 echo '<form class="form-inline" id="change-deviceGroup' . $group['devicegroupName'] . '" role="form" action="" method="POST">';
                 echo '<input type="hidden" name="hostname" value="' . $hostname . '">';
                 echo '<input type="hidden" name="address" value="' . $address . '">';
                 echo '<input type="hidden" name="firstSeen" value="' . $firstSeen . '">';
                 echo '<input type="hidden" name="productionState" value="' . $productionState . '">';
                 echo '<input type="hidden" name="isAlive" value="' . $isAlive . '">';
                 echo '<input type="hidden" name="id" value="' . $id . '">';
                 echo '<input type="hidden" name="deviceGroup" value="' . $group['devicegroupName'] . '">';
                 echo '<input type="hidden" name="change" value="add">';
                 echo '<button type="submit" class="btn btn-success" name="changeDeviceGroup" form="change-deviceGroup' . $group['devicegroupName'] . '"> Add Membership </button> ';
                 echo '</form>';
                 echo '</td></tr>';
               }
             }
             ?>
          </tr>
          </tbody>
        </table>
    </div>
  </div>





  <?php
  }
  else {
    // Something went very wrong with the API call, but keep the layout clean...
    loadUnknown("Page failed in an unexpected way.  Please reload");
  }
?>

