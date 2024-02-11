<!DOCTYPE html>
<html lang="en">
<!-- <META HTTP-EQUIV=Refresh CONTENT="10">  -->
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="" />
  <meta name="author" content="" />

  <title>Daemon UI</title>

  <link href="/js/bootstrap-5/css/bootstrap.min.css" rel="stylesheet" />
  <link href="/css/styles.css" rel="stylesheet" />
</head>

<!-- fas == font awesome javascript.  Has nice icons, etc -->
<!-- https://fontawesome.com/search?m=free  choose icon, and find the name.  Call in the i class= to integrate in -->

<body class="sb-nav-fixed">
  <!-- Navbar -->
  <!-- Upper left corner -->
  <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <!-- Navbar Branding -->
    <a class="navbar-brand ps-3" href="/daemon/index.php">Daemon Control</a>
    <!-- Sidebar Toggle -->
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
    <!-- Navbar Search -->
    <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
      <div class="input-group">
        <input class="form-control" type="text" placeholder="Search for..." aria-label="Search for..." aria-describedby="btnNavbarSearch" />
        <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>
      </div>
    </form>
    <!-- Far right top -->
    <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
      <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
          <li><a class="dropdown-item" href="/user/settings.php">Settings</a></li>
          <li><a class="dropdown-item" href="/user/lockAccount.php">Lock Access</a></li>
          <li><hr class="dropdown-divider" /></li>
          <li>
            <div class="form-check form-switch">
              <label class="form-check-label" for="lightSwitch"> Dark Mode </label>
              <input class="form-check-input" type="checkbox" id="lightSwitch"/>
            </div>
          </li>
          <li><a class="dropdown-item" href="/user/logout.php">Logout</a></li>
        </ul>
      </li>
    </ul>
  </nav>

  <!-- left side vertical menu -->
  <div id="layoutSidenav">
    <div id="layoutSidenav_nav">
      <nav class="sb-sidenav accordion sb-sidenav-dark bg-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
          <div class="nav">
            <div class="sb-sidenav-menu-heading">Main</div>
              <a class="nav-link" href="/index.php">
              <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
              Dashboard
              </a>
              <a class="nav-link" href="/event/index.php">
              <div class="sb-nav-link-icon"><i class="fas fa-bell"></i></div>
              Event
              </a>
              <a class="nav-link" href="/infrastructure/index.php">
              <div class="sb-nav-link-icon"><i class="fas fa-network-wired"></i></div>
              Infrastructure
              </a>
              <a class="nav-link" href="/mapping/index.php">
              <div class="sb-nav-link-icon"><i class="fas fa-diagram-project"></i></div>
              Mapping
              </a>
              <a class="nav-link" href="/reporting/index.php">
              <div class="sb-nav-link-icon"><i class="fas fa-flag"></i></div>
              Reporting
              </a>
              <a class="nav-link" href="/daemon/index.php">
              <div class="sb-nav-link-icon"><i class="fas fa-stopwatch"></i></div>
              Daemon
              </a>


              <div class="sb-sidenav-menu-heading">Support</div>
              <a class="nav-link" href="/admin/index.php">
              <div class="sb-nav-link-icon"><i class="fas fa-lock-open"></i></div>
              Admin
              </a>
              <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
              <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
              Documentation
              <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
              </a>
              <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                <nav class="sb-sidenav-menu-nested nav">
                  <a class="nav-link" href="/support/hosts.html" target="_blank">Host</a>
                  <a class="nav-link" href="/support/infrastructure.html" target="_blank">Infrastructure</a>
                  <a class="nav-link" href="/support/mapping.html" target="_blank">Mapping</a>
                  <a class="nav-link" href="/support/reporting.html" target="_blank">Reporting</a>
                  <a class="nav-link" href="/support/daemon.html" target="_blank">Daemon</a>
                  <a class="nav-link" href="/support/admin.html" target="_blank">Admin</a>
                  <a class="nav-link" href="https://github.com/Guyverix/Vigilare-NMS-GUI" target="_blank">Gitlab UI</a>
                  <a class="nav-link" href="https://github.com/Guyverix/Vigilare-NMS-API" target="_blank">Gitlab API</a>
                  <a class="nav-link" href="http://webserver01:83/" target="_blank">PHP MyAdmin</a>
                  </nav>
                </div>
              </div>
            </div>
          </div>
        </div>
      </nav>
    </div>
