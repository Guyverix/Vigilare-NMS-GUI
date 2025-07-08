<!-- Load shared table sort + pagination logic -->
<script src="/js/tableSort/tableSort.js"></script>

<div class="card mt-4">
  <div class="card-header bg-primary text-white">
    Active Events
  </div>
  <div class="card-body p-2">
    <div class="table-responsive">
      <table id="activeTable" class="table table-sm table-bordered table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>Severity</th>
            <th>Summary</th>
            <th>Start</th>
            <th>Receiver</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($sharedDevice['activeEvents']['data'] as $event): ?>
            <?php
              $sev = intval($event['eventSeverity']);
              $badgeClass = match ($sev) {
                5 => 'bg-danger text-white',
                4 => 'bg-orange text-white', // Custom
                3 => 'bg-warning text-dark',
                2 => 'bg-info text-dark',
                1 => 'bg-success text-white',
                default => 'bg-secondary text-white'
              };
            ?>
            <tr class="<?= $badgeClass ?>">
              <td>
                <span class="badge <?= $badgeClass ?>">Severity <?= $sev ?></span>
              </td>
              <td><?= htmlspecialchars($event['eventSummary']) ?></td>
              <td><?= htmlspecialchars($event['startEvent']) ?></td>
              <td><?= htmlspecialchars($event['eventReceiver']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <!-- Pagination will be added dynamically -->
      <div id="activeTablePagination" class="mt-2 text-center"></div>
    </div>
  </div>
</div>

<!-- Initialize this table -->
<script>
  document.addEventListener("DOMContentLoaded", function () {
    enableTableEnhancements("activeTable", 5); // 5 rows per page
  });
</script>
