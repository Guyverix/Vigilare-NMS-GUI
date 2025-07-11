<?php
  /*
    This tabbed definition is clunky, but a decent start.
    It should be easy enough to add or remove to it
    without clobbering other stuff.

  */
      // debugger($sharedDevice);

      echo "<center>"; // Donno why css is not working correctly on this
      // Only ropterties needs to talk to the API at this point
      echo '<form id="findProperties" action="" method="POST"><input type="hidden" name="id" value="' . $id . '"></form>' . "\n";

      // Add additional hidden inputs with the data we have already pulled.  Dont call the API unless needed for something else
      echo '<form id="hostProperties" method="POST" action="/host/index.php?&page=hostPropertiesEdit.php">' . "\n";
        echo '<input type="hidden" name="id" value="' . $id . '">' . "\n";
        echo '<input type="hidden" name="hostname" value="' . $sharedDevice['properties']['data'][0]['hostname'] . '">' . "\n";
        echo '<input type="hidden" name="deviceProperties" value="' . htmlspecialchars($sharedDevice['properties']['data'][0]['properties']) . '">' . "\n";
      echo '</form>' . "\n";

      echo '<form id="hostModify"     action="/host/index.php?&page=modifyDevice.php"          method="POST">' . "\n";
        echo '<input type="hidden" name="id" value="' . $id . '">' . "\n";
        echo '<input type="hidden" name="hostname" value="' . $sharedDevice['properties']['data'][0]['hostname'] . '">' . "\n";
        echo '<input type="hidden" name="address" value="' . $sharedDevice['properties']['data'][0]['address'] . '">' . "\n";
        echo '<input type="hidden" name="firstSeen" value="' . $sharedDevice['properties']['data'][0]['firstSeen'] . '">' . "\n";
        echo '<input type="hidden" name="productionState" value="' . $sharedDevice['properties']['data'][0]['productionState'] . '">' . "\n";
        echo '<input type="hidden" name="isAlive" value="' . $sharedDevice['properties']['data'][0]['isAlive'] . '">' . "\n";
      echo '</form>' . "\n";

//      echo '<form id="hostDelete"     action="/host/index.php?&page=deviceDelete.php" method="POST">' . "\n";
      echo '<form id="hostDelete"     action="/host/index.php?&page=deleteDevice.php" method="POST">' . "\n";
        echo '<input type="hidden" name="id" value="' . $id . '">' . "\n";
        echo '<input type="hidden" name="hostname" value="' . $sharedDevice['properties']['data'][0]['hostname'] . '">' . "\n";
        echo '<input type="hidden" name="address" value="' . $sharedDevice['properties']['data'][0]['address'] . '">' . "\n";
        echo '<input type="hidden" name="firstSeen" value="' . $sharedDevice['properties']['data'][0]['firstSeen'] . '">' . "\n";
        echo '<input type="hidden" name="productionState" value="' . $sharedDevice['properties']['data'][0]['productionState'] . '">' . "\n";
        echo '<input type="hidden" name="isAlive" value="' . $sharedDevice['properties']['data'][0]['isAlive'] . '">' . "\n";
        echo '<input type="hidden" name="sharedDevice" value="' . htmlspecialchars(json_encode($sharedDevice), ENT_QUOTES, "UTF-8") . '">' . "\n";
      echo '</form>' . "\n";

      echo '<form id="hostMonitors" method="POST" action="/host/index.php?&page=deviceMonitors.php">' . "\n";
        echo '<input type="hidden" name="id" value="' . $id . '">' . "\n";
        echo '<input type="hidden" name="hostname" value="' . $sharedDevice['properties']['data'][0]['hostname'] . '">' . "\n";
        echo '<input type="hidden" name="activeMonitors" value="' . htmlspecialchars(json_encode($rawActiveMonitors['data'], 1)) . '">' . "\n";
      echo '</form>' . "\n";

      echo '<form id="addMonitors"    action="/host/addMonitors.php"         method="POST"><input type="hidden" name="id" value="' . $id . '"></form>' . "\n";

      echo '<form id="hostGraphs"     action="/host/index.php?&page=deviceGraphs.php"          method="POST">' . "\n";
        echo '<input type="hidden" name="id" value="' . $id . '">' . "\n";
        echo '<input type="hidden" name="hostname" value="' . $sharedDevice['properties']['data'][0]['hostname'] . '">' . "\n";
        echo '<input type="hidden" name="activeMonitors" value="' . htmlspecialchars(json_encode($rawActiveMonitors['data'], 1)) . '">' . "\n";
      echo '</form>' . "\n";

      echo '<form id="performance"    action="/host/index.php?&page=devicePerformance2.php"   method="POST">' . "\n";
        echo '<input type="hidden" name="id" value="' . $id . '">' . "\n";
        echo '<input type="hidden" name="hostname" value="' . $sharedDevice['properties']['data'][0]['hostname'] . '">' . "\n";
        echo '<input type="hidden" name="performanceData" value="' . htmlspecialchars(json_encode($rawDevicePerformance['data'], 1)) . '">' . "\n";
      echo '</form>' . "\n";

      // Decide if we have run discovery against host before or not
      if ( ! isset($sharedDevice['properties']['data'][0]['properties'])) {
        echo '<button form="findProperties" name="findProperties" type="submit" class="btn btn-success">Discover Properties</button> ' . "\n";
      }
      else {
        echo '<button form="hostProperties" type="submit" class="btn btn-primary">Change Properties</button> ' . "\n";
      }
      echo '<button form="hostModify" type="submit" class="btn btn-warning">Modify Device</button> ' . "\n";

      // Decide if we have monitors to show or not
      if ( isset($rawActiveMonitors['data'][0])) {
        echo '<button form="hostMonitors" type="submit" class="btn btn-primary">Change Monitors</button> ' . "\n";
      }
      else {
        echo '<button form="hostMonitors" type="submit" class="btn btn-success">Add Monitors</button> ' . "\n";
      }

      // Decide if we have Host or Device components we are aware of for display ( from database not graphs? )
      if ( isset($rawDevicePerformance['data'][0])) {
        echo '<button form="performance" type="submit" class="btn btn-primary">Device Performance</button> ' . "\n";
      }

      // Decide if we have any RRD or Graphite graphs to display (influx will come with V2?)
      if ( in_array("rrd", $storage) || in_array("graphite", $storage)) {
        echo '<button form="hostGraphs" type="submit" class="btn btn-primary">Graphs</button> ' . "\n";
      }

      // Just a simple spacer that does nothing
      echo '<button class="btn btn-primary"> &nbsp&nbsp </button> ' . "\n";

      // Nuke the host
      echo '<button form="hostDelete" type="submit" class="btn btn-danger">Delete Device</button> ' . "\n";
      echo "</center>";
      echo "<!-- End device Top Tabs DATE " . time() . "-->";

?>
