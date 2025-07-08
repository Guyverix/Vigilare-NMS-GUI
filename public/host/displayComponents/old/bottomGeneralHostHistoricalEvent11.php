<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Event Table with Modal Details</title>
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
      <tr class="clickable-row" data-index="0">
        <td>2025-07-01 10:00</td>
        <td>3</td>
        <td>Disk usage high</td>
      </tr>
      <tr id="detail-0" class="detail-row" style="display:none;">
        <td colspan="3">
          {"hostname": "server1", "filesystem": "/dev/sda1", "usage": "95%", "threshold": "90%"}
        </td>
      </tr>
      <tr class="clickable-row" data-index="1">
        <td>2025-07-02 11:30</td>
        <td>2</td>
        <td>Service unreachable</td>
      </tr>
      <tr id="detail-1" class="detail-row" style="display:none;">
        <td colspan="3">
          {"service": "NRPE", "ip": "192.168.15.10", "timeout": "5s"}
        </td>
      </tr>
    </tbody>
  </table>

  <ul id="pagination-historyTable" class="pagination justify-content-center"></ul>

  <!-- Modal -->
  <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="detailModalLabel">Event Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="detailModalBody">
          <!-- Dynamic content -->
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
      let rows = Array.from(tbody.querySelectorAll("tr.clickable-row"));

      headers.forEach((header, columnIndex) => {
        header.addEventListener("click", () => {
          const asc = !header.classList.contains("asc");
          headers.forEach(h => h.classList.remove("asc", "desc"));
          header.classList.add(asc ? "asc" : "desc");
          rows.sort((a, b) => {
            const aText = a.children[columnIndex].textContent.trim();
            const bText = b.children[columnIndex].textContent.trim();
            return asc ? aText.localeCompare(bText) : bText.localeCompare(aText);
          });
          updateTable();
        });
      });

      function updateTable() {
        tbody.innerHTML = "";
        const start = (currentPage - 1) * rowsPerPage;
        const paginated = rows.slice(start, start + rowsPerPage);
        paginated.forEach(row => {
          tbody.appendChild(row);
          const idx = row.dataset.index;
          const detail = document.getElementById(`detail-${idx}`);
          if (detail) tbody.appendChild(detail);
        });
        setupRowClicks();
        setupPagination();
      }

      function setupRowClicks() {
        const clickableRows = document.querySelectorAll(".clickable-row");
        clickableRows.forEach(row => {
          row.addEventListener("click", () => {
            const idx = row.getAttribute("data-index");
            const detailRow = document.getElementById(`detail-${idx}`);
            const modal = new bootstrap.Modal(document.getElementById("detailModal"));
            const modalBody = document.getElementById("detailModalBody");
            if (!detailRow || !modalBody) return;

            const raw = detailRow.querySelector("td").textContent.trim();
            let content = "";
            try {
              const parsed = JSON.parse(raw);
              if (typeof parsed === "object" && parsed !== null) {
                content = "<ul class='list-group'>";
                for (const [key, value] of Object.entries(parsed)) {
                  content += `<li class="list-group-item"><strong>${key}</strong>: ${Array.isArray(value) ? value.join(", ") : value}</li>`;
                }
                content += "</ul>";
              } else {
                content = raw;
              }
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
          a.addEventListener("click", () => {
            currentPage = i;
            updateTable();
          });
          li.appendChild(a);
          pagination.appendChild(li);
        }
      }

      updateTable();
    }

    document.addEventListener("DOMContentLoaded", function () {
      enableTableEnhancements("historyTable", 5);
    });
  </script>

</body>
</html>
