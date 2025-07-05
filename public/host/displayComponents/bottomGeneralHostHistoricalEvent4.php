<?php
// for your test, mock up the array if needed
// $sharedDevice['historyEvents']['data'] = [...];

$historyEvents = $sharedDevice['historyEvents']['data'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Historical Events</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .sortable:hover { cursor: pointer; text-decoration: underline; }
  </style>
</head>
<body class="p-3">

<div class="card mb-4">
  <div class="card-header">
    Historical Events
  </div>
  <div class="card-body p-0">
    <table class="table table-striped mb-0" id="historyEventsTable">
      <thead class="table-dark">
        <tr>
          <th class="sortable">Event Name</th>
          <th class="sortable">Severity</th>
          <th class="sortable">Start</th>
          <th class="sortable">End</th>
          <th class="sortable">Summary</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($historyEvents as $event): 
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
</div>

<nav>
  <ul class="pagination" id="tablePagination"></ul>
</nav>

<script>
// paginator + sorting
document.addEventListener("DOMContentLoaded", function() {
  const rowsPerPage = 10;
  const table = document.getElementById("historyEventsTable");
  const tbody = table.querySelector("tbody");
  let rows = Array.from(tbody.querySelectorAll("tr"));
  const pageCount = Math.ceil(rows.length / rowsPerPage);

  function showPage(page) {
    rows.forEach((row, i) => {
      row.style.display = (i >= (page-1)*rowsPerPage && i < page*rowsPerPage) ? '' : 'none';
    });
  }

  function renderPagination() {
    const pager = document.getElementById("tablePagination");
    pager.innerHTML = '';
    for(let i=1; i<=pageCount; i++) {
      let li = document.createElement("li");
      li.className = "page-item";
      li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
      li.addEventListener("click", (e) => {
        e.preventDefault();
        showPage(i);
        document.querySelectorAll("#tablePagination .page-item").forEach(el => el.classList.remove("active"));
        li.classList.add("active");
      });
      pager.appendChild(li);
    }
    if (pager.querySelector("li")) pager.querySelector("li").classList.add("active");
  }

  // sorting
  const headers = table.querySelectorAll("thead th");
  headers.forEach((th, i) => {
    th.addEventListener("click", function() {
      const direction = th.dataset.direction === "asc" ? "desc" : "asc";
      th.dataset.direction = direction;
      rows.sort((a, b) => {
        const tdA = a.children[i].dataset.value || a.children[i].textContent.trim();
        const tdB = b.children[i].dataset.value || b.children[i].textContent.trim();
        return direction === "asc"
          ? tdA.localeCompare(tdB, undefined, {numeric:true})
          : tdB.localeCompare(tdA, undefined, {numeric:true});
      });
      rows.forEach(row => tbody.appendChild(row));
      showPage(1);
      document.querySelectorAll("#tablePagination .page-item").forEach(el => el.classList.remove("active"));
      if (document.querySelector("#tablePagination .page-item")) document.querySelector("#tablePagination .page-item").classList.add("active");
    });
  });

  renderPagination();
  showPage(1);
});
</script>

</body>
</html>