<!--  </div>  -->
  <!-- Add Main panel content here -->


  <!-- Simplest way to POST the data I could find -->
  <script>
  "use strict";
  function submitForm(oFormElement) {
    var xhr = new XMLHttpRequest();
    xhr.onload = function() {
      alert (xhr.responseText);
      location.reload();  
    } // success case
    xhr.onerror = function() {
      alert ("Failed to create mapping.");
    } // failure case
    xhr.open (oFormElement.method, oFormElement.action, true);
    xhr.send (new FormData (oFormElement));
    return false;
  }
  </script>




  <!-- REAL CONTENT -->
  <div id="layoutSidenav_content">
    <main>
      <div class="container">
        <div class="card mb-1 bg-light">
          <div class="card-body table-responsive">Active Daemons
<br><br><br>
            <table id="daemonState" class="table table-striped table-hover bg-dark table-light" data-loading-template="loadingTemplate">
              <thead>
                <tr>
                  <th><center>Configured Daemon</center></th>
                  <th><center>Iteration Timer</center></th>
                  <th><center>Running Daemon</center></th>
                  <th><center>Component Iteration</center></th>
                  <th><center>Last Heartbeat</center></th>
                  <th><center>pid</center></th>
                  <th><center>Manipulation</center><th>
                </tr>
              </thead>
              <tfoot>
                <tbody>
                <div id="daemonState">
                <!-- This table data is PHP generated -->

                <?php
                  require_once __DIR__ . '/../../config/api.php';
                  $daemonList=array();
                  $tableData='';
                  $ch=curl_init();
                  curl_setopt($ch, CURLOPT_URL, $apiUrl . ":" . $apiPort . "/poller/housekeeping/heartbeat");
                  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                  $output = curl_exec($ch) ;
                  curl_close($ch);
                  $output = json_decode($output, true);
                  $heartbeatCount=count($output);
                  $heartbeat = $output;

                  // Get a list of all configured daemons
                  $ch2=curl_init();
                  curl_setopt($ch2, CURLOPT_URL, $apiUrl . ":" . $apiPort . "/poller/ping/list");
                  curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
                  $configured = curl_exec($ch2) ;
                  curl_close($ch2);
                  $configuredDaemons = json_decode($configured, true);

                  /*  did not end up needing this, but might be useful for something else
                  foreach($heartbeat['data'] as $singleDaemon) {
                    $daemonList[] = $singleDaemon['device'];
                  }
                  $daemonListUnfiltered = $daemonList; // keep a complete list so we can see how many daemons are running
                  $daemonList = array_unique($daemonList); // get rid of dupes in my list
                  */

                  $resultSet=array();
                  // echo '<pre>'; print_r($configuredDaemons['data']); echo '</pre>';
                  foreach ($configuredDaemons['data'] as $confed) {
                    foreach ($confed as $confKey => $confValue) {
                      foreach($heartbeat['data'] as $aDaemon) {
                        if ( strpos(strtolower($aDaemon['device']), $confKey) !== false && strpos($aDaemon['component'], $confValue) !== false ) {
                          // Deal with our edge cases of ping being a string match to housekeeping
                          if ($confKey == 'ping' && $aDaemon['device'] == 'housekeeping' ) { // do nothing, as they are not a real match
                          }
                          else {
                            $formData .= '<form id="' . $aDaemon['device'] . $aDaemon['component'] . 'status" action="' . $apiUrl . ':' . $apiPort . '/poller/' . $aDaemon['device'] . '/status" onsubmit="return submitForm(this);"></form>' . "\n";
                            $formData .= '<form id="' . $aDaemon['device'] . $aDaemon['component'] . 'stop" action="' . $apiUrl . ':' . $apiPort . '/poller/' . $aDaemon['device'] . '/stop" onsubmit="return submitForm(this);"></form>' . "\n";
                            $formData .= '<form id="' . $aDaemon['device'] . $aDaemon['component'] . 'start" action="' . $apiUrl . ':' . $apiPort . '/poller/' . $aDaemon['device'] . '/start" onsubmit="return submitForm(this);"></form>' . "\n";
                            $formData .= '<form id="' . $aDaemon['device'] . $aDaemon['component'] . 'restart" action="' . $apiUrl . ':' . $apiPort . '/poller/' . $aDaemon['device'] . '/restart" onsubmit="return submitForm(this);"></form>' . "\n";
                            $tableData  .= "<tr><td><center>" . $confKey . "</center></td><td><center>" . $confValue . "</center></td><td><center>" . $aDaemon['device'] . "</ceter></td><td><center>" . $aDaemon['component'] . "</center></td><td><center>" . $aDaemon['lastTime'] . "</center></td><td><center>" . $aDaemon['pid'] . "</center></td><td>\n";
                            if ( $aDaemon['device'] !== 'snmptrapd' && $aDaemon['device'] !== 'mysql') {
                              $tableData .= "<center><button type=\"submit\"  class=\"btn btn-primary\" form=\"". $aDaemon['device'] . $aDaemon['component'] . "status\" >Status</button>\n ";
                              $tableData .= "<button type=\"submit\"  class=\"btn btn-danger\" form=\"". $aDaemon['device'] . $aDaemon['component'] . "stop\" >Stop</button>\n ";
                              $tableData .= "<button type=\"submit\"  class=\"btn btn-success\" form=\"". $aDaemon['device'] . $aDaemon['component'] . "start\" >Start</button>\n ";
                              $tableData .= "<button type=\"submit\"  class=\"btn btn-warning\" form=\"". $aDaemon['device'] . $aDaemon['component'] . "restart\" >restart</button> </center></tr>\n";
                            }
                            else { $tableData .= "</center></tr>";
                            }  // end not snmptrapd
                          } // end ping != housekeeping
                        } // end if strpos
                      } // end foreach

                      // Loop to find stuff that never matches ( IE daemon dead hopefullly ONLY THAT)
                      if ( empty($resultSet[$confKey])) {
                        $resultSet[$confKey] = "not found";
                      }
                      foreach($heartbeat['data'] as $aDaemon) {
                        if ( strpos(strtolower($aDaemon['device']), $confKey) !== false ) {
                          if ( ! (($confKey == 'ping') && ($aDaemon['device'] == "housekeeping")) ) {
                            if ( ! (($confKey == 'snmp') && ($aDaemon['device'] == "snmptrapd")) ) {
                              //echo "FOUND " . $confKey . " MATCH AGAISNT " . $aDaemon['device'] . "<br>\n";
                              if ( $resultSet["$confKey"] !== "found" ) {
                                $resultSet["$confKey"] = "found";
                              }  // end if
                            } // end if
                          } // end if
                        } // end if
                      } // end botto foreach
                    } // end foreach
                  }  // end foreach


                foreach ( $resultSet as $deadKey => $deadValue) {
                  if ( $deadValue == "not found" ) {
                    switch ($deadKey) {
                      case "snmp":
                        $daemonKey="smartSnmpPoller";
                        break;
                      case "ping":
                        $daemonKey="smartPingPoller";
                        break;
                      case "nrpe":
                        $daemonKey="smartNrpePoller";
                        break;
                      case "snmp":
                        $daemonKey="smartSnmpPoller";
                        break;
                      case "shell":
                        $daemonKey="smartShellPoller";
                        break;
                      case "housekeeping":
                        $daemonKey="smartHousekeepingPoller";
                        break;
                      default:
                        $daemonKey=$deadValue;
                        break;
                    }
                    $formData .= '<form id="' . $daemonKey . $daemonKey . 'start" action="' . $apiUrl . ':' . $apiPort . '/poller/' . $daemonKey . '/start" onsubmit="return submitForm(this);"></form>';
                    $tableData .= "<tr><td><center>" . $deadKey . "</center></td><td><center>" . " unknown " . "</center></td><td><center>" . " dead " . "</ceter></td><td><center>" . " unknown " . "</center></td><td><center>" . "unknown" . "</center></td><td><center>" . "dead" . "</center></td><td>\n";
                    $tableData .= "<center><button type=\"submit\"  class=\"btn btn-success\" form=\"". $daemonKey . $daemonKey . "start\" >Start</button></center><tr> ";
                  }
                }
                echo $formData;
                echo $tableData;
                ?>
                </div>
                </tbody>
              </tfood>
            </table>
          </div>
        </div>
      </div>
    </main>
  </div>

<?php // print_r($daemonList); ?>
<?php // print_r($heartbeat['data']); ?>
  <script src="/js/font-awesome/all.min.js" crossorigin="anonymous"></script>
  <script src="/js/bootstrap-5/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>  <!-- Yes, anybody can pull our bootstrap js -->
  <script src="/js/light-switch-bootstrap-main/switch.js"></script>
  <script src="/js/scripts.js"></script>
</body>
</html>
