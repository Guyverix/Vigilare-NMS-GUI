<div class="card border-secondary rounded-3">
  <div class="card-header bg-secondary text-white">
    Host Monitors
  </div>
  <div class="card-body p-0">
    <?php if (!empty($sharedDevice['monitors']['data']) && is_array($sharedDevice['monitors']['data'])): ?>
      <ul class="list-group list-group-flush">
        <?php foreach ($sharedDevice['monitors']['data'] as $monitor): ?>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <?= htmlspecialchars($monitor['checkName']) ?>
            <span class="text-muted small">
              <?= htmlspecialchars($monitor['type']) ?> | <?= htmlspecialchars($monitor['storage']) ?>
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
