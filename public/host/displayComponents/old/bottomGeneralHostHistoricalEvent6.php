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
    const table = document.getElementById("historyTable").getElementsByTagName("tbody")[0];
    const rows = table.getElementsByTagName("tr");
    const rowsPerPage = 10;
    const pageCount = Math.ceil(rows.length / rowsPerPage);
    const pagination = document.getElementById("pagination");

    function showPage(page) {
        for (let i = 0; i < rows.length; i++) {
            rows[i].style.display = (Math.floor(i / rowsPerPage) + 1 === page) ? "" : "none";
        }
    }

    // build paginator
    for (let i = 1; i <= pageCount; i++) {
        let li = document.createElement("li");
        li.className = "page-item";
        li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
        li.addEventListener("click", function (e) {
            e.preventDefault();
            document.querySelectorAll(".page-item").forEach(el => el.classList.remove("active"));
            li.classList.add("active");
            showPage(i);
        });
        pagination.appendChild(li);
    }

    // highlight first
    if (pagination.firstChild) pagination.firstChild.classList.add("active");
    showPage(1);
});
</script>
