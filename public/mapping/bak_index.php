<!DOCTYPE html>
<html lang="en">
<!-- <META HTTP-EQUIV=Refresh CONTENT="10">  -->
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="Main Event UI Page" />
  <meta name="author" content="Chris Hubbard" />

  <title>Mapping UI</title>

<!--
  <link href="/js/bootstrap-5/css/bootstrap.min.css" rel="stylesheet" />
  <link href="/js/bootstrap-5/css/bootstrap-grid.css" rel="stylesheet" />
  <link href="/js/bootstrap-5/css/bootstrap-reboot.css" rel="stylesheet" />
  <link href="/js/bootstrap-5/css/bootstrap-utilities.css" rel="stylesheet" />
  <link href="/js/bootstrap-5/css/bootstrap.rtl.css" rel="stylesheet" />
  <link href="/css/styles.css" rel="stylesheet" />
-->
  <link href="/js/sb-demo/css/styles.css" rel="stylesheet" />
  <link href="/js/bootstrap-5/css/bootstrap.css" rel="stylesheet" />

</head>

<!-- fas == font awesome javascript.  Has nice icons, etc -->
<!-- https://fontawesome.com/search?m=free  choose icon, and find the name.  Call in the i class= to integrate in -->

<body class="sb-nav-fixed">

  <!-- Navbar -->
  <!-- Upper left to right across the top -->
  <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">

    <!-- Navbar Branding -->
    <a class="navbar-brand ps-3" href="/mapping/index.php">Mapping</a>

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
        </nav>
      </div>

  <!-- Add Main panel content here -->

  <div id="layoutSidenav_content">
    <main>
      <!--  This is the breadcrumb at the top of main part of the page -->
      <div class='container-fluid'>
        <div class="row">
          <div class="col">
            <ul id="bc1" class="breadcrumb"><span id="ellipses" style="display: inline;">... </span>
            <li><a href="/index.php">Home</a> <span class="divider"> <span class="accesshide "></span> </span><span class="arrow sep">&nbsp ► &nbsp </span> </span> </li>
            <li><a href="/mapping/index.php">Mapping</a></span> </li>
            </ul>
          </div>
        </div>
      </div>
      <!-- Main list of mappings supported -->
      <div class="container-fluid px-4">
        <div class="row">
          <div class="col-xl-3 col-md-6">
            <div class="card" style="width: 400px;">
              <div class="card-body">
                <h5 class="card-title">Specific Host</h5>
                <h6 class="card-subtitle mb-3 text-muted">Changes to a specific single host</h6>
                <p class="card-text">Allows changes or creation of a hostname and IP address pairing.</p>
                <a href="/host/createHost.php" class="card-link">Add New Host</a><br>
                <a href="/mapping/changeHost.php" class="card-link">Change hostname or IP address</a><br>
                <a href="/mapping/deleteHost.php" class="card-link">Remove a single host</a>
              </div>
            </div>
          </div>
          <div class="col-xl-3 col-md-6">
            <div class="card" style="width: 400px;">
              <div class="card-body">
                <h5 class="card-title">Hostgroups</h5>
                <h6 class="card-subtitle mb-3 text-muted">Hostgroups and associated hosts</h6>
                <p class="card-text">Create hostroups for monitors, or add / remove hosts from a hostgroup.</p>
                <a href="/mapping/createHostMonitor.php" class="card-link">Add New Hostgroup</a><br>
                <a href="/mapping/modifyHostMonitor.php" class="card-link">Change hosts associated with a hostgroup</a><br>
                <a href="/mapping/deleteHostMonitor.php" class="card-link">Remove a single hostgroup</a>
              </div>
            </div>
          </div>
          <div class="col-xl-3 col-md-6">
            <div class="card" style="width: 400px;">
              <div class="card-body">
                <h5 class="card-title">Host Attributes</h5>
                <h6 class="card-subtitle mb-3 text-muted">Atrtributes linked to a single host</h6>
                <p class="card-text">Specific variables that can be used elsewhere associated with a specific host.</p>
                <a href="/mapping/newHost.php" class="card-link">Add host attribute to host</a><br>
                <a href="/mapping/changeHost.php" class="card-link">Change value associated with host</a><br>
                <a href="/mapping/deleteHost.php" class="card-link">Remove value associated with host</a>
              </div>
            </div>
          </div>
          <div class="col-xl-3 col-md-6">
            <div class="card" style="width: 400px;">
              <div class="card-body">
                <h5 class="card-title">Infrastructure(todo)</h5>
                <h6 class="card-subtitle mb-3 text-muted">Link hosts to infrastructure paths</h6>
                <p class="card-text">Define infrastructure values</p>
                <a href="#" class="card-link">Add new infrastructure class</a><br>
                <a href="#" class="card-link">Change hostname associated with class</a><br>
                <a href="#" class="card-link">Remove an infrastructure class</a>
              </div>
            </div>
          </div>
        </div>
        <span><hr></span>
        <div class="row">
          <div class="col-xl-3 col-md-6">
            <div class="card" style="width: 400px;">
              <div class="card-body">
                <h5 class="card-title">SNMP Traps</h5>
                <h6 class="card-subtitle mb-3 text-muted">snmptrap translations</h6>
                <p class="card-text">Create or edit how an SNMP trap is displayed</p>
                <a href="/mapping/createTrapMapping.php" class="card-link">Add New Trap</a><br>
                <a href="/mapping/modifyTrapMapping.php" class="card-link">Change specific trap</a><br>
                <a href="/event/replayEvent.php" class="card-link">Replay and edit a recieved trap</a><br>
               <a href="/mapping/deleteTrapMapping.php" class="card-link">Remove a single trap mapping</a>
              </div>
            </div>
          </div>
          <div class="col-xl-3 col-md-6">
            <div class="card" style="width: 400px;">
              <div class="card-body">
                <h5 class="card-title">Monitoring</h5>
                <h6 class="card-subtitle mb-3 text-muted">Work with active monitors</h6>
                <p class="card-text">Add modify or remove monitors or alter hostgroups used by a specific monitor</p>
                <a href="/mapping/createGlobalMapping.php" class="card-link">Add a new monitor</a><br>
                <a href="/mapping/modifyGlobalMapping.php" class="card-link">Change a monitor</a><br>
               <a href="/mapping/deleteGlobalMapping.php" class="card-link">Remove or disable a monitor</a>
              </div>
            </div>
          </div>
        <span><hr></span>
        <div class="row">
          <div class="col-xl-3 col-md-6">
            <div class="card" style="width: 400px;">
              <div class="card-body">
                <h5 class="card-title">Reporting Engine(todo)</h5>
                <h6 class="card-subtitle mb-3 text-muted">Set report rules</h6>
                <p class="card-text">Create or alter report generation</p>
                <a href="#" class="card-link">Add a new report</a><br>
                <a href="#" class="card-link">Change specific report</a><br>
                <a href="#" class="card-link">Remove a single report</a>
              </div>
            </div>
          </div>
          <div class="col-xl-3 col-md-6">
            <div class="card" style="width: 400px;">
              <div class="card-body">
                <h5 class="card-title">Maintenance Engine(todo)</h5>
                <h6 class="card-subtitle mb-3 text-muted">Set maintenance windows</h6>
                <p class="card-text">Set timeframes that suppress events from firing</p>
                <a href="#" class="card-link">Add a new maintenance window</a><br>
                <a href="#" class="card-link">Change an existing window</a><br>
                <a href="#" class="card-link">Remove a maintenance window</a>
              </div>
            </div>
          </div>
          <div class="col-xl-3 col-md-6">
            <div class="card" style="width: 400px;">
              <div class="card-body">
                <h5 class="card-title">Correlation Engine(todo)</h5>
                <h6 class="card-subtitle mb-3 text-muted">Change the correlation engine for business</h6>
                <p class="card-text">Setup rules for the event correltaion engine to match raw events and relate them to business logic</p>
                <a href="#" class="card-link">Add a new rule</a><br>
                <a href="#" class="card-link">Change a rule</a><br>
                <a href="#" class="card-link">Remove an existing rule</a>
              </div>
            </div>
          </div>
          <div class="col-xl-3 col-md-6">
            <div class="card" style="width: 400px;">
              <div class="card-body">
                <h5 class="card-title">Notification Engine(todo)</h5>
                <h6 class="card-subtitle mb-3 text-muted">Change who gets notified when</h6>
                <p class="card-text">Setup or change notifications</p>
                <a href="#" class="card-link">Add a new rule</a><br>
                <a href="#" class="card-link">Change a notification rule</a><br>
                <a href="#" class="card-link">Remove existing rule</a>
              </div>
            </div>
          </div>
        </div>
      </div>

    </main>
  </div>

  <footer class="py-4 bg-light mt-auto">
    <div class="container-flex px-4">
      <div class="d-flex align-items-center justify-content-between small">
        <?php echo ' <div class="text-muted">&copy; NMS Monitoring ' . date(Y) . ' </div>'; ?>
          <div>
            <a href="/general/privacy.html">Privacy Policy</a>
            &middot;
            <a href="/general/toc.html">Terms &amp; Conditions</a>
          </div>
      </div>
    </div>
  </footer>
  <script src="/js/font-awesome/all.min.js" crossorigin="anonymous"></script>
  <script src="/js/bootstrap-5/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script src="/js/light-switch-bootstrap-main/switch.js"></script>
  <script src="/js/sb-demo/js/scripts.js"></script>
<!--  <script src="/js/bootstrap-5/js/bootstrap.js"></script>    -->
<!--  <script src="/js/scripts.js"></script>  -->
</body>
</html>
