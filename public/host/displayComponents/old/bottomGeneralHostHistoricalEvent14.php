<!-- Load the shared table functionality -->
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

<script src="/js/bootstrap-5/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>


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

<script src="/js/tableSort/tableSort.js"></script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    enableTableEnhancements("historyTable", 5);
    setupModalClickHandlers(tableId);
  });
</script>
