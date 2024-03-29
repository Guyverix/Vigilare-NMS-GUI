<!DOCTYPE html>
<html lang="en">
<!-- <META HTTP-EQUIV=Refresh CONTENT="10">  -->
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="Main Create Mapping Poller UI Page" />
  <meta name="author" content="Chris Hubbard" />

  <title>Mapping UI</title>

  <link href="/js/sb-demo/css/styles.css" rel="stylesheet" />
<!--  <link href="/js/bootstrap-5/css/bootstrap.css" rel="stylesheet" />  -->

</head>

<!-- fas == font awesome javascript.  Has nice icons, etc -->
<!-- https://fontawesome.com/search?m=free  choose icon, and find the name.  Call in the i class= to integrate in -->

<body class="sb-nav-fixed">

  <!-- Navbar -->
  <!-- Upper left to right across the top -->
  <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">

    <!-- Navbar Branding -->
    <a class="navbar-brand ps-3" href="/mapping/index.php">Events</a>

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
    </div>



  <!-- Add Main panel content here -->

  <div id="layoutSidenav_content">
    <main>
      <div class="container">
        <!--  This is the breadcrumb at the top of the page -->
        <div class='container-fluid mt-2'>
          <ul id="bc1" class="breadcrumb"><span id="ellipses" style="display: inline;">...</span>
            <li><a href="/index.php">Home</a> <span class="divider"> <span class="accesshide "></span> </span><span class="arrow sep">&nbsp ► &nbsp </span> </span> </li>
            <li><a href="/mapping/index.php">Mapping</a> <span class="divider"> <span class="accesshide "></span> </span><span class="arrow sep">&nbsp ► &nbsp </span> </span> </li>
            <li><a href="/mapping/createGlobalMapping.php">Create Global Monitor Mapping</a></span> </li>
          </ul>
        </div>

        <!-- Simplest way to POST the data I could find -->
        <script>
        "use strict";
        function submitForm(oFormElement) {
          var xhr = new XMLHttpRequest();
          xhr.onload = function() {
            alert (xhr.responseText);
            location.reload(true);
          } // success case
          xhr.onerror = function() {
            alert ("Failed to create mapping.");
          } // failure case
          xhr.open (oFormElement.method, oFormElement.action, true);
          xhr.send (new FormData (oFormElement));
          return false;
        }
        </script>

        <!-- After breadcrumbs show something to do -->
        <?php
        include_once __DIR__ . '/../../config/api.php';
        ?>
        <form id="form" action="<?php echo $apiUrl . ':' . $apiPort; ?>/mappingWIP/poller/create" method="POST"  onsubmit="return submitForm(this);">
          <div class="mb-3 md-3">
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="popover" title="Event Alarm Name" data-bs-content="Alarm names must never contain spaces.  This will cause heart burn in other areas.  Recommend camelCase and dashes to make good alarm names">?</button>
            <label for="displayName" class="form-label">Display Name:</label>
            <input type="displayName" class="form-control" id="checkName" placeholder="camelCaseAlarmNameChangeMe" name="checkName">
          </div>
          <div class="mb-3">
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="popover" title="Command Details" data-bs-content="This is the content of the command to be run.  Raw OIDs, NRPE commands, Shell (nagios) commands, and curl (future) are valid commands that can be run">?</button>
            <label for="oid" class="form-label">Command to run is of type: SNMP OID / NRPE command / Shell Command / Curl (future):</label>
            <input type="displayName" class="form-control" id="checkAction" placeholder=".1.2.3.X.Y.Z or /blah/foo/bar/check.sh $hostname -w 2 -c 3 " name="checkAction">
          </div>
          <div class="mb-3">
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="popover" title="Severity" data-bs-content="Supported types: get (snmp), walk (snmp), nrpe, shell, curl (future) ">?</button>
            <label for="severity" class="form-label">Check type:</label>
