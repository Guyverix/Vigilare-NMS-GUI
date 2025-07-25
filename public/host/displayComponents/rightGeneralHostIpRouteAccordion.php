<?php
// Look for the 'ipRoute' entry
$ipRouteData = null;


// debugger($sharedDevice['performance']['data']);
for ($i = 0; $i < count($sharedDevice['performance']['data']); $i++) {
    if (!empty($sharedDevice['performance']['data'][$i]['checkName']) &&
        strtolower($sharedDevice['performance']['data'][$i]['checkName']) === 'iproute') {

        $rawValue = $sharedDevice['performance']['data'][$i]['value'] ?? '{}';
        $ipRouteData = json_decode($rawValue, true);
        break;
    }
}

// debugger($ipRouteData);
?>

<?php if (!empty($ipRouteData) && is_array($ipRouteData)): ?>
  <div class="accordion mb-3" id="accordionIpRoutes">
    <div class="accordion-item">
      <h2 class="accordion-header" id="headingIpRoutes">
        <button class="accordion-button bg-primary text-white collapsed" type="button"
                data-bs-toggle="collapse" data-bs-target="#collapseIpRoutes"
                aria-expanded="false" aria-controls="collapseIpRoutes">
          IP Routes
        </button>
      </h2>
      <div id="collapseIpRoutes" class="accordion-collapse collapse"
           aria-labelledby="headingIpRoutes" data-bs-parent="#accordionIpRoutes">
        <div class="accordion-body p-0">
          <ul class="list-group list-group-flush">
            <?php foreach ($ipRouteData as $destination => $route): ?>
              <li class="list-group-item">
                <div class="fw-bold mb-1"><?= htmlspecialchars($destination) ?></div>
                <div class="row small">
                  <div class="col-md-4"><span class="text-muted">Next Hop:</span> <?= htmlspecialchars($route['routeNextHop'] ?? 'N/A') ?></div>
                  <div class="col-md-2"><span class="text-muted">Metric:</span> <?= htmlspecialchars($route['routeMetric'] ?? 'N/A') ?></div>
                  <div class="col-md-2"><span class="text-muted">Netmask:</span> <?= htmlspecialchars($route['routeNetmask'] ?? 'N/A') ?></div>
                  <div class="col-md-2"><span class="text-muted">Type:</span> <?= htmlspecialchars($route['routeType'] ?? 'N/A') ?></div>
                  <div class="col-md-2"><span class="text-muted">Proto:</span> <?= htmlspecialchars($route['routeProto'] ?? 'N/A') ?></div>
                </div>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>
