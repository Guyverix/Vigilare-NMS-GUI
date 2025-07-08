<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Event History with Modal Details</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    th.sortable {
      cursor: pointer;
    }
  </style>
</head>
<body class="container py-4">

<h2>Event History</h2>

<table id="historyTable" class="table table-bordered">
  <thead>
    <tr>
      <th class="sortable">Time</th>
      <th class="sortable">Severity</th>
      <th class="sortable">Summary</th>
    </tr>
  </thead>
  <tbody>
    <tr class="clickable-row" data-detail='{"hostname":"server1","usage":"95%","filesystem":"/dev/sda1"}'>
      <td>2025-07-01 10:00</td>
      <td>3</td>
      <td>Disk usage high</td>
    </tr>
    <tr class="clickable-row" data-detail='{"service":"NRPE","ip":"192.168.15.10","timeout":"5s"}'>
      <td>2025-07-02 11:30</td>
      <td>2</td>
      <td>Service unreachable</td>
    </tr>
  </tbody>
</table>

<ul id="pagination-historyTable" class="pagination justify-content-center"></ul>

<!-- Modal for Details -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Event Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="detailModalBody">
        <!-- Filled by JS -->
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function enableTableEnhancements(tableId, rowsPerPage = 5) {
  const table = document.getElementById(tableId);
  const tbody = table.querySelector("tbody");
  const headers = table.querySelectorAll("th.sortable");
  const pagination = document.getElementById(`pagination-${tableId}`);
  let currentPage = 1;

  let rows = Array.from(tbody.querySelectorAll("tr"));

  function updateTable() {
    tbody.innerHTML = "";
    const start = (currentPage - 1) * rowsPerPage;
    const paginated = rows.slice(start, start + rowsPerPage);
    paginated.forEach(row => tbody.appendChild(row));
    setupClicks();
    setupPagination();
  }

  function setupClicks() {
    const modal = new bootstrap.Modal(document.getElementById("detailModal"));
    const modalBody = document.getElementById("detailModalBody");

    table.querySelectorAll(".clickable-row").forEach(row => {
      row.addEventListener("click", () => {
        const raw = row.getAttribute("data-detail");
        let content = "";

        try {
          const parsed = JSON.parse(raw);
          content = "<ul class='list-group'>";
          for (const [k, v] of Object.entries(parsed)) {
            content += `<li class="list-group-item"><strong>${k}</strong>: ${v}</li>`;
          }
          content += "</ul>";
        } catch {
          content = `<pre>${raw}</pre>`;
        }

        modalBody.innerHTML = content;
        modal.show();
      });
    });
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
      a.href = "#";
      a.addEventListener("click", (e) => {
        e.preventDefault();
        currentPage = i;
        updateTable();
      });
      li.appendChild(a);
      pagination.appendChild(li);
    }
  }

  headers.forEach((header, index) => {
    header.addEventListener("click", () => {
      const asc = !header.classList.contains("asc");
      headers.forEach(h => h.classList.remove("asc", "desc"));
      header.classList.add(asc ? "asc" : "desc");

      rows.sort((a, b) => {
        const aText = a.children[index].textContent.trim();
        const bText = b.children[index].textContent.trim();
        return asc ? aText.localeCompare(bText) : bText.localeCompare(aText);
      });

      updateTable();
    });
  });

  updateTable();
}

document.addEventListener("DOMContentLoaded", () => {
  enableTableEnhancements("historyTable", 5);
});
</script>
</body>
</html>