<!--            <input type="displayName" class="form-control" id="severity" placeholder="1" name="severity">    -->
            <div>
              <input class="form-check-input" type="radio" name="type" id="get" value="get" checked>
              <label class="form-check-label" for="get">SNMP GET</label>
            </div>
            <div>
              <input class="form-check-input" type="radio" name="type" id="walk" value="walk">
              <label class="form-check-label" for="walk">SNMP Walk</label>
            </div>
            <div>
              <input class="form-check-input" type="radio" name="type" id="nrpe" value="nrpe">
              <label class="form-check-label" for="nrpe">NRPE</label>
            </div>
            <div>
              <input class="form-check-input" type="radio" name="type" id="shell" value="shell">
              <label class="form-check-label" for="shell">Shell (Nagios style)</label>
            </div>
            <div>
              <input class="form-check-input" type="radio" name="type" id="curl" value="curl" disabled>
              <label class="form-check-label" for="type">Curl</label>
            </div>
          </div>
          <div class="mb-3">
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="popover" title="Storage Type" data-bs-content="Supported Storage types are: database, graphite, databaseMetric ">?</button>
            <label for="severity" class="form-label">Metric Storage type:</label>
            <div>
              <input class="form-check-input" type="radio" name="storage" id="database" value="database" checked>
              <label class="form-check-label" for="database">Database</label>
            </div>
            <div>
              <input class="form-check-input" type="radio" name="storage" id="graphite" value="graphite">
              <label class="form-check-label" for="graphite">Graphite</label>
            </div>
            <div>
              <input class="form-check-input" type="radio" name="storage" id="databaseMetric" value="databaseMetric">
              <label class="form-check-label" for="databaseMetric">Database Metrics</label>
            </div>
          </div>
          <div class="mb-3">
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="popover" title="Age Out Value" data-bs-content="Iteration cycle to use.  Default is 300 seconds (5 minutes), each new iteration cycle gets its own daemon.  Use caution with making all kinds of different cycles for no reason.">?</button>
            <label for="age_out" class="form-label">Iteraction cycle speed:</label>
            <input type="displayName" class="form-control" id="iteration" value="300" name="iteration"></input>
          </div>
          <div class="mb-3">
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="popover" title="HostGroup(s)" data-bs-content="list of hostgroups that this monitor is used in.  If they do not exist, this check will never fire.">?</button>
            <label for="pre_processing">Host Groups: (Optional)</label>
            <textarea class="form-control" rows="2" id="hostGroup" name="hostGroup" placeholder='["hostgroup", "hostgroup2"]'></textarea>
          </div>
          <div class="mb-3">
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="popover" title="Host" data-bs-content="Abnormal hosts that require this check, but do not fall into a specific hostGroup category.  Generally these are snowflakes, and should be avoided if possible.">?</button>
            <label for="host" class="form-label">Hostnames: (Optional)</label>
            <textarea class="form-control" rows="2" id="host" name="hostname" placeholder='["server01.some.domain.com", "server02.another.domain.com"]'></textarea>
          </div>
          <!-- <button type="submit" class="btn btn-primary"  value="post request">Create Monitor</button>    -->
          <!-- <button type="submit" class="btn btn-primary">Create Monitor</button>  -->
          <!-- <button type="submit" class="btn btn-danger" formaction="http://192.168.15.65:8002/mappingWIP/poller/die" value="Test New Monitor Values" form="form">TEST</button>  -->
          <button type="submit" class="btn btn-danger" disabled>Test Changes</button>
          <button type="submit" class="btn btn-primary" form="form">Create Monitor</button>
        </form>
      </div>
    </main>
  </div>

  <!-- this echos post data to the console.  Kinda useful -->
  <script>
  addEventListener('DOMContentLoaded', onloadCb);

  // This callback will be executed when the page initially loads.
  // See line 1 to know why
  function onloadCb () {
    // Grab all forms which have a method attribute
    const forms = document.querySelectorAll('form[method]');

    // Display info of any submitted form
    forms.forEach(function (form) {
        form.addEventListener('submit', displayInfo);
    });

    forms.forEach(function (form) {
      form.add
  };

  function displayInfo(event) {
    // Prevent the page from redirecting to the action attribute.
    event.preventDefault();

    // Get the form data
    const formData = new FormData(event.target);
    logAsTitle('Data:');

    for (let [key, val] of formData.entries()) {
        console.log(`${key} \t ${val || '<Empty>'}`);
    }

    logAsTitle('Form attributes');
    const neededAttributes = ['action','method', 'enctype'];
    for (let attr of neededAttributes) {
        console.log(`${attr} - ${event.target[[attr]] || '<empty>'}`);
    }
  }

  function logAsTitle(str) {
    console.log('\n' + str);
    console.log('='.repeat(str.length));
  }
  </script>


  <footer class="py-4 bg-light mt-auto">
    <div class="container-flex px-4">
      <div class="d-flex align-items-center justify-content-between small">
        <?php echo ' <div class="text-muted">&copy; NMS Monitoring ' . date(Y) . ' </div>'; ?>
          <div>
            <a href="#">Privacy Policy</a>
              &middot;
            <a href="#">Terms &amp; Conditions</a>
          </div>
      </div>
    </div>
  </footer>

  <script src="/js/font-awesome/all.min.js" crossorigin="anonymous"></script>
  <script src="/js/bootstrap-5/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script src="/js/light-switch-bootstrap-main/switch.js"></script>
  <script src="/js/sb-demo/js/scripts.js"></script>
  <script src="/js/popover/script.js"></script>

</body>
</html>
