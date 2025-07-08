<?php
// Sample data for demonstration
$historyEvents = [
  [
    'time' => '2025-07-01 10:00',
    'severity' => 3,
    'summary' => 'Disk usage high',
    'details' => [
      'host' => 'server1',
      'check' => 'disk-space',
      'threshold' => '90%',
      'actual' => '95%',
      'message' => 'SNMP timeout on .1.3.6.1...'
    ]
  ],
  [
    'time' => '2025-07-02 11:30',
    'severity' => 2,
    'summary' => 'Service unreachable',
    'details' => [
      'host' => 'server2',
      'service' => 'NRPE',
      'error' => 'Connection refused',
      'message' => 'NRPE failure detected'
    ]
  ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>History Table</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
  <table id="historyTable" class="table table-bordered">
    <thead>
      <tr>
        <th class="sortable">Time</th>
        <th class="sortable">Severity</th>
        <th class="sortable">Summary</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($historyEvents as $index => $event): ?>
        <tr class="clickable-row" data-index="<?= $index ?>" data-detail='<?= htmlspecialchars(json_encode($event["details"]), ENT_QUOTES, "UTF-8") ?>'>
          <td><?= htmlspecialchars($event["time"]) ?></td>
          <td><?= htmlspecialchars($event["severity"]) ?></td>
          <td><?= htmlspecialchars($event["summary"]) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <ul id="pagination-historyTable" class="pagination justify-content-center"></ul>
</div>

<!-- Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailModalLabel">Event Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="detailModalContent" class="container"></div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function enableTableEnhancements(tableId, rowsPerPage = 10) {
  const table = document.getElementById(tableId);
  const tbody = table.querySelector("tbody");
  const headers = table.querySelectorAll("th.sortable");
  const pagination = document.getElementById(`pagination-${tableId}`);
  let currentPage = 1;
  let rows = Array.from(tbody.querySelectorAll("tr.clickable-row"));

  // Sorting
  headers.forEach((header, columnIndex) => {
    header.style.cursor = "pointer";
    header.addEventListener("click", () => {
      const ascending = !header.classList.contains("asc");
      headers.forEach(h => h.classList.remove("asc", "desc"));
      header.classList.add(ascending ? "asc" : "desc");
      rows.sort((a, b) => {
        const aText = a.children[columnIndex].textContent.trim();
        const bText = b.children[columnIndex].textContent.trim();
        return ascending
          ? aText.localeCompare(bText)
          : bText.localeCompare(aText);
      });
      updateTable();
    });
  });

  function updateTable() {
    tbody.innerHTML = "";
    const start = (currentPage - 1) * rowsPerPage;
    const paginated = rows.slice(start, start + rowsPerPage);
    paginated.forEach(row => tbody.appendChild(row));
    setupRowClicks();
    setupPagination();
  }

  function setupPagination() {
    if (!pagination) return;
    pagination.innerHTML = "";
    const pageCount = Math.ceil(rows.length / rowsPerPage);
    for (let i = 1; i <= pageCount; i++) {
      const li = document.createElement("li");
      li.className = "page-item" + (i === currentPage ? " active" : "");
      const a = document.createElement("a");
      a.className = "page-link";
      a.textContent = i;
      a.addEventListener("click", () => {
        currentPage = i;
        updateTable();
      });
      li.appendChild(a);
      pagination.appendChild(li);
    }
  }

  function setupRowClicks() {
    const rows = table.querySelectorAll("tr.clickable-row");
    rows.forEach(row => {
      row.addEventListener("click", () => {
        const data = row.getAttribute("data-detail");
        if (!data) return;
        const details = JSON.parse(data);
        const contentDiv = document.getElementById("detailModalContent");
        contentDiv.innerHTML = "";

        const dl = document.createElement("dl");
        for (const [key, value] of Object.entries(details)) {
          const dt = document.createElement("dt");
          dt.textContent = key;
          const dd = document.createElement("dd");
          dd.textContent = value;
          dl.appendChild(dt);
          dl.appendChild(dd);
        }
        contentDiv.appendChild(dl);

        const modal = new bootstrap.Modal(document.getElementById("detailModal"));
        modal.show();
      });
    });
  }

  updateTable();
}

document.addEventListener("DOMContentLoaded", function () {
  enableTableEnhancements("historyTable", 5);
});
</script>
</body>
</html>
