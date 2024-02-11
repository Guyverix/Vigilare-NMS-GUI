<!DOCTYPE html>
<html lang="en">
<!--<META HTTP-EQUIV=Refresh CONTENT="30">  -->
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="Main Graph UI Page" />
  <meta name="author" content="Chris Hubbard" />

  <title>Graph UI</title>
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
    <a class="navbar-brand ps-3" href="/event/event.php">Active Events</a>

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

<?php
////////////////////////////////////////////////
//  Define any necessary constants here
//  defined as URL/FQDN using GET
///////////////////////////////////////////////
$rawHostname=$_GET[hostname];
if (empty($rawHostname)) {
  $rawHostname=$_GET[id];
}

?>
      <div id="layoutSidenav_content">
        <main>
          <div class="container-fluid">
            <div class='container mt-2'>
              <ul id="bc1" class="breadcrumb"><span id="ellipses" style="display: inline;">...</span>
              <li><a href="/index.php">Home</a> <span class="divider"> <span class="accesshide "><span class="arrow_text">/</span> </span><span class="arrow sep">►&nbsp</span> </span>
              </li>
              <li><a href="/infrastructure/index.php">Infrastructure</a><span class="divider"> <span class="accesshide "><span class="arrow_text">/</span> </span><span class="arrow sep">►&nbsp</span> </span>
              </li>
              <li><a href="/host/hostList.php">Host</a><span class="divider"> <span class="accesshide "><span class="arrow_text">/</span> </span><span class="arrow sep">►&nbsp</span> </span>
              </li>
              <li><a href="/host/hostnameDetails.php?id=<?php echo $rawHostname; ?>"><?php echo $rawHostname; ?></a><span class="divider"> <span class="accesshide "><span class="arrow_text">/</span> </span><span class="arrow sep">►&nbsp</span> </span>
              </li>
              <li><span tabindex="0">&nbspAll Graphs&nbsp</span></li>
              </ul>
            </div>

            <div class="card mb-1 bg-light">
             <div class="card-body table-responsive">




<?php
$safeHostname=preg_replace('/\./', '_', $rawHostname); // API reads periods as pathing or options.  Always convert period to underscore
require_once __DIR__ . '/../../config/api.php';

//  Initiate curl
$ch = curl_init();
$url = "http://" . $apiHostname . ':' . $apiPort . "/graphite/$safeHostname";

// Will return the response, if false it print the response
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL,$url);

// Execute
$rawResult=curl_exec($ch);

// Closing
curl_close($ch);

$result=json_decode($rawResult, true);

