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
            <li><a href="/mapping/createTrapMapping.php">Create SNMP Trap Mapping</a></span> </li>
          </ul>
        </div>

        <!-- Simplest way to POST the data I could find -->
        <script>
        "use strict";
        function submitForm(oFormElement) {
          var xhr = new XMLHttpRequest();
          xhr.onload = function() {
            alert (xhr.responseText);
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
//        echo '<form action="' . $apiUrl . ':' . $apiPort . '/mapping/set"  method="POST">';
        ?>
        <form id="form" action="<?php echo $apiUrl . ':' . $apiPort; ?>/mapping/set"  method="POST" onsubmit="return submitForm(this);">
          <div class="mb-3 md-3">
            <button type="button" class="btn btn-sm btn-primary" title="Event Alarm Name" data-bs-toggle="popover" data-bs-content="Alarm names must never contain spaces.  This will cause heart burn in other areas.  Recommend camelCase and dashes to make good alarm names">?</button>
            <label for="displayName" class="form-label">Display Name:</label>
            <input type="displayName" class="form-control" id="display_name" placeholder="camelCaseAlarmName" name="display_name">
          </div>
          <div class="mb-3">
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="popover" title="SNMP OID" data-bs-content="All OID values must start with a period and end with an integer.  This system currently ignores using MIB sets to translate the incomming data to pretty names as the computer itself is not using those.  MIB's generally appear more for human reading.  We can do better by mapping and sharing the mappings out.">?</button>
            <label for="oid" class="form-label">SNMP Trap OID:</label>
            <input type="displayName" class="form-control" id="oid" placeholder=".1.2.3.X.Y.Z" name="oid">
          </div>
          <div class="mb-3">
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="popover" title="Severity" data-bs-content="Event severities are defined from 0 to 5.  0 = OK, 1 = DEBUG, 2 = INFO, 3 = WARNING, 4 = ERROR, 5 = CRITICAL ">?</button>
            <label for="severity" class="form-label">Event Severity:</label>
            <input type="displayName" class="form-control" id="severity" placeholder="1" name="severity">
          </div>
          <div class="mb-3">
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="popover" title="Age Out Value" data-bs-content="An integer value in seconds for how long to keep a single non-updated event active.  Event will move to history when the age-out value has been passed with no updates to the event, or a clear event has not been triggered">?</button>
            <label for="age_out" class="form-label">Age Out Timer:</label>
            <input type="displayName" class="form-control" id="age_out" placeholder="86400" name="age_out">
          </div>
          <div class="mb-3">
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="popover" title="Event Pre Processing" data-bs-content="Use raw PHP to manipulate events to be readable by a NOC, or other interested party.  See documentation for examples">?</button>
            <label for="pre_processing">Event Pre Processing:</label>
            <textarea class="form-control" rows="10" id="pre_processing" name="pre_processing"></textarea>
          </div>
          <div class="mb-3">
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="popover" title="Event Post Processing" data-bs-content="Use raw PHP to change an event AFTER it has been inserted into the database.">?</button>
            <label for="post_processing" class="form-label">Event Post Processing: (Rarely needed)</label>
            <textarea class="form-control" rows="5" id="post_processing" name="post_processing"></textarea>
          </div>
          <div class="mb-3">
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="popover" title="Trap Types" data-bs-content="Define what SNMP trap type we received">?</button>
            <label for="type" class="form-label">Trap Type: (Unused currently)</label>
            <input type="displayName" class="form-control" id="type" placeholder="1" name="type" disabled>
          </div>
          <div class="mb-3">
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="popover" title="Parent Of Event" data-bs-content="Define events that are a parent or more important than the event received for correlations">?</button>
            <label for="parent_of" class="form-label">Parent Of: (Unused currently)</label>
            <input type="displayName" class="form-control" id="parent_of" placeholder="fakeEventParent" name="parent_of" disabled>
          </div>
          <div class="mb-3">
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="popover" title="Child Of Event" data-bs-content="Define events that this event takes precendence over">?</button>
            <label for="child_of" class="form-label">Child Of: (Unused currently)</label>
            <input type="displayName" class="form-control" id="child_of" placeholder="fakeEventChildren" name="child_of" disabled>
          </div>
          <button type="submit" class="btn btn-primary" disabled>Test Mapping</button>
          <button type="submit" class="btn btn-primary"  value="post request">Create Mapping</button>
          <!-- <button type="submit" class="btn btn-primary">Create Mapping</button>  -->
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
