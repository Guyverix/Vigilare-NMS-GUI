<div class="card mb-3">
  <div class="card-header bg-secondary text-white">
    Historical Events
  </div>
  <div class="card-body p-2">
    <table class="table table-sm table-hover text-center" id="historyTable">
      <thead class="table-light">
        <tr>
          <th>Summary</th>
          <th>Severity</th>
          <th>State Change</th>
          <th>End</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $history = $sharedDevice['historyEvents']['data'];
        foreach ($history as $event) {
            $sev = (int)$event['eventSeverity'];
            switch ($sev) {
                case 0: $badge = 'bg-success'; $sevText = 'OK'; break;
                case 1: $badge = 'bg-secondary'; $sevText = 'Debug'; break;
                case 2: $badge = 'bg-info'; $sevText = 'Info'; break;
                case 3: $badge = 'bg-warning'; $sevText = 'Warning'; break;
                case 4: $badge = 'bg-orange'; $sevText = 'Major'; break;
                case 5: $badge = 'bg-danger'; $sevText = 'Critical'; break;
                default: $badge = 'bg-dark'; $sevText = 'Unknown'; break;
            }
            echo "<tr class='{$badge} text-white'>
                    <td>{$event['eventSummary']}</td>
                    <td>{$sevText}</td>
                    <td>{$event['stateChange']}</td>
                    <td>{$event['endEvent']}</td>
                  </tr>";
        }
        ?>
      </tbody>
    </table>

    <!-- pagination controls -->
    <nav>
      <ul class="pagination justify-content-center" id="pagination"></ul>
    </nav>
  </div>
</div>


<script>
document.addEventListener("DOMContentLoaded", function () {
    const table = document.getElementById("historyTable");
    const tbody = table.querySelector("tbody");
    const rows = Array.from(tbody.rows);
    const pagination = document.getElementById("pagination");
    const rowsPerPage = 5;
    let currentPage = 1;

    function paginate(page) {
        const start = (page - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        rows.forEach((row, i) => {
            row.style.display = (i >= start && i < end) ? "" : "none";
        });

        const pageItems = pagination.querySelectorAll("li");
        pageItems.forEach(li => li.classList.remove("active"));
        if (pageItems[page - 1]) pageItems[page - 1].classList.add("active");
        currentPage = page;
    }

    function buildPagination() {
        pagination.innerHTML = "";
        const pageCount = Math.ceil(rows.length / rowsPerPage);
        for (let i = 1; i <= pageCount; i++) {
            const li = document.createElement("li");
            li.className = "page-item";
            li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
            li.addEventListener("click", (e) => {
                e.preventDefault();
                paginate(i);
            });
            pagination.appendChild(li);
        }
    }

    function sortTable(column, order) {
        const sorted = rows.sort((a, b) => {
            const valA = a.cells[column].textContent.trim();
            const valB = b.cells[column].textContent.trim();

            const numA = Date.parse(valA) || valA;
            const numB = Date.parse(valB) || valB;

            if (numA < numB) return order === "asc" ? -1 : 1;
            if (numA > numB) return order === "asc" ? 1 : -1;
            return 0;
        });

        sorted.forEach(row => tbody.appendChild(row));
        paginate(1);
    }

    // Sorting headers
    document.querySelectorAll("#historyTable th.sortable").forEach(th => {
        th.style.cursor = "pointer";
        th.addEventListener("click", () => {
            const col = parseInt(th.dataset.column);
            const currentOrder = th.dataset.order;
            const newOrder = currentOrder === "asc" ? "desc" : "asc";
            th.dataset.order = newOrder;
            sortTable(col, newOrder);
        });
    });

    buildPagination();
    paginate(1);
});
</script>
