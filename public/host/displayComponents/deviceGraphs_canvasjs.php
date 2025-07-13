<?php
/**
 * deviceGraphs_canvasjs.php
 *
 * This page handles CanvasJS-based graph rendering for a specific device.
 * It displays a list of available CanvasJS-compatible graphs (checkType + checkName pairs)
 * and lets the user drill into each one via form submission.
 * vars set are id, hostname and activeMonitors as an array
 */

require_once __DIR__ . '/../../../functions/generalFunctions.php';
require_once __DIR__ . '/../functions/hostFunctions.php';

if (!isset($activeMonitors)) {
  echo loadUnknown("Device data not available");
  exit;
}

$canvasChecks = [];
$allChecks = $activeMonitors ?? [];

foreach ($allChecks as $check) {
  if (($check['storage'] ?? '') === 'graphite') {
    $canvasChecks[] = $check;
  }
}

if (empty($canvasChecks)) {
  echo loadUnknown("No CanvasJS-compatible graphs available for this device.");
  exit;
}
?>
<div class="container mt-5">
  <div class="card">
    <div class="card-header bg-primary text-white">
      <h5 class="mb-0">Available CanvasJS Graphs for <?php echo htmlspecialchars($sharedDevice['host']); ?></h5>
    </div>
    <div class="card-body">
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th>Type</th>
            <th>Name</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($canvasChecks as $check): ?>
            <?php
              $checkName = $check['checkName'] ?? 'unknown';
              $checkTypeRaw = $check['type'] ?? 'unknown';
              $checkType = in_array($checkTypeRaw, ['get', 'walk', 'snmp']) ? 'snmp' : $checkTypeRaw;
            ?>
            <tr>
              <td><?php echo htmlspecialchars($check['type']); ?></td>
              <td><?php echo htmlspecialchars($check['checkName']); ?></td>
              <td>
                <form method="POST" action="/host/graphs/canvasJs.php" target="_blank">
                  <input type="hidden" name="checkType" value="<?php echo htmlspecialchars($checkType); ?>">
                  <input type="hidden" name="checkName" value="<?php echo htmlspecialchars($checkName); ?>">
                  <input type="hidden" name="hostname" value="<?php echo htmlspecialchars($hostname); ?>">
                  <input type="hidden" name="specialHostname" value="<?php echo htmlspecialchars($specialHostname); ?>">
                  <input type="hidden" name="id" value="<?php echo (int)$id; ?>">
                  <input type="submit" class="btn btn-sm btn-primary" value="View">
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
