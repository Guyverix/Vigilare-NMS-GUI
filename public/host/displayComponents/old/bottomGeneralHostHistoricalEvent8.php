<!-- Load the shared table functionality -->
<script src="/js/tableSort/tableSort.js"></script>

<?php debugger($sharedDevice['historyEvents']); ?>


<div class="card mb-3">
  <div class="card-header bg-secondary text-white">
    Historical Events
  </div>
  <div class="card-body p-2">
    <table class="table table-sm table-hover text-center" id="historyTable2">
      <thead class="table-light">
        <tr>
          <th class="sortable">Summary</th>
          <th class="sortable">Severity</th>
          <th class="sortable">State Change</th>
          <th class="sortable">End</th>
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
      <ul class="pagination justify-content-center" id="pagination-historyTable2"></ul>
    </nav>
  </div>
</div>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    enableTableEnhancements("historyTable2", 20);
  });
</script>
