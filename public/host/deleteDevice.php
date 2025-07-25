<?php
/*
  As this is a discrete page being called, we need to load the support pages
  They contain the support functions needed for the POST calls, as well
  as validation that the user is still logged in
*/

// Only needed for debugging and bypassing security, etc
require_once(__DIR__ . '/../../functions/generalFunctions.php');
checkCookie($_COOKIE);  // disable check here to test 401 responses elsewhere due to expired stuff

// Load local vars for use (urls, ports, etc)
require_once __DIR__ . "/../../config/api.php";

// Hosts and Devices have A LOT of variables in play.  We need functions specific to this group
require_once __DIR__ . "/functions/hostFunctions.php";

$sharedDevice = [];
if (!empty($_POST['sharedDevice'])) {
    $sharedDevice = json_decode($_POST['sharedDevice'], true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        die("Invalid sharedDevice data");
    }
}

// Grab our POSSIBLE values so users can choose what they change
$headers = array();
$headers[] = 'Authorization: Bearer ' . $_COOKIE['token'];
$post = array();  // We are using post, so give it an empty array to post with
$quitEarly = 0;

// debugger($_POST);
if (empty($_POST['id']) && ! isset($id)) {
  $id=$sharedDevice['properties']['data'][0]['id'];
}
else {
  $id = $_POST['id'];
}

// debugger($id);
// exit();

  if (isset($_POST['deviceDelete'])) {
    $post = [ 'id' => $_POST['id'] ];

    $rawDeleteDevice = callApiPost("/device/delete", $post, $headers);
    $deleteDevice = json_decode($rawDeleteDevice['response'],true);
    $responseCode = $deleteDevice['statusCode'];
    if ($responseCode !== 200 && $responseCode !== 403) {    // Anything other than a 200 OK is an issue
      decideResponse($responseCode, $responseString );
      $quitEarly = 1;
    }
    elseif ( $responseCode == 403) {
      load403Warn("Expired access credentials");
      $quitEarly = 1;
    }
    else {
      // After a successful update, wait and then reload the page
      successMessage('Device deletion is complete.');
      $quitEarly = 2;
      echo '<script>
              window.setTimeout(function() {
                window.location = "/host/index.php";
              }, 2000);
            </script>';
    }
  }

// Assuming you're pulling this host info from a shared structure
$host = $sharedDevice['properties']['data'][0] ?? [];
$monitors = $sharedDevice['monitors']['data'] ?? [];

$hostname = $host['hostname'] ?? 'Unknown';
$ip = $host['address'] ?? 'N/A';
$firstSeen = $host['firstSeen'] ?? 'N/A';
?>
<div class="container mt-4">

  <!-- Host Details Card -->
  <div class="card border-primary mb-4">
    <div class="card-header bg-primary text-white">
      Host Details
    </div>
    <div class="card-body">
      <dl class="row">
        <dt class="col-sm-4">Hostname</dt>
        <dd class="col-sm-8"><?= htmlspecialchars($hostname) ?></dd>

        <dt class="col-sm-4">IP Address</dt>
        <dd class="col-sm-8"><?= htmlspecialchars($ip) ?></dd>

        <dt class="col-sm-4">First Seen</dt>
        <dd class="col-sm-8"><?= htmlspecialchars($firstSeen) ?></dd>
      </dl>
    </div>
  </div>

  <!-- Monitors List Card -->
  <div class="card border-secondary mb-4">
    <div class="card-header bg-secondary text-white">
      Active Monitors
    </div>
    <div class="card-body p-0">
      <?php if (!empty($monitors) && is_array($monitors)): ?>
        <ul class="list-group list-group-flush">
          <?php foreach ($monitors as $monitor): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <span>
                <i class="fas fa-eye me-1 text-primary"></i>
                <?= htmlspecialchars($monitor['checkName']) ?>
              </span>
              <span class="badge 
                <?php
                  switch (strtolower($monitor['type'])) {
                    case 'nrpe': echo 'bg-success'; break;
                    case 'alive': case 'ping': echo 'bg-success text-dark'; break;
                    case 'get': case 'walk': case 'snmpget': case 'snmp': echo 'bg-warning text-dark'; break;
                    case 'shell': default: echo 'bg-secondary'; break;
                  }
                ?>">
                <?= htmlspecialchars(strtoupper($monitor['type'] ?? 'UNKNOWN')) ?>
              </span>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <div class="p-3 text-muted">No monitors defined for this host.</div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Delete Button (triggers modal) -->
  <div class="text-center">
    <button class="btn btn-danger btn-lg" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">
      Delete Host
    </button>
  </div>

</div>

<!-- Bootstrap Modal for Confirmation -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content border-danger">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="confirmDeleteModalLabel">Confirm Host Deletion</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Are you absolutely sure you want to delete the host <strong><?= htmlspecialchars($hostname) ?></strong>?</p>
        <p>This action cannot be undone.</p>
      </div>
      <div class="modal-footer">
        <form method="post" action="/host/index.php?&page=deleteDevice.php">
          <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
          <input type="hidden" name="deviceDelete" value="deviceDelete">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Yes, Delete Host</button>
        </form>
      </div>
    </div>
  </div>
</div>
