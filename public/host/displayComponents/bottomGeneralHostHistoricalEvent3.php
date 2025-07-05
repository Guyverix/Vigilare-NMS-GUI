<?php
$historyEvents = $sharedDevice['historyEvents']['data'] ?? [];
?>
<div class="card border-secondary rounded-3 mt-3">
  <div class="card-header bg-secondary text-white">
    Historical Events
  </div>
  <div class="card-body p-0">
    <?php if (!empty($historyEvents)): ?>
      <div class="table-responsive">
        <table class="table table-striped table-hover mb-0" id="historyEventsTable">
          <thead class="table-dark">
            <tr>
              <th scope="col" data-sort="eventName">Name</th>
              <th scope="col" data-sort="eventSeverity">Severity</th>
              <th scope="col" data-sort="startEvent">Start</th>
              <th scope="col" data-sort="endEvent">End</th>
              <th scope="col">Summary</th>
            </tr>
          </thead>
          <tbody>
           <?php
            foreach ($historyEvents as $event):
                $sev = (int)$event['eventSeverity'];
                switch ($sev) {
                    case 0: $rowClass = 'table-success'; break;
                    case 1: $rowClass = 'table-secondary'; break;
                    case 2: $rowClass = 'table-info'; break;
                    case 3: $rowClass = 'table-warning'; break;
                    case 4: $rowClass = 'table-danger'; break;
                    case 5: $rowClass = 'bg-danger text-white'; break;
                    default: $rowClass = 'table-light'; break;
                }
                $summary = htmlspecialchars($event['eventSummary'] ?? '');
                if (strlen($summary) > 80) {
                    $summary = substr($summary, 0, 77) . '...';
                }
            ?>
            <tr class="<?= $rowClass ?>">
            <td><?= htmlspecialchars($event['eventName']) ?></td>
            <td data-value="<?= $sev ?>"><?= $sev ?></td>
            <td><?= htmlspecialchars($event['startEvent']) ?></td>
            <td><?= htmlspecialchars($event['endEvent']) ?></td>
            <td><?= $summary ?></td>
          </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <div class="p-3 text-muted">
        No historical events found for this host.
      </div>
    <?php endif; ?>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
  const table = document.getElementById("historyEventsTable");
  const headers = table.querySelectorAll("th[data-sort]");
  
  headers.forEach(header => {
    header.style.cursor = "pointer";
    header.addEventListener("click", function() {
      const sortKey = header.dataset.sort;
      const tbody = table.querySelector("tbody");
      const rows = Array.from(tbody.querySelectorAll("tr"));
      const index = Array.from(header.parentNode.children).indexOf(header);
      
      // toggle sort direction
      header.dataset.direction = header.dataset.direction === "asc" ? "desc" : "asc";
      const direction = header.dataset.direction;

      rows.sort((a, b) => {
        let valA = a.children[index].dataset.value || a.children[index].innerText;
        let valB = b.children[index].dataset.value || b.children[index].innerText;

        if (sortKey === "eventSeverity") {
          valA = parseInt(valA, 10);
          valB = parseInt(valB, 10);
        } else if (sortKey.includes("Event")) {
          valA = Date.parse(valA);
          valB = Date.parse(valB);
        }

        if (valA < valB) return direction === "asc" ? -1 : 1;
        if (valA > valB) return direction === "asc" ? 1 : -1;
        return 0;
      });

      // rebuild tbody
      rows.forEach(row => tbody.appendChild(row));
    });
  });
});
</script>
