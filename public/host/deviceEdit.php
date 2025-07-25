<?php
require_once(__DIR__ . '/../../functions/generalFunctions.php');
require_once(__DIR__ . "/../../config/api.php");
require_once(__DIR__ . "/functions/hostFunctions.php");

$headers = ['Authorization: Bearer ' . $_COOKIE['token']];
$quitEarly = 0;
$deviceMemberList = [];
$groupList = [];
$hostname = $address = $firstSeen = $productionState = $isAlive = $id = null;
$monitorModalBody = '';
$monitorModalTitle = '';

if (isset($_POST['id']) && !isset($_POST['changeDevice'])) {
    extract($_POST);  // sets $id, $hostname, etc.

    // Normalize productionState
    $productionState = ($productionState == 0) ? 'available' : 'disabled';

    if (isset($_POST['changeDeviceGroup'])) {
        $post = [
            'change' => $_POST['change'],
            'deviceGroup' => $_POST['deviceGroup'],
            'hostname' => $_POST['id']
        ];
        $changeResult = callApiPost("/device/update", $post, $headers);
    }

    if (isset($_POST['deviceGroupMonitors'])) {
        $post = ['deviceGroupMonitors' => $_POST['deviceGroupMonitors']];
        $findMonitors = callApiPost("/device/find", $post, $headers);
        $decoded = json_decode($findMonitors['response'], true);
        foreach ($decoded['data'] as $m) {
            $monitorModalBody .= htmlspecialchars($m['checkName']) . "<br>";
        }
        $monitorModalTitle = "Monitors associated with " . $_POST['deviceGroupMonitors'];
    }

    // Get group membership
    $post = ['deviceInDeviceGroup' => $id];
    $response = callApiPost('/device/find', $post, $headers);
    $deviceMemberList = array_column(json_decode($response['response'], true)['data'], 'devicegroupName');

    // Get all device groups
    $post = ['deviceGroup' => "deviceGroup"];
    $groupResponse = callApiPost('/device/find', $post, $headers);
    $groupList = json_decode($groupResponse['response'], true)['data'];
}

elseif (isset($_POST['changeDevice'])) {
    extract($_POST);
    $post = [
        'id' => $id,
        'hostname' => $hostname,
        'address' => $address,
        'productionState' => ($productionState == 'available') ? 0 : 1
    ];
    $response = callApiPost("/device/update", $post, $headers);
    $result = json_decode($response['response'], true);
    $code = $result['statusCode'];

    if ($code !== 200 && $code !== 403) {
        decideResponse($code, $response['response']);
        $quitEarly = 1;
    } elseif ($code === 403) {
        load403Warn("Expired access credentials");
        $quitEarly = 1;
    } else {
        successMessage('Device update is complete.');
        $device = $result['data'][0];
        $hostname = $device['hostname'];
        $address = $device['address'];
        $productionState = $device['productionState'];
        $id = $device['id'];
        $isAlive = $_POST['isAlive'];
        $firstSeen = $_POST['firstSeen'];

        $post = ['deviceInDeviceGroup' => $id];
        $response = callApiPost('/device/find', $post, $headers);
        $deviceMemberList = array_column(json_decode($response['response'], true)['data'], 'devicegroupName');

        $post = ['deviceGroup' => "deviceGroup"];
        $groupResponse = callApiPost('/device/find', $post, $headers);
        $groupList = json_decode($groupResponse['response'], true)['data'];
    }
} else {
    $quitEarly = 1;
    loadUnknown("Page was not loaded in a normal way. Please go back to the device page and try again.");
}

if ($quitEarly === 0):
?>

<div class="container mt-5">
  <h1 class="text-center mb-4">Modify Device: <a href="/host/index.php?page=deviceDetails.php&id=<?= htmlspecialchars($id) ?>"><?= htmlspecialchars($hostname) ?></a></h1>


  <?php include 'displayComponents/deviceEditForm.php'; ?>
  <?php include 'displayComponents/deviceEditGroupMemberships.php'; ?>
  <?php if ($monitorModalBody): ?>
    <?php include 'displayComponents/deviceEditMonitorModal.php'; ?>
    <script>
      var modal = new bootstrap.Modal(document.getElementById('deviceGroupModal'));
      modal.show();
    </script>
  <?php endif; ?>
</div>

<?php else: ?>
  <div class="alert alert-danger m-4">Failed to load device information.</div>
<?php endif; ?>