if ( preg_match( '/unable to retrieve metric/' ,$result['data'][0])) {
  echo "No metric graphs found";
}
else {
  foreach ($result as $lists) {
    foreach($lists as $list) {
      $list=preg_replace('/[ ]/','%20',$list); // PHP does dumb things with spaces when echoing
      $list=preg_replace('/-6h/','-48h', $list);  // testing different times
      switch ($list) {
        case strpos($list, '.lmsensors' ) !== false:
          $filterList = preg_replace('/.*..lmsensors./', '', $list);
          $sensorsPath = explode('.', $filterList);
          $sensorList = $sensorsPath[0];
          $sensorValue = preg_replace('/,.*/','', $sensorsPath[1]);
          $sensorValue = preg_replace('/\).*/','', $sensorsPath[1]);
          $cleanSensors["$sensorList"]["$sensorValue"] = "$list";
          break;
        case strpos($list, '.load' ) !== false:
          $filterList = preg_replace('/.*.\.load\./', '', $list);
          $filterList = preg_replace('/.*.\.nrpePoller\./', '', $filterList);  // NRPE checks for load also hit this, dammit
          $loadsPath = explode('.', $filterList);
          $loadList = $loadsPath[0];
          $loadValue = preg_replace('/[,)].*/','', $loadsPath[1]);  // strip out all after seeing , or ) character
          $cleanLoad["$loadList"]["$loadValue"] = "$list";
          break;
        case strpos($list, '.interfaces' ) !== false:
          $filterList=preg_replace('/.*..interfaces./', '', $list);
          $interfacePath=explode('.', $filterList);
          $interfaceList=$interfacePath[0];
          $interfaceValue=preg_replace('/[,)].*/','', $interfacePath[1]);
          $cleanInterfaces["$interfaceList"]["$interfaceValue"] = "$list";
          break;
        case strpos($list, '.nrpePoller' ) !== false:
          $filterList = preg_replace('/.*..nrpePoller./', '', $list);
          $nrpePath = explode('.', $filterList);
          $nrpeList = $nrpePath[0];
          $nrpeValue = preg_replace('/[,)].*/','', $nrpePath[1]);
          $cleanNrpe["$nrpeList"]["$nrpeValue"] = "$list";
          break;
        default:
          // this is a best effort attempt to show anything else that remains
          $filterList = preg_replace("/.*.$safeHostname./", '', $list);
          $defaultPath = explode('.', $filterList);
          $defaultList = $defaultPath[0];
          $defaultValue = preg_replace('/[,)].*/','', $defaultPath[1]);
//echo "DEFAULT " . $defaultList . "LIST " . $defaultList . "VALUE " . $defaultValue . "<br>";
          $cleanDefault["$defaultList"]["$defaultValue"] = "$list";
          break;
      }  // end switch
    }  // end foreach
  }  // end foreach


  /* All graphs should be datafilled now.  Create tables for them (if they exist) */
  $st1='<a href="';
//  $st2='<img id="image-viewer" src=';
  $st3='" target="_blank"><img id="image-viewer" src=';
  $ed2='</a>';
  $ed1='>' . $ed2 . '</div></td>';

  $st='<img id="image-viewer" src=';
  $ed='</div></td>';

  if ( ! empty($cleanInterfaces)) {
     echo "Ethernet<br>\n";
    foreach ($cleanInterfaces as $key => $value) {
      // docker0 etc
      echo '<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#' . $key . '">' .$key . '</button>' . "\n";
      echo '<div class="modal" id="' . $key .'">' . "\n" . '<div class="modal-dialog  modal-xl modal-dialog-centered">' . "\n" . '<div class="modal-content">' . "\n";
      echo '<div class="modal-header"><h4 class="modal-title">Ethernet Interface: ' . $key . '</h4> <button type="button" class="btn-close" data-bs-dismiss="modal"></button>' . "\n" . '</div>' . "\n" . '<div class="modal-body">' . "\n";
      echo "<center><table>";
      $counter=0;
      foreach ($value as $k => $v) {
        // Make a thumb and a large image
        $vc=preg_replace('/586/', '250', $v);
        $vc=preg_replace('/308/','150', $vc);
        $v=preg_replace('/586/', '1586', $v);
        $v=preg_replace('/308/','800', $v);
        // counterFoo
        if ( $counter == 0 ) {
         echo "<tr><td><b><center>" . $k ."</center></b><br>" . $st1 . $v . $st3 . $vc . $ed1;
         $counter++;
        }
        elseif ( $counter <=2 ) {
          echo "<td><b><center>" . $k ."</center></b><BR>". $st1 . $v . $st3 . $vc . $ed1;
          $counter++;
        }
        elseif( $counter >= 3 ) {
          echo "<td><b><center>" . $k ."</center></b><BR>" . $st1 . $v . $st3 . $vc . $ed1 . "</tr>";
          $counter=0;
        }
      } // end foreach
      $counter=0;
      echo "</table></center>";
      echo '</div><div class="modal-footer"><button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button></div></div></div></div>&nbsp';
    }  // end foreach
    echo "<br><br>";
  }  // end if
  if ( ! empty ($cleanDefault)) {
    foreach ($cleanDefault as $key => $value) {
     echo "Default<br>\n";
      // random string1, random string 2,  etc...
      echo '<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#' . $key . '">View ' .$key . '</button>';
      echo '<div class="modal" id="' . $key .'"> <div class="modal-dialog  modal-xl modal-dialog-centered"> <div class="modal-content"><div class="modal-header"><h4 class="modal-title">Default: ' . $key . '</h4> <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div> <div class="modal-body">';
      echo "<table>";
      $counter=0;
      foreach ($value as $k => $v) {
        $vc=preg_replace('/586/', '250', $v);
        $vc=preg_replace('/308/','150', $vc);
        $v=preg_replace('/586/', '1586', $v);
        $v=preg_replace('/308/','800', $v);
        // counterFoo
        if ( $counter == 0 ) {
         $kClean=preg_replace('/_/','/',$k);
         $kClean=preg_replace('/"/','',$kClean);
         echo "<tr><td><b><center>" . $kClean . "</center></b><br>" . $st1 . $v . $st3 . $vc . $ed1;
         $counter++;
        }
        elseif ( $counter <= 2 ) {
          $kClean=preg_replace('/_/','/',$k);
          $kClean=preg_replace('/"/','',$kClean);
          echo "<td><b><center>" . $kClean . "</center></b><BR>". $st1 . $v . $st3 . $vc . $ed1;
          $counter++;
        }
        elseif( $counter >= 3 ) {
          $kClean=preg_replace('/_/','/',$k);
          $kClean=preg_replace('/"/','',$kClean);
          echo "<td><b><center>" . $kClean . "</center></b><BR>" . $st1 . $v . $st3 . $vc . $ed1 . "</tr>";
          $counter=0;
        }
      } // end foreach
      $counter=0;
      echo "</table>";
      echo '</div><div class="modal-footer"><button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button></div></div></div></div>&nbsp';
    }  // end foreach
    echo "<br><br>";
  }  // end if
  if ( ! empty ($cleanNrpe)) {
    echo "NRPE<br>";
    foreach ($cleanNrpe as $key => $value) {
      echo '<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#' . $key . '">View ' .$key . '</button>';
      echo '<div class="modal" id="' . $key .'"> <div class="modal-dialog  modal-xl modal-dialog-centered"> <div class="modal-content"><div class="modal-header"><h4 class="modal-title">NRPE Check: ' . $key . '</h4> <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div> <div class="modal-body">';
      echo "<table>";
      $counter=0;

      foreach ($value as $k => $v) {
        $vc=preg_replace('/586/', '250', $v);
        $vc=preg_replace('/308/','150', $vc);
        $v=preg_replace('/586/', '1586', $v);
        $v=preg_replace('/308/','800', $v);
        if ( $counter == 0 ) {
         echo "<tr><td><b><center>" . $k ."</center></b><br>" . $st1 . $v . $st3 . $vc . $ed1;
         $counter++;
        }
        elseif ( $counter <=3 ) {
          echo "<td><b><center>" . $k ."</center></b><BR>". $st1 . $v . $st3 . $vc . $ed1;
          $counter++;
        }
        elseif( $counter >= 4 ) {
          echo "<td><b><center>" . $k ."</center></b><BR>" . $st1 . $v . $st3 . $vc . $ed1 . "</tr>";
          $counter=0;
        }
      } // end foreach
      $counter=0;
      echo "</table><br><br>";
      echo '</div><div class="modal-footer"><button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button></div></div></div></div>&nbsp';
    }  // end foreach
    echo "<br><br>";
  }  // end if
  if ( ! empty($cleanSensors)) {
    echo "Sensors<br>";
    foreach ($cleanSensors as $key => $value) {
      echo '<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#' . $key . '">View ' .$key . '</button>';
      echo '<div class="modal" id="' . $key .'"> <div class="modal-dialog  modal-xl modal-dialog-centered"> <div class="modal-content"><div class="modal-header"><h4 class="modal-title">Sensors: ' . $key . '</h4> <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div> <div class="modal-body">';
      echo "<table>";
      $counter=0;
      foreach ($value as $k => $v) {
        $vc=preg_replace('/586/', '250', $v);
        $vc=preg_replace('/308/','150', $vc);
        $v=preg_replace('/586/', '1586', $v);
        $v=preg_replace('/308/','800', $v);
        // counterFoo
        if ( $counter == 0 ) {
         echo "<tr><td><b><center>" . $k ."</center></b><br>" . $st1 . $v . $st3 . $vc . $ed1;
         $counter++;
        }
        elseif ( $counter <= 2 ) {
          echo "<td><b><center>" . $k ."</center></b><BR>". $st1 . $v . $st3 . $vc . $ed1;
          $counter++;
        }
        elseif( $counter >= 3 ) {
          echo "<td><b><center>" . $k ."</center></b><BR>" . $st1 . $v . $st3 . $vc . $ed1 . "</tr>";
          $counter=0;
        }
      } // end foreach
      $counter=0;
      echo "</table><br><br>";
      echo '</div><div class="modal-footer"><button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button></div></div></div></div>&nbsp';
    }  // end foreach
    echo "<br><br>";
  }  // end if

  if ( ! empty ($cleanLoad)) {
    echo "Load<br>";
    foreach ($cleanLoad as $key => $value) {
      // Load averages
      echo '<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#' . $key . '">View ' .$key . '</button>';
      echo '<div class="modal" id="' . $key .'"> <div class="modal-dialog  modal-xl modal-dialog-centered"> <div class="modal-content"><div class="modal-header"><h4 class="modal-title">Load Averages: ' . $key . '</h4> <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div> <div class="modal-body">';
      echo "<table>";
      $counter=0;
      foreach ($value as $k => $v) {
        $vc=preg_replace('/586/', '250', $v);
        $vc=preg_replace('/308/','150', $vc);
        $v=preg_replace('/586/', '1586', $v);
        $v=preg_replace('/308/','800', $v);
        // counterFoo
        if ( $counter == 0 ) {
         echo "<tr><td><b><center>" . $k ."</center></b><br>" . $st1 . $v . $st3 . $vc . $ed1;
         $counter++;
        }
        elseif ( $counter <=3 ) {
          echo "<td><b><center>" . $k ."</center></b><BR>". $st1 . $v . $st3 . $vc . $ed1;
          $counter++;
        }
        elseif( $counter >= 4 ) {
          echo "<td><b><center>" . $k ."</center></b><BR>" . $st1 . $v . $st3 . $vc . $ed1 . "</tr>";
          $counter=0;
        }
      } // end foreach
      $counter=0;
      echo "</table><br><br>";
      echo '</div><div class="modal-footer"><button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button></div></div></div></div>&nbsp';
    }  // end foreach
    echo "<br><br>";
  }  // end if
} // end else
?>
  </div>
  </div>
  </div>
  </main>
  </div>

  <script src="/js/font-awesome/all.min.js" crossorigin="anonymous"></script>
  <script src="/js/bootstrap-5/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script src="/js/sb-demo/js/scripts.js"></script>
