<?php
require_once(__DIR__ . '/../../functions/generalFunctions.php');
require_once __DIR__ . "/../../config/api.php";
include __DIR__ . "/functions/eventFunctions.php";

checkCookie($_COOKIE);

$headers = ['Authorization: Bearer ' . $_COOKIE['token']];
$cookieTimezone = $_COOKIE['clientTimezone'] ?? 'UTC +0';
$displayAck = $_COOKIE['showEventAck'] ?? 'true';
$displaySeverity = isset($_COOKIE['showEventSeverity'])
  ? explode(',', $_COOKIE['showEventSeverity'])
  : ["0", "1", "2", "3", "4", "5"];

$cookieTimezone = explode(' ', $cookieTimezone);
$localOffset = ((int)($cookieTimezone[1] ?? 0) * 3600);
$localTime = strtotime("now") + $localOffset;

if (isset($_POST['realMoveToHistory'])) {
  $post = [
    'id' => $_POST['id'],
    'reason' => $_POST['reason']
  ];
  callApiPost("/events/moveToHistory", $post, $headers);
  echo '<script>if (window.history.replaceState) window.history.replaceState(null, null, window.location.href);</script>';
}

if (isset($_POST['moveToHistory'])) {
  modalMoveToHistory($_POST['evid'], $_POST['hostname']);
  echo '<script>new bootstrap.Modal(document.getElementById("eventToHistoryModal")).toggle();</script>';
}

if (isset($_POST['displayDetails'])) {
  showEventModal($_POST);
  echo '<script>new bootstrap.Modal(document.getElementById("showEventModal")).toggle();</script>';
}

?>
<h1 class="text-center my-4">Live Events</h1>
<div class="container-fluid">
  <div class="row">
    <div class="col-md-9">
      <form id="saveFilter" onsubmit="return false;" class="d-flex flex-wrap gap-2">
        <?php
        for ($i = 1; $i <= 5; $i++) {
          $checked = in_array((string)$i, $displaySeverity) ? 'checked' : '';
          $labels = ["Debug", "Information", "Error", "Warning", "Critical"];
          $btnClass = ["secondary", "primary", "info", "warning", "danger"];
          echo "<label class='btn btn-sm btn-outline-{$btnClass[$i-1]}'>";
          echo "<input type='checkbox' name='activeFilter[]' value='{$i}' {$checked}> {$labels[$i-1]}";
          echo "</label>\n";
        }
        ?>
        <button type="submit" class="btn btn-sm btn-outline-primary" name="saveFilter">
          <i class="fas fa-bookmark"></i> Save filter
        </button>
      </form>
    </div>
    <div class="col-md-3 text-end">
      <form id="chooseAck" method="POST">
        <?php if ($displayAck === 'true'): ?>
          <button name="viewAck" value="false" class="btn btn-sm btn-outline-success">Hide Acknowledged</button>
        <?php else: ?>
          <button name="viewAck" value="true" class="btn btn-sm btn-outline-success">Show Acknowledged</button>
        <?php endif; ?>
      </form>
    </div>
  </div>

  <div class="row mt-3">
    <div class="col text-end">
      <span class="text-muted">Last Refresh: <?= date('Y-m-d H:i:s', $localTime) ?></span>
    </div>
  </div>

  <div class="table-responsive mt-3">
    <table id="dt-events" class="table table-striped table-hover bg-dark table-dark text-center text-nowrap">
      <thead>
        <tr>
          <th>Device</th>
          <th>Monitor</th>
          <th>Summary</th>
          <th>First Seen</th>
          <th>Last Update</th>
          <th>Count</th>
          <th>Severity</th>
          <th>Manipulation</th>
        </tr>
      </thead>
      <tbody id="eventTableBody">
        <!-- Content replaced dynamically by JS -->
      </tbody>
    </table>
  </div>
</div>

<script src="/js/simple-datatables/simple-datatables.js"></script>
<script>
function saveEventSetting(name, value) {
  fetch(`/event/saveEventSetting.php?name=${encodeURIComponent(name)}&value=${encodeURIComponent(value)}`)
    .then(resp => resp.ok ? console.log(`Saved: ${name} = ${value}`) : console.warn("Save failed"))
    .catch(err => console.error("Error saving setting:", err));
}

document.addEventListener("DOMContentLoaded", () => {
  const filterForm = document.getElementById("saveFilter");
  if (filterForm) {
    filterForm.addEventListener("submit", () => {
      const selected = Array.from(filterForm.querySelectorAll('input[name="activeFilter[]"]:checked'))
                            .map(el => el.value);
      saveEventSetting("showEventSeverity", selected.join(","));
      setTimeout(() => location.reload(), 200);
    });
  }

  const ackButton = document.querySelector("#chooseAck button[name='viewAck']");
  if (ackButton) {
    ackButton.addEventListener("click", (e) => {
      e.preventDefault();
      const val = ackButton.value;
      saveEventSetting("showEventAck", val);
      document.getElementById("chooseAck").submit();
    });
  }

  function refreshEventTable() {
    fetch('/event/eventData.php')
      .then(response => response.text())
      .then(html => {
        const tbody = document.getElementById('eventTableBody');
        if (tbody) tbody.innerHTML = html;
      })
      .catch(err => console.error("Event fetch error:", err));
  }

  setInterval(refreshEventTable, 45000);
  refreshEventTable();

  const datatablesSimple = document.getElementById("dt-events");
  if (datatablesSimple) {
    new simpleDatatables.DataTable("#dt-events", {
      searchable: true,
      sortable: true,
      storable: true,
      paging: true,
      perPage: 25,
      perPageSelect: [25, 50, 100, 200],
      labels: {
        placeholder: "Search Active Events"
      }
    });
  }
});
</script>
