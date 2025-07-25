<div class="accordion mb-3" id="accordionHostMonitors">
  <div class="accordion-item border-secondary rounded-3">
    <h2 class="accordion-header" id="headingHostMonitors">
      <button class="accordion-button bg-primary text-white collapsed" type="button"
              data-bs-toggle="collapse" data-bs-target="#collapseHostMonitors"
              aria-expanded="false" aria-controls="collapseHostMonitors">
        Current Monitors
      </button>
    </h2>
    <div id="collapseHostMonitors" class="accordion-collapse collapse"
         aria-labelledby="headingHostMonitors" data-bs-parent="#accordionHostMonitors">
      <div class="accordion-body p-0">
        <?php if (!empty($sharedDevice['monitors']['data']) && is_array($sharedDevice['monitors']['data'])): ?>
          <ul class="list-group list-group-flush">
            <?php foreach ($sharedDevice['monitors']['data'] as $monitor): ?>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <span>
                  <i class="fas fa-eye me-1 text-primary"></i>
                  <?= htmlspecialchars($monitor['checkName']) ?>
                </span>
                <span>
                  <span class="badge
                    <?php
                      switch (strtolower($monitor['type'])) {
                        case 'nrpe': echo 'bg-success'; break;
                        case 'alive': echo 'bg-success text-dark'; break;
                        case 'ping': echo 'bg-success text-dark'; break;
                        case 'get': echo 'bg-warning text-dark'; break;
                        case 'walk': echo 'bg-warning text-dark'; break;
                        case 'snmpget': echo 'bg-warning text-dark'; break;
                        case 'snmp': echo 'bg-warning text-dark'; break;
                        case 'shell': echo 'bg-secondary'; break;
                        default: echo 'bg-secondary'; break;
                      }
                    ?>
                  ">
                    <?= htmlspecialchars(strtoupper($monitor['type'] ?? 'UNKNOWN')) ?>
                  </span>
                </span>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php else: ?>
          <div class="p-3 text-muted">
            No monitors defined for this host.
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
