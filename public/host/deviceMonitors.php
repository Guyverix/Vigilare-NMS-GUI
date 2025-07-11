<?php
require_once(__DIR__ . '/../../functions/generalFunctions.php');
require_once(__DIR__ . '/../../config/api.php');
require_once(__DIR__ . '/functions/hostFunctions.php');

$headers = ['Authorization: Bearer ' . $_COOKIE['token']];
$quitEarly = 0;

$id = $_POST['id'] ?? null;
$hostname = $_POST['hostname'] ?? 'Unknown Host';

if (!$id) {
    loadUnknown("Page was called without correct parameters. Please go back and try again.");
    $quitEarly = 1;
}

// Add Monitor
if (isset($_POST['addMonitor'])) {
    $post = ['id' => $_POST['monitorId'], 'hostId' => $_POST['hostId']];
    $response = callApiPost("/monitors/monitorAddHost", $post, $headers);
    $result = json_decode($response['response'], true);
    $status = $result['statusCode'] ?? 500;

    switch ($status) {
        case 200:
            successMessage('Device has been added to requested monitor.');
            break;
        case 403:
            load4XX(); $quitEarly = 1; break;
        default:
            decideResponse($status); $quitEarly = 1; break;
    }
}

// Remove Monitor
if (isset($_POST['removeMonitor'])) {
    $post = ['id' => $_POST['monitorId'], 'hostId' => $_POST['hostId']];
    $response = callApiPost("/monitors/monitorDeleteHost", $post, $headers);
    $result = json_decode($response['response'], true);
    $status = $result['statusCode'] ?? 500;

    switch ($status) {
        case 200:
            successMessage($result['data']['result'] ?? 'Monitor removed.');
            break;
        case 403:
            load4XX(); $quitEarly = 1; break;
        default:
            decideResponse($status); $quitEarly = 1; break;
    }
}

// Get current monitors
if ($quitEarly === 0) {
    $post = ['id' => $id];
    $activeMonitorsResp = callApiPost("/monitors/findMonitorsByHostId", $post, $headers);
    $activeMonitors = json_decode($activeMonitorsResp['response'], true)['data']['result'] ?? [];

    $allMonitorsResp = callApiPost("/monitors/findMonitors", [], $headers);
    $allMonitorsData = json_decode($allMonitorsResp['response'], true);
    $status = $allMonitorsData['statusCode'] ?? 500;

    if ($status === 200) {
        $allMonitors = $allMonitorsData['data']['result'];
    } elseif ($status === 403) {
        load4XX(); $quitEarly = 1;
    } else {
        decideResponse($status); $quitEarly = 1;
    }
}

$activeMonitorIds = array_column($activeMonitors, 'id');

// Filter out any already-active monitor from the full list
$availableMonitors = array_filter($allMonitors, function ($monitor) use ($activeMonitorIds) {
    return !in_array($monitor['id'], $activeMonitorIds);
});

?>

<?php if ($quitEarly === 0): ?>
<div class="container mt-5">
  <h1 class="text-center mb-4">
    Change Monitors for 
    <a href="/host/index.php?&page=deviceDetails.php&id=<?= htmlspecialchars($id) ?>">
      <?= htmlspecialchars($hostname) ?>
    </a>
  </h1>

  <div class="row mb-5">
    <div class="col-lg-6">
      <?php include 'displayComponents/deviceMonitorTableActive.php'; ?>
    </div>
    <div class="col-lg-6">
      <?php include 'displayComponents/deviceMonitorTableAll.php'; ?>
    </div>
  </div>
</div>

<script src="/js/simple-datatables/simple-datatables.js"></script>
<script>
  const initDataTables = (id, label) => {
    const el = document.getElementById(id);
    if (el) {
      new simpleDatatables.DataTable(el, {
        searchable: true,
        sortable: true,
        paging: true,
        perPage: 15,
        perPageSelect: [25, 50, 100, 200],
        labels: { placeholder: label }
      });
    }
  };

  document.addEventListener("DOMContentLoaded", () => {
    initDataTables("dt-activeMonitor", "Active Monitors...");
    initDataTables("dt-allMonitor", "All Monitors...");
  });
</script>
<?php else: ?>
<div class="alert alert-danger m-4">API calls failed in an unexpected way. Please reload.</div>
<?php endif; ?>
