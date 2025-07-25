<?php
// deviceGraphs.php - Main entry for rendering graph links per storage type

require_once(__DIR__ . '/../../functions/generalFunctions.php');
require_once __DIR__ . "/../../config/api.php";
require_once __DIR__ . "/functions/hostFunctions.php";

$headers = [ 'Authorization: Bearer ' . $_COOKIE['token'] ];
$quitEarly = 0;

$id = $_POST['id'] ?? null;
$hostname = $_POST['hostname'] ?? null;
$activeMonitors = isset($_POST['activeMonitors']) ? json_decode($_POST['activeMonitors'], true) : [];
$specialHostname = $_POST['specialHostname'] ?? null;

//debugger($_POST);

if (!$id || !$hostname || !$specialHostname) {
  $quitEarly = 1;
}

$storage = [];
foreach ($activeMonitors as $monitor) {
  if (!in_array($monitor['storage'], $storage)) {
    $storage[] = $monitor['storage'];
  }
}


// Include RRD logic if applicable
//if (in_array('rrd', $storage)) {
//jj}

/*
  currently canvasJs can parse the results returned from Graphite raw data and display them
  In the future changes to the APIs should allow for this with raw RRD data as well as
  other graphing values types.  I expect influx db, and raw csv and json to be supported
  eventually.
*/

//debugger($storage);
// Include Graphite logic if applicable

if ($quitEarly === 0): ?>

<div class="container">
  <div class="text-center mt-5">
    <h1>Available Graphs for <a href="/host/index.php?page=deviceDetails.php&id=<?= htmlspecialchars($id) ?>">
      <?= htmlspecialchars($hostname) ?></a></h1>
  </div>
  <div class="accordion mt-4" id="graphAccordion">
    <?php if (in_array('rrd', $storage)): ?>
      <?php  include __DIR__ . '/displayComponents/deviceGraphs_rrd.php'; ?>
    <?php endif; ?>

    <?php if (in_array('graphite', $storage)): ?>
      <?php // include __DIR__ . '/displayComponents/deviceGraphs_graphite.php'; ?>
      <?php include __DIR__ . '/displayComponents/deviceGraphs_canvasjs.php'; ?>
    <?php endif; ?>
  </div>
</div>

<?php else:
  loadUnknown("Page failed to load values correctly.  Please go back one page and attempt again.");
endif; ?>
