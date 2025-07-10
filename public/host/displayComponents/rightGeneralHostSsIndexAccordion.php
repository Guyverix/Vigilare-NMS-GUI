<?php
// Find the first entry with checkName containing 'ssIndex'
$ssIndexData = null;

// Loop through zero-indexed array
for ($i = 0; $i < count($sharedDevice['performance']['data']); $i++) {
    $checkName = $sharedDevice['performance']['data'][$i]['checkName'] ?? '';
    if (stripos($checkName, 'ssindex') !== false) {
        $rawValue = $sharedDevice['performance']['data'][$i]['value'] ?? '{}';
        $ssIndexData = json_decode($rawValue, true);
        $ssIndexLabel = $checkName;
        break;
    }
}
$ssIndexLabel = 'Server Statistics';
?>

<?php if (!empty($ssIndexData) && is_array($ssIndexData)): ?>
  <div class="accordion mb-3" id="accordionSsIndex">
    <div class="accordion-item">
      <h2 class="accordion-header" id="headingSsIndex">
        <button class="accordion-button bg-primary text-white collapsed" type="button"
                data-bs-toggle="collapse" data-bs-target="#collapseSsIndex"
                aria-expanded="false" aria-controls="collapseSsIndex">
          <?= htmlspecialchars($ssIndexLabel ?: 'Interface States') ?>
        </button>
      </h2>
      <div id="collapseSsIndex" class="accordion-collapse collapse"
           aria-labelledby="headingSsIndex" data-bs-parent="#accordionSsIndex">
        <div class="accordion-body p-0">
          <ul class="list-group list-group-flush">
            <?php foreach ($ssIndexData as $interface => $status): ?>
              <li class="list-group-item">
                <div class="d-flex justify-content-between">
                  <div>
                    <span class="text-muted">Metric:</span>
                    <span class="fw-semibold"><?= htmlspecialchars($interface) ?></span>
                  </div>
                  <div>
                    <span class="text-muted">Value:</span>
                    <span class="fw-semibold"><?= htmlspecialchars($status) ?></span>
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
