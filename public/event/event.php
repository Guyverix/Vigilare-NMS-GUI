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
//          echo "<label class='btn btn-sm btn-outline-{$btnClass[$i-1]}'>";
          echo "<label class='btn btn-sm btn-{$btnClass[$i-1]}'>";
          echo "<input type='checkbox' name='activeFilter[]' value='{$i}' {$checked}> {$labels[$i-1]}";
          echo "</label>\n";
        }
        ?>
<!--        <button type="submit" class="btn btn-sm btn-outline-primary" name="saveFilter">    -->
        <button type="submit" class="btn btn-sm btn-outline-success" name="saveFilter">
          <i class="fas fa-bookmark"></i> Save filters
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
          <th>Severity</th>
          <th>Device</th>
          <th>Monitor</th>
          <th>Summary</th>
          <th>First Seen</th>
          <th>Last Update</th>
          <th>Count</th>
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
let dt;
const tableEl = document.getElementById("dt-events");

const DT_OPTIONS = {
  searchable: true,
  sortable: true,
  storable: true,             // also persists to localStorage; our manual restore keeps it instant
  paging: true,
  perPage: 25,
  perPageSelect: [25, 50, 100, 200],
  labels: { placeholder: "Search Active Events" }
};

// ---- read current UI state from the rendered DataTable (safe across versions) ----
function readDTState() {
  if (!tableEl) return {};
  const state = {};

  // Sort: read from thead aria-sort + index
  const th = tableEl.querySelector('thead th[aria-sort="ascending"], thead th[aria-sort="descending"]');
  if (th) {
    state.sortIndex = Array.from(th.parentNode.children).indexOf(th);
    state.sortDir   = th.getAttribute('aria-sort') === 'descending' ? 'desc' : 'asc';
  }

  // Search text (input added by simple-datatables)
  const searchInput = tableEl.closest('.dataTable-wrapper')?.querySelector('.dataTable-input');
  if (searchInput) state.search = searchInput.value;

  // Per-page (selector added by simple-datatables)
  const perPageSel = tableEl.closest('.dataTable-wrapper')?.querySelector('.dataTable-selector');
  if (perPageSel) state.perPage = parseInt(perPageSel.value, 10);

  // Current page (pagination UI)
  const activePage = tableEl.closest('.dataTable-wrapper')?.querySelector('.dataTable-pagination li.active a');
  if (activePage) state.page = parseInt(activePage.textContent.trim(), 10);

  return state;
}

// ---- apply state to a newly-initialized table instance ----
function applyDTState(instance, state) {
  if (!instance || !state) return;

  // Per-page first (affects pagination)
  if (state.perPage && Number.isFinite(state.perPage)) {
    instance.update({ perPage: state.perPage });
  }

  // Reapply sort (if we captured it)
  if (state.sortIndex != null && state.sortDir) {
    try { instance.columns.sort(state.sortIndex, state.sortDir); } catch(e) {}
  }

  // Reapply search (do this after sort so layout settles)
  if (state.search) {
    try { instance.search(state.search); } catch(e) {}
  }

  // Reapply page (if API available). Fallback: click the pagination link.
  if (state.page && Number.isFinite(state.page)) {
    if (typeof instance.page === 'function') {
      try { instance.page(state.page); } catch(e) {}
    } else {
      const link = tableEl.closest('.dataTable-wrapper')?.querySelector(`.dataTable-pagination a[href="#${state.page}"]`);
      if (link) link.click();
    }
  }
}

function refreshEventTable() {
  // capture state before destroying
  const state = readDTState();

  fetch('/event/eventData.php', { cache: 'no-store' })
    .then(r => r.text())
    .then(html => {
      // Tear down to restore the original table DOM
      if (dt) dt.destroy();

      // Replace rows
      const tbody = document.getElementById('eventTableBody');
      if (tbody) tbody.innerHTML = html;

      // Re-init and reapply saved state
      dt = new simpleDatatables.DataTable(tableEl, DT_OPTIONS);
      applyDTState(dt, state);
    })
    .catch(err => console.error("Event fetch error:", err));
}

// ----- your existing helpers kept intact -----
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
      saveEventSetting("showEventAck", ackButton.value);
      document.getElementById("chooseAck").submit();
    });
  }

  // init + periodic updates
  if (tableEl) dt = new simpleDatatables.DataTable(tableEl, DT_OPTIONS);
  refreshEventTable();
  setInterval(refreshEventTable, 45000);
});
</script>










