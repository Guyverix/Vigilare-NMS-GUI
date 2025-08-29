<?php
// about.php
$releaseVersion = "v0.0.1";
$lastUpdated = date("F j, Y, g:i a", filemtime(__FILE__));

// Minimal, easy-to-extend changelog data
$changelog = [
  [
    'version' => 'v0.0.1',
    'date'    => '2025-08-28',
    'notes'   => [
      'Initial preview release.',
      'Added About page with version badge and last-updated footer.',
      'Scaffolding for devices, events, and graphs pages in progress.'
    ]
  ],
  // Add future entries here:
  // ['version' => 'v0.0.2', 'date' => 'YYYY-MM-DD', 'notes' => ['Change 1', 'Change 2']]
];
?>

<?php
  if ( file_exists (__DIR__ . '/includes/head.html')) {
    readfile(__DIR__ . '/includes/head.html');
  }
  else {
    if (isset($title)) {
      includeHead($title);  // calls head.php from the generalFunctions.php script
    }
    else {
      readfile(__DIR__ . '/shared/head.html');  // generic with generic title
    }
  }
?>
  <!-- looking at a refactor as these look so damn nice -->
  <link id="bootstrap-css" rel="stylesheet" href="/css/bootstrap/bootstrap.min.css">
  <link id="theme-css" rel="stylesheet" href="/css/light/vigilare-dashboard.css">

<div class="container mt-4">
  <div class="row justify-content-center">
    <div class="col-lg-8">

      <div class="card shadow-lg border-0">
        <div class="card-header bg-primary">
          <h4 class="mb-0">About Vigilare</h4>
        </div>

        <div class="card-body">
          <p class="lead">
            Vigilare is an open-source Network Monitoring System (NMS) designed to provide
            visibility, alerting, and performance tracking for devices and services
            across diverse environments.
          </p>
          <p>
            The system is built to be modular, fault-tolerant, and user-friendly —
            combining flexible data collection with modern visualization.
            Current functionality includes device monitoring, event management,
            graphing, and historical tracking.
          </p>

          <hr>

          <h5>Project Information</h5>
          <ul class="list-group mb-4">
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <span>Release Version</span>
              <span class="badge bg-success"><?php echo $releaseVersion; ?></span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <span>License</span>
              <span class="badge bg-secondary">MIT</span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <span>Source Code</span>
              <a href="https://github.com/guyverix/Vigilare-NMS-GUI" target="_blank" class="text-decoration-none">GitHub Repository GUI</a>
              <a href="https://github.com/guyverix/Vigilare-NMS-API" target="_blank" class="text-decoration-none">GitHub Repository API</a>
            </li>
          </ul>

          <h5 class="mb-3">Changelog</h5>
          <div class="accordion" id="changelogAccordion">
            <?php foreach ($changelog as $i => $entry): 
              $headingId = "chg-heading-" . $i;
              $collapseId = "chg-collapse-" . $i;
              $isFirst = ($i === 0);
            ?>
            <div class="accordion-item">
              <h2 class="accordion-header" id="<?php echo $headingId; ?>">
                <button class="accordion-button <?php echo $isFirst ? '' : 'collapsed'; ?>" type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#<?php echo $collapseId; ?>"
                        aria-expanded="<?php echo $isFirst ? 'true' : 'false'; ?>"
                        aria-controls="<?php echo $collapseId; ?>">
                  <span class="me-2 badge bg-success"><?php echo htmlspecialchars($entry['version']); ?></span>
                  <span class="me-2">—</span>
                  <span class="text-muted small"><?php echo htmlspecialchars($entry['date']); ?></span>
                </button>
              </h2>
              <div id="<?php echo $collapseId; ?>" class="accordion-collapse collapse <?php echo $isFirst ? 'show' : ''; ?>"
                   aria-labelledby="<?php echo $headingId; ?>" data-bs-parent="#changelogAccordion">
                <div class="accordion-body">
                  <?php if (!empty($entry['notes'])): ?>
                    <ul class="mb-0">
                      <?php foreach ($entry['notes'] as $note): ?>
                        <li><?php echo htmlspecialchars($note); ?></li>
                      <?php endforeach; ?>
                    </ul>
                  <?php else: ?>
                    <p class="mb-0 text-muted">No notes for this release.</p>
                  <?php endif; ?>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>

          <p class="text-muted mt-4 mb-0">
            Vigilare is in early development. Features and functionality are subject
            to change as the project evolves.
          </p>
        </div>

        <div class="card-footer text-muted text-end small">
          Last updated: <?php echo $lastUpdated; ?>
        </div>
      </div>

    </div>
  </div>
</div>

<?php
  if ( file_exists( __DIR__ . ("/includes/bottomFooter.php"))) {
    include __DIR__ . ('/includes/bottomFooter.php');
  }
  else {
    include __DIR__ . ('/shared/bottomFooter.php');
  }
?>
