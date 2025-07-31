<?php
require_once(__DIR__ . '/../../functions/generalFunctions.php');
checkCookie($_COOKIE);
require_once __DIR__ . "/../../config/api.php";

$headers = ['Authorization: Bearer ' . $_COOKIE['token']];
$post = [];

$rawDevicesList = callApiPost("/device/view", $post, $headers);
if (!is_array($rawDevicesList)) {
  $rawDevicesList = json_decode($rawDevicesList['response'], true);
}
$devicesList = json_decode($rawDevicesList['response'], true);
$responseCode = $devicesList['statusCode'];
$devices = $devicesList['data'];
$quitEarly = 0;

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

if ($quitEarly === 0):
?>
  <h1 class="text-center my-4">All Hosts and Devices</h1>

  <div class="container-fluid">
    <div class="col d-flex justify-content-center">
      <div class="card w-75" style="border-radius: .5%">
        <div class="card-body">
          <div class="table-responsive">
            <table id="dt-deviceList" class="table table-striped table-hover align-middle text-center text-nowrap">
              <thead>
                <tr>
                  <th>Device ID</th>
                  <th>Host Name</th>
                  <th>IP Address</th>
                  <th>Monitored</th>
                  <th>Alive</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($devices as $device): ?>
                  <tr>
                    <td><?= htmlspecialchars($device['id']) ?></td>
                    <td>
                      <a href="./index.php?page=deviceDetails.php&id=<?= urlencode($device['id']) ?>">
                        <?= htmlspecialchars($device['hostname']) ?>
                      </a>
                    </td>
                    <td><?= htmlspecialchars($device['address']) ?></td>
                    <td>
                      <?php
                      $state = (int)$device['productionState'];
                      if ($state === 0) {
                        echo '<span class="badge bg-success">Monitored</span>';
                      } elseif ($state === 1) {
                        echo '<span class="badge bg-secondary">Ignored</span>';
                      } else {
                        echo '<span class="badge bg-warning text-dark">Maintenance</span>';
                      }
                      ?>
                    </td>
                    <td>
                      <?php
                      $status = strtolower($device['isAlive']);
                      if ($status === "alive") {
                        echo '<img src="/images/generic/green_dot.png" style="width:20px;height:20px;" alt="Alive">';
                      } elseif ($status === "dead") {
                        echo '<img src="/images/generic/red_dot.png" style="width:20px;height:20px;" alt="Dead">';
                      } else {
                        echo '<img src="/images/generic/grey_dot.png" style="width:20px;height:20px;" alt="Unknown">';
                      }
                      ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div> <!-- table-responsive -->
        </div>
      </div>
    </div>
  </div>

  <script src="/js/simple-datatables/simple-datatables.js"></script>
  <script>
    window.addEventListener("DOMContentLoaded", () => {
      const table = document.getElementById("dt-deviceList");
      if (table) {
        new simpleDatatables.DataTable(table, {
          searchable: true,
          sortable: true,
          storable: true,
          paging: true,
          perPage: 25,
          perPageSelect: [25, 50, 100, 200],
          labels: {
            placeholder: "Search Devices..."
          }
        });
      }
    });
  </script>
<?php
else:
  loadUnknown("API calls failed in an unexpected way. Please reload.");
endif;
?>
