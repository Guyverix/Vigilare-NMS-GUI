<?php
/*
  Find the 'portsUsed' entry
  This is also a good example of changing the bootstrap css values
  based on the data.  Keep this as a decent reminder on how
  to do this simply
*/

$portsUsedData = null;

// Match our checkName and build an array based on the data returned
for ($i = 0; $i < count($sharedDevice['performance']['data']); $i++) {
    if (!empty($sharedDevice['performance']['data'][$i]['checkName']) &&
        strtolower($sharedDevice['performance']['data'][$i]['checkName']) === 'portsused') {
        $rawValue = $sharedDevice['performance']['data'][$i]['value'] ?? '';
        $portsUsedData = json_decode($rawValue, true);
        break;
    }
}
?>

<?php if (!empty($portsUsedData) && is_array($portsUsedData)): ?>
  <div class="accordion mb-3" id="accordionPortsUsed">
    <div class="accordion-item">
      <h2 class="accordion-header" id="headingPortsUsed">
        <button class="accordion-button bg-primary text-white collapsed" type="button"
                data-bs-toggle="collapse" data-bs-target="#collapsePortsUsed"
                aria-expanded="false" aria-controls="collapsePortsUsed">
          Ports Used
        </button>
      </h2>
      <div id="collapsePortsUsed" class="accordion-collapse collapse"
           aria-labelledby="headingPortsUsed" data-bs-parent="#accordionPortsUsed">
        <div class="accordion-body p-0">
          <ul class="list-group list-group-flush">
            <?php foreach ($portsUsedData as $entry): 
              $port = (int)($entry['port'] ?? 0);
              $isLowPort = $port <= 1024;
              $highlightClass = $isLowPort ? ' bg-success' : '';
              $highlightClassText = $isLowPort ? ' text-white ' : '';
              $highlightClassMuted = $isLowPort ? ' text-white ' : 'text-muted';
            ?>
              <li class="list-group-item<?= $highlightClass ?> <?= $highlightClassText ?>">
                <div class="d-flex justify-content-between">
                  <div>
                    <span class="<?= $highlightClassMuted ?>">Address:</span>
                    <span class="fw-semibold"><?= htmlspecialchars($entry['address'] ?? 'N/A') ?></span>
                  </div>
                  <div>
                    <span class="<?= $highlightClassMuted ?>">Port:</span>
                    <span class="fw-semibold"><?= $port ?></span>
                  </div>
                </div>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>
